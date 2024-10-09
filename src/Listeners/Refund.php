<?php

namespace Webkul\Papara\Listeners;

use Papara\Model\Refund as RefundModel;
use Papara\Request\CreateRefundRequest;
use Webkul\Admin\Listeners\Base;
use Webkul\Admin\Mail\Order\RefundedNotification;
use Webkul\Papara\Helpers\PaparaApi;

class Refund extends Base
{
    /**
     * After order is created
     */
    public function afterCreated(\Webkul\Sales\Contracts\Refund $refund): void
    {
        $this->refundOrder($refund);

        try {
            if (! core()->getConfigData('emails.general.notifications.emails.general.notifications.new_refund')) {
                return;
            }

            $this->prepareMail($refund, new RefundedNotification($refund));
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * After Refund is created
     */
    public function refundOrder(\Webkul\Sales\Contracts\Refund $refund): void
    {
        $order = $refund->order;

        if ($order->payment->method === 'papara') {

            $request = new CreateRefundRequest();
            $request->setLocale(app()->getLocale());
            $request->setConversationId($refund['id']);
            $request->setPaymentTransactionId($order->payment['additional']);

            $request->setPrice(number_format($refund['base_grand_total'], '2', '.', ''));
            $request->setCurrency($refund['order_currency_code']);

            $refund = RefundModel::create($request, PaparaApi::options());

            if ($refund->getStatus() === 'success') {
                //
            } else {
                $errorMessage = $refund->getErrorMessage();
            }
        }
    }
}
