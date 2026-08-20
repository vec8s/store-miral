<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled(Fortify::username())) {
            $this->merge([
                Fortify::username() => trim($this->input(Fortify::username())),
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
            Fortify::username() => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }
}
