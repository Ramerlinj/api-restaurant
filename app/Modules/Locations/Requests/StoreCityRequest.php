<?php

namespace App\Modules\Locations\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
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
            'id' => ['required', 'integer', 'min:1', 'unique:cities,id'],
            'name' => ['required', 'string', 'max:120', 'unique:cities,name'],
        ];
    }
}
