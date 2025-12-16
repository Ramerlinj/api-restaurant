<?php

namespace App\Modules\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'address_id' => ['nullable', 'integer', 'exists:addresses,id', 'required_without:address'],
            'address' => ['nullable', 'array', 'required_without:address_id'],
            'address.address_line' => ['required_without:address_id', 'string', 'max:200'],
            'address.city_id' => ['required_without:address_id', 'integer', 'exists:cities,id'],
            'address.sector' => ['nullable', 'string', 'max:100'],
            'address.reference' => ['nullable', 'string', 'max:200'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.pizza_id' => ['required', 'integer', 'exists:pizzas,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
