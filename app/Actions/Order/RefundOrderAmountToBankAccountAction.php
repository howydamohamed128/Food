<?php

namespace App\Actions\Order;

use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;

use MyFatoorah\Library\API\MyFatoorahRefund;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;

class RefundOrderAmountToBankAccountAction {
    use AsAction;

    public function handle(Order $order, $percentage, $amount) {
        $refundAmount = $amount ;

        $myfatoorahApiV2 = app(MyFatoorahRefund::class);

        return $myfatoorahApiV2->refund(
            keyId: $order->transaction->meta_data['invoiceId'] ?? 0,
            amount: $refundAmount,
            comment: 'Refund',
            keyType: 'InvoiceId',
            currency: "SAR"

        );
    }

}
