<?php

declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

final class ListPlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'asking_price' => ['required', 'integer', 'min:1'],
        ];
    }

    public function askingPrice(): int
    {
        return $this->integer('asking_price');
    }
}
