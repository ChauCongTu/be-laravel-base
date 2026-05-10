<?php

declare(strict_types=1);

namespace App\Domains\Task\Requests;

use App\Domains\Task\Data\TaskData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', Rule::in(['todo', 'doing', 'done'])],
            'priority'    => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date'    => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
        ];
    }

    public function toTaskData(): TaskData
    {
        return TaskData::fromArray([
            ...$this->validated(),
            'user_id' => $this->user()->id,
        ]);
    }
}
