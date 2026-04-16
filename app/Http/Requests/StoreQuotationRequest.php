<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required', 'exists:leads,id'],
            'tanggal_penawaran' => ['required', 'date'],
            'nomor_penawaran' => ['nullable', 'string'],
            'nilai_penawaran' => ['required', 'numeric'],
            'hpp' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:berjalan,nego,deal,batal'],
            'keterangan' => ['nullable', 'string'],
        ];
    }
}
