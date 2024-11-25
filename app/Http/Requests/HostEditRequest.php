<?php

namespace App\Http\Requests;

use App\Rules\VirtualHostSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HostEditRequest extends FormRequest
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
        $hostId = $this->route('host');

        $rules = [
            'domain' => ['required', 'string'],
            'created_at' => ['nullable', 'date'],
            'config' => ['required', 'array', new VirtualHostSection],

            'config.*._addr_port' => ['nullable', 'string'],
        ];

        if (! empty($hostId)) {
            $rules[] = Rule::unique('hosts', 'domain')->ignore($hostId);
        } else {
            $rules[] = Rule::unique('hosts', 'domain');
        }

        return $rules;
    }
}
