<?php

namespace App\Modules\Auth\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->user()?->getKey();

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'surname' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'string', 'email:strict,rfc', 'max:150', 'unique:users,email,' . $userId],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'required', 'string', Rule::in(User::AVAILABLE_ROLES)],
        ];
    }
}
