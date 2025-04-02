<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];

        // Add unique check for updates
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = 'required|string|max:255|unique:categories,name,' . $this->route('category');
        } else {
            $rules['name'] = 'required|string|max:255|unique:categories';
        }

        return $rules;
    }
}