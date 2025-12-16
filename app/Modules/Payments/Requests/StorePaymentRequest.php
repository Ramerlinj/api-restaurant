<?php

namespace App\Modules\Payments\Requests;

use App\Modules\Payments\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['nullable', 'string', Rule::in(Payment::AVAILABLE_STATUSES)],
        ];
    }
}
