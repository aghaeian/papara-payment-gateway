<?php

namespace Webkul\Papara\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Papara\Model\Address;
use Papara\Model\BasketItem;
use Papara\Model\BasketItemType;
use Papara\Model\Buyer;
use Papara\Model\CheckoutForm;
use Papara\Model\CheckoutFormInitialize;
use Papara\Model\PaymentGroup;
use Papara\Request\CreateCheckoutFormInitializeRequest;
use Papara\Request\RetrieveCheckoutFormRequest;
use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Models\Customer;
use Webkul\Papara\Helpers\Ipn;
use Webkul\Papara\Helpers\PaparaApi;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
        protected Ipn $ipnHelper
    ) {
        //
    }

    /**
     * Redirects to the Papara server.
     *
     * \Illuminate\Contracts\View\View
     * \Illuminate\Foundation\Application
     * \Illuminate\Contracts\View\Factory
     * \Illuminate\Contracts\Foundation\Application
     */
    public function redirect(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $cart = Cart::getCart();
        $address = $cart->billing_address;
        $user = Customer::find($cart->customer_id);

        $requestPapara = new CreateCheckoutFormInitializeRequest();
        $requestPapara->setLocale(app()->getLocale());
        $requestPapara->setConversationId(rand());
        $requestPapara->setPrice(number_format($cart['base_sub_total'], '2', '.', ''));
        $requestPapara->setPaidPrice(number_format($cart['base_grand_total'], '2', '.', ''));
        $requestPapara->setCurrency($cart['cart_currency_code']);
        $requestPapara->setBasketId($cart['id']);
        $requestPapara->setPaymentGroup(PaymentGroup::PRODUCT);
        $requestPapara->setCallbackUrl(route('papara.callback'));
        $requestPapara->setEnabledInstallments([2, 3, 6, 9]);

        $buyer = new Buyer();
        $buyer->setId($cart['id']);
        $buyer->setName($cart['customer_first_name']);
        $buyer->setSurname($cart['customer_last_name']);
        $buyer->setGsmNumber($address['phone']);
        $buyer->setEmail($address['email']);
        $buyer->setIdentityNumber(rand());
        $buyer->setLastLoginDate((string) $cart['created_at']);
        $buyer->setRegistrationDate((string) $user['created_at']);
        $buyer->setRegistrationAddress($address['address']);
        $buyer->setIp($request->ip());
        $buyer->setCity($address['city']);
        $buyer->setCountry($address['country']);
        $buyer->setZipCode($address['postcode']);

        $requestPapara->setBuyer($buyer);
        $shippingAddress = new Address();
        $shippingAddress->setContactName($cart['customer_first_name'].' '.$cart['customer_last_name']);
        $shippingAddress->setCity($address['city']);
        $shippingAddress->setCountry($address['country']);
        $shippingAddress->setAddress($address['address']);
        $shippingAddress->setZipCode($address['postcode']);
        $requestPapara->setShippingAddress($shippingAddress);

        $billingAddress = new Address();
        $billingAddress->setContactName($cart->customer_first_name.' '.$cart->customer_last_name);
        $billingAddress->setCity($address['city']);
        $billingAddress->setCountry($address['country']);
        $billingAddress->setAddress($address['address']);
        $billingAddress->setZipCode($address['postcode']);
        $requestPapara->setBillingAddress($billingAddress);

        $basketItems = [];
        $products = 0;
        foreach ($cart['items'] as $product) {
            $BasketItem = new BasketItem();
            $BasketItem->setId($product['id']);
            $BasketItem->setName($product['name']);
            $BasketItem->setCategory1($product->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS');
            $BasketItem->setCategory2($product->getTypeInstance()->isStockable() ? 'PHYSICAL_GOODS' : 'DIGITAL_GOODS');
            $BasketItem->setItemType(BasketItemType::PHYSICAL);
            $BasketItem->setPrice(number_format($product['total'], '2', '.', ''));
            $basketItems[$products] = $BasketItem;
            $products++;
        }
        $requestPapara->setBasketItems($basketItems);

        $checkoutFormInitialize = CheckoutFormInitialize::create($requestPapara, PaparaApi::options());
        $paymentForm = $checkoutFormInitialize->getCheckoutFormContent();
        $paymentPageUrl = $checkoutFormInitialize->getPaymentPageUrl().'&iframe=true';
        $checkoutFormInitialize->setPaymentPageUrl($paymentPageUrl);

        return view('papara::papara-form', compact('paymentForm'));
    }

    /**
     * Redirects to the Papara server.
     */
    public function callback(Request $request): RedirectResponse
    {
        $requestPapara = new RetrieveCheckoutFormRequest();
        $requestPapara->setLocale(app()->getLocale());
        $requestPapara->setToken($request->token);
        $checkoutForm = CheckoutForm::retrieve($requestPapara, PaparaApi::options());

        if ($checkoutForm->getPaymentStatus() == 'SUCCESS') {
            $paymentTransactionId = $checkoutForm->getPaymentItems()[0]->getPaymentTransactionId();

            if (! is_null($paymentTransactionId)) {
                session(['payment_transaction_id' => $paymentTransactionId]);
            }

            return redirect()->route('papara.success');
        } else {
            return redirect('/checkout/onepage');
        }
    }

    /**
     * Place an order and redirect to the success page.
     *
     * @throws \Exception
     */
    public function success(): RedirectResponse
    {
        $cart = Cart::getCart();

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        $this->savePaymentTransactionId($order['id']);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
    /**
     * Redirect to the cart page with error message.
     */
    public function failure(): RedirectResponse
    {
        session()->flash('error', 'Papara payment was either cancelled or the transaction failed.');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Prepares order's invoice data for creation.
     */
    protected function prepareInvoiceData($order): array
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice'  => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * Saves the payment transaction ID to the database.
     */
    protected function savePaymentTransactionId(int $orderId): void
    {
        OrderPayment::where('order_id', $orderId)->update(['additional' => session('payment_transaction_id')]);
    }
}
