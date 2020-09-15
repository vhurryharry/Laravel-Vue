<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => ['required', 'string', 'max:65'],
            'event_type' => ['required', 'string', 'max:255'],
            'start_date' => ['required'],
            'starts_at' => ['required'],
            'event_privacy' => ['required', 'string'],
            'body' => ['required', 'string'],
            'timezone' => ['required'],
        ];

        if ($this->attributes->get('event_type') == 'one-time') {
            $rules['end_date'] = 'required';
            $rules['ends_at'] = 'required';
        }

        return $rules;
    }
}
