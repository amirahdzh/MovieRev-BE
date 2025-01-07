<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CastMovieRequest extends FormRequest
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
            'movie_id' => 'required|exists:movies,id',
            'cast_id' => 'required|exists:casts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'movie_id.required' => 'movie tidak boleh kosong!',
            'cast_id.required' => 'cast tidak boleh kosong!',
        ];
    }
}
