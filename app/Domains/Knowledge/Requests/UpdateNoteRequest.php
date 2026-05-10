<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Requests;

use App\Domains\Knowledge\Data\NoteData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'type'      => ['nullable', Rule::in(['markdown', 'text'])],
            'is_pinned' => ['nullable', 'boolean'],
        ];
    }

    public function toNoteData(): NoteData
    {
        return NoteData::fromArray([
            ...$this->validated(),
            'user_id' => $this->user()->id,
        ]);
    }
}
