<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings {
    public string|null $app_logo;
    public string|null $fav_icon;
    public string $app_name;
    public string $app_email;
    public string $app_phone;
    public string $app_mobile;
    public string $app_whatsapp;
    public string $app_address;

    // public float $taxes;
    // public bool $visitor_mode;
    // public bool $maintenance_mode;
    // public array $refund_rules = [];

    // public array $applications_links = [];
    // public array $app_versions = [];
    // public array $app_pages = [];

    public array $social_links = [];
    // public array $working_days = [];
    // public array $bank_account = [];
    // public array $app_details = [];

    public static function group(): string {
        return 'general';
    }
}