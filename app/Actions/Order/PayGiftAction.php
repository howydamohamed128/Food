<?php

namespace App\Actions\Order;

use App\Actions\User\CalculateBalanceThatDeductedFromWalletAction;
use App\Actions\User\WithdrawBalanceAction;
use App\Models\Gift;
use App\Models\Order;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;


class PayGiftAction {
    use AsAction;
    public function handle($amount,Gift $gift) {
        $myfatoorahApiV2 = app(MyFatoorahPayment::class);

        $payment_data = $myfatoorahApiV2->getInvoiceURL([
            'CustomerName' => $gift->sender->name,
            'InvoiceValue' => $amount,
            'DisplayCurrencyIso' => 'SAR',
            'CustomerEmail' => $gift->sender->email,
            'CallBackUrl' => route('gifts.webhooks.myfatoorah.callback'),
            'ErrorUrl' => route('gifts.webhooks.myfatoorah.callback'),
            'MobileCountryCode' => '+965',
            'CustomerMobile' => $gift->sender->phone,
            'Language' => app()->getLocale(),
            'CustomerReference' => $gift->id,
            'SourceInfo' => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package ' . MYFATOORAH_LARAVEL_PACKAGE_VERSION
        ]);
        $gift->update(['payment_data' => [...$payment_data, 'gateway' => 'myfatoorah']]);



    }


}
