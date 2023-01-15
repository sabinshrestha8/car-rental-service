<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
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
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'number_plate' => 'required|max:255',
            'horse_power' => 'required|Integer|max:255',
            'milage' => 'required|integer|max:255',
            'image' => 'required|string'
        ];
    }
}
