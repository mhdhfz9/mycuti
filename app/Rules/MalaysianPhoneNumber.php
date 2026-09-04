<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class MalaysianPhoneNumber implements ValidationRule
{
    /**
     * Matches Malaysian mobile numbers (01X-XXXXXXX / 01X-XXXXXXXX) and
     * landline numbers (0X-XXXXXXX / 0X-XXXXXXXX), with an optional +60/60
     * country code, after spaces and dashes are stripped for comparison.
     */
    private const PATTERN = '/^(\+?60|0)(1[0-46-9][0-9]{7,8}|[3-9][0-9]{7,9})$/';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalized = preg_replace('/[\s-]/', '', (string) $value);

        if (! preg_match(self::PATTERN, (string) $normalized)) {
            $fail(__('Sila masukkan nombor telefon Malaysia yang sah (contoh: 012-3456789).'));
        }
    }
}
