<?php

namespace App\Actions\Payments\MyFatoorah;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Order;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\MyfatoorahApiV2;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class CreateMyFatoorahInvoiceAction {
    use AsAction;

    public function handle($transactionable, User $user, $total) {
        $myfatoorahApiV2 = app(MyFatoorahPayment::class);
        $transaction = $transactionable->transaction()->create([
            'user_id' => $user->id,
            'price' => $total,
            'meta_data' => ['gateway' => 'myfatoorah']
        ]);
        if ($total <= 0) {
            $transaction->update(['status' => 'paid']);
            return;
        }
        $payment_data = $myfatoorahApiV2->getInvoiceURL([
            'CustomerName' => $user->name,
            'InvoiceValue' => $total,
            'DisplayCurrencyIso' => 'SAR',
            'CustomerEmail' => $user->email,
            'CallBackUrl' => route('webhooks.myfatoorah.callback'),
            'ErrorUrl' => route('webhooks.myfatoorah.callback'),
            'MobileCountryCode' => '+965',
            'CustomerMobile' => $user->phone,
            'Language' => app()->getLocale(),
            'CustomerReference' => $transactionable->id,
            'SourceInfo' => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package ' . MYFATOORAH_LARAVEL_PACKAGE_VERSION
        ]);
        $transaction->update(['meta_data' => [...$payment_data, 'gateway' => 'myfatoorah']]);
    }

}
