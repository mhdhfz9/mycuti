<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user IC numbers.
     *
     * IC numbers are stored encrypted, so a plain `Rule::unique` can't
     * compare the raw input against the ciphertext column — uniqueness is
     * checked against the `ic_number_hash` blind index instead.
     *
     * @return array<int, ValidationRule|array<mixed>|string|\Closure>
     */
    protected function icNumberRules(?int $userId = null): array
    {
        return [
            'required',
            'digits:12',
            function (string $attribute, mixed $value, \Closure $fail) use ($userId): void {
                $query = User::where('ic_number_hash', User::hashIcNumber((string) $value));

                if ($userId !== null) {
                    $query->whereKeyNot($userId);
                }

                if ($query->exists()) {
                    $fail(__('This IC number has already been taken.'));
                }
            },
        ];
    }
}
