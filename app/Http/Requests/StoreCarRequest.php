<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'=> ['required', 'string'],
            'description'=> ['required', 'string'],
            'number_plate' => ['required', 'string', 'unique:cars,number_plate'],
            'horsepower' => ['required', 'integer'],
            'mileage' => ['required', 'integer'],
            'price' => ['required', 'integer'],
            'image' => ['image', 'mimes:jpeg,png', 'max:2048']
        ];
    }
}
