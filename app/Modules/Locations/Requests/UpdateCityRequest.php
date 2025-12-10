<?php

namespace App\Modules\Locations\Requests;

use App\Modules\Locations\Models\City;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCityRequest extends FormRequest
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
        /** @var City|null $city */
        $city = $this->route('city');
        $cityId = $city?->getKey();

        return [
            'name' => ['sometimes', 'required', 'string', 'max:120', 'unique:cities,name,' . $cityId],
        ];
    }
}
