<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShelterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'address_line_1' => ['required', 'max:255'],
            'address_line_2' => [],
            'postcode' => ['required', 'max:50'],
            'city' => ['required', 'max:255'],
            'phone_number' => ['required', 'max:20'],
        ];
    }
}
