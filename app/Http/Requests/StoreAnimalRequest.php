<?php

namespace App\Http\Requests;

use App\Enums\AnimalGender;
use App\Models\Species;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnimalRequest extends FormRequest
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
            'description' => ['required'],
            'birthdate' => ['required', 'date'],
            'species_id' => ['required', Rule::exists(Species::class, 'id')],
            'gender' => ['required', Rule::enum(AnimalGender::class)],
        ];
    }
}
