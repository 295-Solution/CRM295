<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'nomor_wa' => ['required', 'string', 'max:30'],
            'sumber_client' => ['required', Rule::in(Client::SOURCE_OPTIONS)],
            'jenis_bisnis' => ['required', Rule::in(array_merge(Client::BUSINESS_TYPE_OPTIONS, [Client::CUSTOM_BUSINESS_TYPE]))],
            'jenis_bisnis_custom' => ['nullable', 'required_if:jenis_bisnis,'.Client::CUSTOM_BUSINESS_TYPE, 'string', 'max:255'],
        ];
    }

    public function clientPayload(): array
    {
        $validated = $this->validated();

        if (($validated['jenis_bisnis'] ?? null) === Client::CUSTOM_BUSINESS_TYPE) {
            $validated['jenis_bisnis'] = trim((string) ($validated['jenis_bisnis_custom'] ?? ''));
        }

        unset($validated['jenis_bisnis_custom']);

        return $validated;
    }
}
