<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTiketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_status' => 'sometimes|exists:status,id_status',
            'id_kategori' => 'sometimes|exists:kategori,id_kategori',
            'id_prioritas' => 'sometimes|exists:prioritas,id_prioritas',
            'id_teknisi' => 'sometimes|nullable|exists:users,id',
        ];
    }
}
