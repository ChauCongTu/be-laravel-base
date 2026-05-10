<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Requests;

use App\Domains\Knowledge\Data\FolderData;
use Illuminate\Foundation\Http\FormRequest;

final class CreateFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'color'     => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'      => ['nullable', 'string', 'max:50'],
            'parent_id' => ['nullable', 'integer', 'exists:folders,id'],
        ];
    }

    public function toFolderData(): FolderData
    {
        return FolderData::fromArray([
            ...$this->validated(),
            'user_id' => $this->user()->id,
        ]);
    }
}
