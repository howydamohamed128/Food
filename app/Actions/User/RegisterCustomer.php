<?php

namespace App\Actions\User;

use App\Actions\Authentication\RemoveVerficationCodes;
use App\Actions\Authentication\UpdateUserToken;
use App\Models\Customer;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use Notification;

class RegisterCustomer
{
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle($first_name, $last_name, $phone, $title, $location,$email = null, $national_code=null, $avatar = null)
    {
        $customer = Customer::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'title' => $title,
            'location' => $location,
            'national_code' => $national_code,
            'password' => $phone,
        ]);
        $customer->addresses()->create([
            'location' => $location,
            'title' => $title,
            'national_code' => $national_code,
            'primary' => 1
        ]);
        UpdateUserToken::run($customer);
        RemoveVerficationCodes::run($customer);
        if ($avatar) {
            $customer->addMedia($avatar)->toMediaCollection();
        }
        return $customer;
    }
}
