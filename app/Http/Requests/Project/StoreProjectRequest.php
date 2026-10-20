<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string', 'max:255', 'required_with:client_email'],
            'client_email' => ['nullable', 'email', 'max:255', 'required_with:client_name'],
            'client_phone' => ['nullable', 'string', 'max:20'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', Rule::in(['aktif', 'arsip'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama project wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid. Pilih: aktif atau arsip.',
            'client_name.required_with' => 'Nama klien wajib diisi bila email klien diisi.',
            'client_email.required_with' => 'Email klien wajib diisi bila nama klien diisi.',
            'client_email.email' => 'Format email klien tidak valid.',
            'client_email.unique' => 'Email klien ini sudah digunakan.',
        ];
    }
}
