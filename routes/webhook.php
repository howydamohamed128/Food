<?php

use App\Actions\Order\GIftPaidAction;
use App\Actions\Payments\Tabby\CheckTabbyPaymentStatusAction;
use App\Actions\Payments\Tamara\CheckTamaraPaymentStatusAction;
use App\Lib\Utils;
use App\Models\Customer;
use App\Models\Gift;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewGiftSentNotification;
use App\Notifications\OrderPaidNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;


// Route::get('webhooks/myfatoorah/callback', function (MyFatoorahPaymentStatus $myfatoorahApiV2) {
//     $response = $myfatoorahApiV2->getPaymentStatus(request()->get('Id'), 'PaymentId');
//     $transaction = \App\Models\Transaction::where('meta_data->invoiceId', $response->InvoiceId)->first();

//     if ($response->InvoiceStatus == 'Paid' && $transaction->status !== \App\Enum\OrderPaymentStatus::PAID) {
//         $transaction->update(['status' => \App\Enum\OrderPaymentStatus::PAID]);
//         if ($transaction->transactionable instanceof Order) {
//             try {

//                 Notification::send([$transaction?->transactionable?->customer, ...Utils::getAdministrationUsers()], new OrderPaidNotification($transaction?->transactionable));
//                 Mail::to($transaction->transactionable?->customer?->email)->send(new \App\Mail\OrderInvoiceMail($transaction->transactionable));
//             } catch (Exception $exception) {
//                 Log::error($exception->getMessage());
//             }
//         }
//         if ($transaction->transactionable instanceof \App\Models\Gift) {
//             GIftPaidAction::run($transaction);
//         }

//         return Api::isOk('Payment is done successfully');
//     }
//     return Api::isError('something went wrong');
// })->name('webhooks.myfatoorah.callback');


// Route::any('webhooks/tabby/callback', fn() => CheckTabbyPaymentStatusAction::run())->name('webhooks.tabby.callback');
// Route::any('webhooks/tabby/success', fn() => 'success')->name('webhooks.tabby.success');
// Route::any('webhooks/tabby/cancel', fn() => CheckTabbyPaymentStatusAction::run())->name('webhooks.tabby.cancel');
// Route::any('webhooks/tabby/failure', fn() => CheckTabbyPaymentStatusAction::run())->name('webhooks.tabby.failure');

// Route::any('webhooks/tamara/callback', fn() => CheckTamaraPaymentStatusAction::run())->name('webhooks.tamara.callback');
// Route::any('webhooks/tamara/success', fn() => "success")->name('webhooks.tamara.success');
// Route::any('webhooks/tamara/cancel', fn() => "canceled")->name('webhooks.tamara.cancel');
// Route::any('webhooks/tamara/failure', fn() => "failed")->name('webhooks.tamara.failure');


// Route::get('gifts/webhooks/myfatoorah/callback', function (MyFatoorahPaymentStatus $myfatoorahApiV2) {
//     $response = $myfatoorahApiV2->getPaymentStatus(request()->get('Id'), 'PaymentId');
//     $transaction = Gift::where('payment_data->invoiceId', $response->InvoiceId)->first();
//     if ($response->InvoiceStatus == 'Paid' && $transaction->status !== \App\Enum\OrderPaymentStatus::PAID) {
//         $transaction->update(['status' => \App\Enum\OrderPaymentStatus::PAID]);
//         $transaction->update([
//             'payment_data' => array_merge($transaction->payment_data, [
//                 ...collect($response)->toArray(),
//                 'method' => $response->focusTransaction->PaymentGateway,
//                 'paid_at' => Carbon::now()->toDateTimeString()
//             ]),
//             'payment_status' => 'paid',
//         ]);
//         $customer = User::where('phone', $transaction->phone)->first();
//         if ($customer) {
//             $transaction->receiver->deposit($transaction->price, [
//                 'description' => 'Gift Received from ' . $transaction->sender->name,
//             ]);

//             $transaction->update([
//                 'receiver_id' => $customer->id,
//                 'received' => true,
//             ]);
//         }



//         return Api::isOk('Payment is done successfully');
//     }
//     return Api::isError('something went wrong');
// })->name('gifts.webhooks.myfatoorah.callback');
