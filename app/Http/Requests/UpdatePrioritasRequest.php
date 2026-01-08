<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrioritasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('prioritas');

        return [
            'nama_prioritas' => 'required|string|max:255|unique:prioritas,nama_prioritas,' . $id . ',id_prioritas',
            'color' => 'nullable|string|max:50',
        ];
    }
}
