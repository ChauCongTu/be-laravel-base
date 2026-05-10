<?php

declare(strict_types=1);

namespace App\Domains\Knowledge\Requests;

use App\Domains\Knowledge\Data\SnippetData;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateSnippetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'folder_id'   => ['nullable', 'integer', 'exists:folders,id'],
            'title'       => ['required', 'string', 'max:255'],
            'code_block'  => ['required', 'string', 'max:50000'],
            'language'    => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function toSnippetData(): SnippetData
    {
        return SnippetData::fromArray([
            ...$this->validated(),
            'user_id' => $this->user()->id,
        ]);
    }
}
