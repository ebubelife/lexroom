<?php

namespace App\Rules;

use App\Helpers\PhoneHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NigerianPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = PhoneHelper::validateAndNormalize($value);
        
        if (!$normalized) {
            $fail('The :attribute must be a valid Nigerian phone number (e.g., 08012345678, +2348012345678, or 2348012345678).');
        }
    }
}
