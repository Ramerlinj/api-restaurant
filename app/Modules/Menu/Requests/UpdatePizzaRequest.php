<?php

namespace App\Modules\Menu\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePizzaRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'is_recommended' => ['sometimes', 'boolean'],
            'image' => ['sometimes', 'image', 'max:5120'],
        ];
    }
}
