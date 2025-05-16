<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'password' => [
        'mixed' => "The :attribute must contain at least one uppercase and one lowercase letter.",
        'symbols' => "The :attribute must contain at least one symbol.",
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field is required.',
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',
    'at_lest_one_day' => 'At least one day must be selected.',
    'boundaries_required' => 'Boundaries must be drawn on the map.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'now' => 'Now'
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'full_name' => 'Name',
        'name' => 'Name',
        'value' => 'Value',
        'username' => 'Username',
        'email' => 'Email',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'city' => 'City',
        'country' => 'Country',
        'address' => 'Address',
        'phone' => 'Phone',
        'mobile' => 'Mobile',
        'age' => 'Age',
        'sex' => 'Gender',
        'gender' => 'Gender',
        'day' => 'Day',
        'month' => 'Month',
        'year' => 'Year',
        'hour' => 'Hour',
        'minute' => 'Minute',
        'second' => 'Second',
        'content' => 'Content',
        'description' => 'Description',
        'excerpt' => 'Excerpt',
        'date' => 'Date',
        'time' => 'Time',
        'available' => 'Available',
        'size' => 'Size',
        'price' => 'Price',
        'desc' => 'Description',
        'title' => 'Title',
        'q' => 'Search',
        'link' => 'Link',
        'slug' => 'Slug',
        'city_id' => 'City',
        'device_token' => 'Device Token',
        "code" => "Code",
        "zone_id" => "Zone",
        'state' => 'District',
        'rate' => 'Rating',
        "old_password" => "Old Password",
        'coordinate' => 'Location',
        'receipt_method' => 'Receipt Method',
        'products' => 'Services',
        'payment_gateway' => 'Payment Method',
        'address_id' => 'Address',
        'id' => 'ID',
        'status' => 'Status',
        'commercial_registration_no' => 'Commercial Registration Number',
        'commercial_registration_no_image' => 'Commercial Registration Image',
        'type' => 'Type',
        'message' => 'Message',
        'contact_type_id' => 'Message Type',
        'guest' => 'Guest',
        'user_id' => 'User',
        'branch_id' => 'Branch',
        'period' => 'Preferred Delivery Time',
        'delivery_method' => 'Delivery Method',
        'institution_activity' => 'Institution Activity',
        'notes' => 'Notes',
        'coupon_code' => 'Discount Code',
        'bank_transfer_image'=>'Bank Transfer Image',
        'address_name'=>'Address Name',
        'district_id'=>'District',
        'primary'=>'Address Type',
        'national_code' => 'National Address Short Code',
        'location.lat'=>'Latitude',
        'location.lng'=>'Longitude',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        "is_approved_conditions" => "Terms and Conditions Approval",
        'amount' => 'Gift Amount',
        'options' => 'Add-ons',
        'option_id' => 'Add-on',
    ],
    'values' => [
        'due_date' => [
            'now' => 'Now'
        ],
        'start_date' => [
            'today' => 'Now'
        ],
        'end_date' => [
            'today' => 'Now'
        ]
    ],
    'api' => [
        'order_amount_too_high'=>'Order total exceeds maximum allowed limit',
        'product' => [
            'not_exists' => 'Product #:index does not exist',
            'not_available' => ":title is not available for booking",
            "time_up" => ':title is available from :from to :to',
            "option_not_exists" => "Option #:index is not available with :title",
            'option_unavailable' => "Option :option is currently unavailable",
            "option_not_available" => ":title is currently unavailable",
            "value_not_exists" => "Value :title is invalid",
            "value_not_available" => ":title is currently unavailable",
            "option_required" => ":title is required",
        ],
        "branch_not_available" => "The branch is currently not accepting new orders",
        "address_out_side_current_branch" => 'Selected address is outside the delivery zone of current branch',
        "branch_in_maintenance_mode" => 'The branch is currently under maintenance, please try again later',
        'invalid_status' => 'Invalid status',
        'invalid_phone_format' => 'Invalid phone number format',
        "invalid_credentials" => "Invalid login credentials",
        'invalid_verification_code' => 'Invalid or expired verification code',
        "order_not_delivered_yet" => "Order hasn't been delivered yet",
        'order_already_canceled' => "Order was already canceled",
        'order_already_rated' => 'Order was already rated',
        'order_already_reported' => 'Order was already reported',
        "invalid_address" => "Invalid address",
        'account_suspend' => 'Account is currently suspended',
        'current_branch_cant_provide_takeaway_method' => 'Current branch does not provide pickup service',
        "coupon_code_is_expired" => 'Discount code is invalid or expired',
        "coupon_code_exceeds_the_number_of_usages_times" => 'Discount code exceeded usage limit',
        "coupon_code_already_used" => 'Discount code was already used',
        'coupon_code_not_found'=> 'Invalid discount code',
    ],
];
