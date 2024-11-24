<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'configs' => ['required', 'array'],
            // Default
            'configs.default' => ['required', 'array'],
            'configs.default.file' => ['required', 'string'],
            'configs.default.tags' => ['nullable', 'array'],
            'configs.default.tags.*.id' => ['exists:tags,id'],
            // Files
            'configs.files' => ['nullable', 'array'],
            'configs.files.*.file' => ['string'],
            'configs.files.*.tags' => ['nullable', 'array'],
            'configs.files.*.tags.*.id' => ['exists:tags,id'],
        ];
    }
}
