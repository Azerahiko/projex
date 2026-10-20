<?php

namespace App\Http\Requests\Task;

use App\Models\Project;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', Rule::in(['todo', 'in-progress', 'done'])],
            'priority' => ['nullable', 'string', Rule::in(['rendah', 'sedang', 'tinggi'])],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id'), $this->projectMemberRule()],
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
            'project_id.required' => 'Project wajib dipilih.',
            'project_id.exists' => 'Project tidak ditemukan.',
            'title.required' => 'Judul tugas wajib diisi.',
            'status.in' => 'Status tidak valid. Pilih: todo, in-progress, atau done.',
            'priority.in' => 'Prioritas tidak valid. Pilih: rendah, sedang, atau tinggi.',
            'due_date.date' => 'Deadline harus berupa tanggal yang valid.',
            'assigned_to.exists' => 'User tidak ditemukan.',
        ];
    }

    /**
     * Ensure the assignee belongs to the selected project.
     */
    private function projectMemberRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value === null) {
                return;
            }

            /** @var Project|null $project */
            $project = Project::find($this->input('project_id'));

            if (! $project) {
                return;
            }

            $isMember = $project->user_id === (int) $value
                || $project->members()->where('users.id', $value)->exists();

            if (! $isMember) {
                $fail('User yang dipilih bukan anggota project.');
            }
        };
    }
}
