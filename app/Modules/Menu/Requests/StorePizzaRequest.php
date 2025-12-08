<?php

namespace App\Modules\Menu\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePizzaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminPrivileges() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_recommended' => ['sometimes', 'boolean'],
            'image' => ['required', 'image', 'max:5120'],
        ];
    }
}
