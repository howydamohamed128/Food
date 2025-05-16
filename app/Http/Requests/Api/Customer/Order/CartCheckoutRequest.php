<?php

namespace App\Http\Requests\Api\Customer\Order;

use App\Enum\Catalog\OptionTypes;
use App\Enum\ServiceReservationTypesEnum;
use App\Models\Catalog\Product;
use App\Models\Catalog\Service;
use App\Models\Option;
use App\Rules\IsValidCouponOrder;
use App\Rules\WorkerServeServiceRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CartCheckoutRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    public function authorize() {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $rules = [
            'address_id' => 'required|exists:addresses,id',
            'type' => ['required', Rule::in(['service', 'plan'])],
            'service_id' => 'required|exists:products,id',
            'date' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'from' => ['required', 'date_format:H:i'],
            'to' => ['required', 'date_format:H:i', 'after:from'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:adds,id'],
            'options' => ['required', 'array'],
            'options.*.option_id' => ['required', 'exists:options,id'],
            'national_code' => ['nullable','string'],
            'options.*.value' => [
                function ($attribute, $value, $fail) {
                    $optionId = $this->input(str_replace('.value', '.option_id', $attribute));
                    $question = Option::find($optionId);
                    if (! $question) {
                        $fail(__('validation.exists', ['attribute' => __('validation.attributes.option_id')]));
                        return;
                    }
                    if ($question->required && (is_null($value) || $value === '')) {
                        $fail(__('validation.required', ['attribute' => $question->name]));
                        return;
                    }
                    switch ($question->type) {
                        case OptionTypes::TEXTAREA:
                            if (empty($value) && $question->required) {
                                $fail(__('validation.required', ['attribute' => $question->name]));
                            }
                            break;
                        case OptionTypes::DATE:
                            if (empty($value) && $question->required) {
                                $fail(__('validation.required', ['attribute' => $question->name]));
                            }
                            break;
                        case OptionTypes::RADIO:
                            if (empty($value) && $question->required) {
                                $fail(__('validation.required', ['attribute' => $question->name]));
                            }
                            if (! in_array($value, $question->values->pluck('id')->toArray())) {
                                $fail(__('validation.in', ['attribute' => $question->name]));
                            }
                            break;
                        case OptionTypes::CHECKBOX:
                            if (empty($value) && $question->required) {
                                $fail(__('validation.required', ['attribute' => $question->name]));
                            }
                            $validValues = $question->values->pluck('id')->toArray();
                            foreach ((array) $value as $val) {
                                if (! in_array($val, $validValues)) {
                                    $fail(__('validation.in', ['attribute' => $question->name]));

                                    return;
                                }
                            }
                            break;
                    }
                },
            ],
            'coupon_code' => ['nullable', 'string', new IsValidCouponOrder()],
            'notes' => ['nullable', 'string'],
        ];

        foreach ($this->input('options', []) as $index => $option) {
            $optionId = $option['option_id'] ?? null;
            $optionModel = Option::find($optionId);

            if ($optionModel && $optionModel->required && ! isset($option['value'])) {
                $rules["options.$index.value"] = ['required'];
            }
        }

        return $rules;
    }

    public function messages()
    {

        $messages = [

            'options.required' => __('validation.required', ['attribute' => __('validation.attributes.options')]),
            'options.*.option_id.required' => __('validation.required', ['attribute' => __('validation.attributes.option_id')]),
            'options.*.option_id.required_with' => __('validation.required', ['attribute' => __('validation.attributes.option_id')]),
            'options.*.option_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.option_id')]),

        ];


        $options = data_get($this->all(), 'options', []);
        if (! is_array($options)) {
            return $messages;
        }
        $optionIds = array_filter(array_column($options, 'option_id'), 'is_numeric');
        $optionModels = Option::whereIn('id', $optionIds)->get();

        foreach ($options as $index => $option) {
            if (is_array($option) && isset($option['option_id'])) {
                $optionModel = $optionModels->firstWhere('id', $option['option_id']);
                if ($optionModel) {
                    $messages["options.$index.value.required"] = __('validation.required', ['attribute' => $optionModel->name]);
                }
            }
        }

        return $messages;
    }


}
