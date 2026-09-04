<?php

namespace App\Concerns;

use App\Rules\MalaysianPhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;

trait ContactUsValidationRules
{
    /**
     * Get the validation rules used to validate a contact-us submission.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function contactUsRules(): array
    {
        return [
            'name' => $this->contactNameRules(),
            'phone' => $this->contactPhoneRules(),
            'email' => $this->contactEmailRules(),
            'message' => $this->contactMessageRules(),
        ];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function contactNameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function contactPhoneRules(): array
    {
        return ['required', 'string', 'max:20', new MalaysianPhoneNumber];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function contactEmailRules(): array
    {
        return ['required', 'string', 'email:rfc', 'max:255'];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function contactMessageRules(): array
    {
        return ['required', 'string', 'max:1000'];
    }
}
