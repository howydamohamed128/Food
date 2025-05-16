<?php

namespace App\Lib;

use App\Settings\DeveloperSetting;
use Illuminate\Support\Arr;
use App\Filament\Pages\Settings\ManageDeveloper;
use App\Models\Branch;
use App\Models\Manager;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Support\Str;

class Utils {
    static public function formatDistance($distance): string {
        $unit = trans('meter');
        if ($distance > 1000) {
            $distance = ($distance / 1000);
            $unit = trans("km");
        }
        $distance = round($distance, 2);
        return trans(":NUMBER :UNIT", ['NUMBER' => $distance, 'UNIT' => $unit]);
    }

    static public function getBranchFromRequestHeader(): string|null {
        return request()->header('X-Branch-ID');
    }

    static public function getBranchReceiptMethods() {
        return Branch::find(self::getBranchFromRequestHeader())->receipt_methods;
    }

    static public function getBranchLocation() {
        return Branch::where('id', self::getBranchFromRequestHeader())->first('location')->location;
    }

    static public function branchInHeavyLoadMode(): bool {
        return Branch::find(self::getBranchFromRequestHeader())->heavy_load_mode;
    }

    static public function getAdministrationUsers($ids = []) {
        return User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super_admin']))
            ->orWhereIn('id', $ids)
            ->where(fn($q) => $q->where('settings->notification_status', 1)->orWhereNull('settings->notification_status'))
            ->get();
    }


    public static function convertStringToArrayLanguage($text, $params = []): array {
        $arr = [];
        foreach (['ar', 'en'] as $lang) {
            $arr[$lang] = __($text, $params, $lang);
        }
        return $arr;
    }

    public static function randomOtpCode(): int {
        $settings = new DeveloperSetting();
        return $settings->otp_code_is_random ? rand(11111, 99999) : 12345;
    }

    public static function getTranslatedField($field = null): string|null {
        if (is_string($field)) {
            return $field;
        }
        if (is_object($field)) {
            $field = (array)$field;
        }

        $locale = $locale ?? app()->getLocale();
        $anotherLocale = $locale == 'ar' ? 'en' : 'ar';
        return Arr::get($field, $locale, Arr::get($field, $anotherLocale));

    }

    public static function generateImage($name): string {
//        $name =Str::of($name)->explode(' ')->map(fn($name) => mb_substr($name, 0, 1))
//            ->implode("+");

        return "https://ui-avatars.com/api/?name=$name&color=FFFFFF&background=030712";
    }
}
