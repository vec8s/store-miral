<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\SendPasswordResetLinkRequest as FortifySendPasswordResetLinkRequest;

class SendPasswordResetLinkRequest extends FortifySendPasswordResetLinkRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled(Fortify::email())) {
            $this->merge([
                Fortify::email() => trim($this->input(Fortify::email())),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            Fortify::email() => ['required', 'string', 'email', 'max:255'],
        ];
    }
}
