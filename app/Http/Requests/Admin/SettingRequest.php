<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'brand_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'welcome_message' => 'nullable|string',
            // Se comprime y convierte a WebP al guardarlo (ver ImageUploadService),
            // por eso se admite un archivo original más pesado que el que queda en disco.
            'logo' => 'nullable|image|max:8192', // 8MB Max (antes de comprimir)
        ];
    }
}
