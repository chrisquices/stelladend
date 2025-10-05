<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currentPassword' => [
                'required', 'string', 'min:8', 'max:255',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user()->password)) {
                        $fail('The current password is incorrect.');
                    }
                }
            ],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ];
    }
}
