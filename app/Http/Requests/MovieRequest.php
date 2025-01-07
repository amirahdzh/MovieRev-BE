<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
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
            'title' => 'required|max:255',
            'summary' => 'required',
            'year' => 'required',
            'poster' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'genre_id' => 'required|exists:genres,id'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'inputan title tidak boleh kosong',
            'summary.required' => 'inputan summary tidak boleh kosong',
            'year.required' => 'inputan year tidak boleh kosong',
            'poster.mimes' => 'format poster hanya boleh jpg, jpeg, png',
            'genre_id.required' => 'genre_id tidak boleh kosong',
            'genre_id.exist' => 'id genre tidak ditemukan di data genre',
            'title.max' => 'inputan title maksimal 255',
        ];
    }
}
