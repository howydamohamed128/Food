<?php

namespace App\Traits;

use App\Actions\Payments\CreateCashInvoiceAction;
use App\Actions\Payments\CreateWalletInvoiceAction;
use App\Actions\Payments\MyFatoorah\CreateMyFatoorahInvoiceAction;
use App\Actions\Payments\Tabby\CreateTabbyInvoiceAction;
use App\Actions\Payments\Tamara\CreateTamaraInvoiceAction;
use App\Enum\PaymentMethods;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

;

trait Transactionable {
    public function scopePaid($query) {
        return $query->where(fn($query) => $query->whereHas('transaction', fn($query) => $query->where('status', 'paid')
            ->orWhere('meta_data->gateway', 'cash')))
            ->orWhere(fn($query) => $query->whereDoesntHave('transaction'));
    }

    public function isPaid(): bool {
        return $this->transaction?->status->value == 'paid';
    }

    public function scopeUnPaid($query) {
        return $query->whereHas('transaction', fn($query) => $query->where('status', 'pending')->where('meta_data->gateway', 'paypal'));
    }


    public function transactions() {
        return $this->morphMany(Transaction::class, 'transactionable')->withoutGlobalScopes();
    }

    public function transaction() {
        return $this->morphOne(Transaction::class, 'transactionable')->withoutGlobalScopes();
    }


    public function pay($total, $user, $payment_method = 'myfatoorah') {
        return match ($payment_method) {
            'myfatoorah' => CreateMyFatoorahInvoiceAction::run($this, $user, $total),
            'wallet' => CreateWalletInvoiceAction::run($this, $user, $total),
            'cash' => CreateCashInvoiceAction::run($this, $user, $total),
        };
    }

}
