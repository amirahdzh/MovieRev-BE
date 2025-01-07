<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CastRequest extends FormRequest
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
            'name' => 'required',
            'age' => 'required',
            'bio' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'nama tidak boleh kosong',
            'age.required' => 'umur tidak boleh kosong',
            'bio.required' => 'bio tidak boleh kosong',
        ];
    }
}
