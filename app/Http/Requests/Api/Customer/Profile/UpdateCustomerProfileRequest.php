<?php

namespace App\Http\Requests\Api\Customer\Profile;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\KSAPhoneRule;

class UpdateCustomerProfileRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(['name' => $this->get('full_name')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            // 'phone' => [
                // 'nullable',
                // Rule::unique('users')->ignore(auth()->id())->whereNull('deleted_at'),
                // new KSAPhoneRule(),
            // ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore(auth()->id())->whereNull('deleted_at'),
            ],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }
    public function messages()
{
    return [
        'email.unique' => 'البريد الإلكتروني مستخدم بالفعل من قبل.',
    ];
}
}
