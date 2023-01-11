<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookCarRequest extends FormRequest
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
            'booked_from'=> ['required', 'date_format:Y-m-d H:i:s'],
            'booked_to'=> ['required', 'date_format:Y-m-d H:i:s', 'after:started_time'],
            'car_id' => ['required', 'integer'],
        ];
    }
}
