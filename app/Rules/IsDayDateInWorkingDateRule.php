<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Lib\Utils;
use App\Models\Branch;

class IsDayDateInWorkingDateRule implements ValidationRule {
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $branch =Branch::find(Utils::getBranchFromRequestHeader());
        if (!$branch->availableNow()) {
            $fail(__("validation.api.branch_not_available"));
        }
        if ($branch->maintenance_mode){
            $fail(__("validation.api.branch_in_maintenance_mode"));
        }

    }
}
