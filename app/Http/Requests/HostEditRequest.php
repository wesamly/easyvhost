<?php

namespace App\Http\Requests;

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
            'domain' => ['string', 'required'],
            'created_at' => ['nullable', 'date'],
        ];

        if (!empty($hostId)) {
            $rules[] = Rule::unique('hosts', 'domain')->ignore($hostId);
        } else {
            $rules[] = Rule::unique('hosts', 'domain');
        }

        return $rules;
    }
}
