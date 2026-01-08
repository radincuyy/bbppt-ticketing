<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrioritasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_prioritas' => 'required|string|max:255|unique:prioritas,nama_prioritas',
            'color' => 'nullable|string|max:50',
        ];
    }
}
