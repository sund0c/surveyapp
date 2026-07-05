<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Auth already enforced by VerifyHmacSignature middleware upstream
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('comment')) {
            // Strip any HTML/script tags before it ever reaches the DB.
            // Dashboard should still escape output on display, but this is defense-in-depth
            // against stored XSS via the comment field.
            $this->merge([
                'comment' => strip_tags((string) $this->input('comment')),
            ]);
        }
    }
}
