<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'published_year' => 'required|integer|min:1000|max:' . (date('Y') + 1),
            'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:0',
            'cover_image' => 'nullable|image|max:2048',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ];

        // Add ISBN validation with unique check for updates
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['isbn'] = 'sometimes|required|string|max:20|unique:books,isbn,' . $this->route('book');
        } else {
            $rules['isbn'] = 'required|string|max:20|unique:books';
        }

        return $rules;
    }
}