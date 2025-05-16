<?php

namespace App\Http\Requests\Api\Customer;

use App\Enum\ServiceReservationTypesEnum;
use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\KSAPhoneRule;
use Illuminate\Validation\Rule;

class StoreGiftRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            // "service_id" => ["required", Rule::exists('products', 'id')->where('service_type', 'service')],
            // 'duration' => [Rule::requiredIf($this->isServiceTimed()), "integer", Rule::in([30, 60, 90, 120])],
            // "quantity" => ["required", "integer", "min:1"],
            'phone' => ["required", new KSAPhoneRule],
            'amount' => ['required','min:1']
        ];
    }

    public function service() {
        return Service::find($this->service_id);
    }

    public function receiver() {
        return User::where('phone', $this->phone)->first();
    }

    public function isServiceTimed(): bool {
        return Product::find($this->get('service_id'))->type == ServiceReservationTypesEnum::TIMED;
    }

    public function getPrice(): float {

        return $this->service()->getPrice($this->get('duration')) * $this->get('quantity');
    }
}