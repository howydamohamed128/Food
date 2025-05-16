<?php


namespace App\Actions\Order;

use App\Lib\Utils;
use App\Models\Order;
use App\Notifications\NewGiftSentNotification;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class GIftPaidAction {
    use AsAction;

    public function handle($transaction) {

        // // Notification::send(Utils::getAdministrationUsers(), new NewGiftSentNotification($transaction->transactionable));
        // // Notification::send($transaction->transactionable->receiver, new \App\Notifications\NewGiftReceivedNotification($transaction->transactionable));
        // // \App\Actions\User\DepositBalanceAction::run($transaction->transactionable->receiver, $transaction->transactionable->price, [
        //     'ar' => __("panel.messages.deposit_to_wallet_for_gift", ['amount' => $transaction->transactionable->price, 'gift' => $transaction->transactionable->id], 'ar'),
        //     'en' => __("panel.messages.deposit_to_wallet_for_gift", ['amount' => $transaction->transactionable->price, 'gift' => $transaction->transactionable->id], 'en'),
        // ]);

    }

}