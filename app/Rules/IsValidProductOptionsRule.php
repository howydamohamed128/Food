<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Str;
use App\Models\Catalog\Product;

class IsValidProductOptionsRule implements ValidationRule {
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $index = Str::betweenFirst($attribute, ".", ".");
        $id = request()->input(Str::replace("*", $index, 'products.*.id'));
        $product = Product::find($id);

        $option = $product->options()->where('id', $value);

        if ($notExist = !$option->exists()) {
            $fail(__("validation.api.product.option_not_exists", ['index' => $value, 'title' => $product->title]));
            return;
        }
        if (!$option?->first()?->status ) {

            $fail(__("validation.api.product.option_unavailable", ['option' => $option?->first()->option?->option?->name]));
        }

    }
}
