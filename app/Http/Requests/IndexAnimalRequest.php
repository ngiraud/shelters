<?php

namespace App\Http\Requests;

use App\Enums\AnimalGender;
use App\Models\Species;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAnimalRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'species' => ['nullable', Rule::exists(Species::class, 'id')],
            'gender' => ['nullable', Rule::enum(AnimalGender::class)],
        ];
    }
}
