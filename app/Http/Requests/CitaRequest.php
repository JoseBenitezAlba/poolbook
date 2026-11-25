<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CitaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start' => 'required|date_format:Y-m-d\TH:i:s.v\Z',
            'end' => 'required|date|after:start',
            'resourceId' => 'required|string',
            'extendedProps.day_of_week' => 'required|integer',
            'extendedProps.date' => 'required|date',
        ];
    }
}
