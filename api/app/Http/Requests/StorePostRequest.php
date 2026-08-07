<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'media' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,mp4,mov',
                'max:51200'
            ],

            'caption' => [
                'nullable',
                'string',
                'max:2200'
            ],
        ];
    }

    public function messages(): array {
        return [
            'media.required' => 'A mídia do post é pbrigatória',
            'media.file' => 'A mídia enviada não é um arquivo válido',
            'media.mimes' => 'A mídia deve ser uma imagem ou vídeo válido.',
            'media.max' => 'A mídia não pode ter mais de 50MB',
            'caption.max' => 'A legenda não pode ter mais de 2200 caracteres',
        ];
    }
}
