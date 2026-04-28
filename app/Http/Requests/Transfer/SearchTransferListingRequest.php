<?php

declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

final class SearchTransferListingRequest extends FormRequest
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
            'team_name' => ['sometimes', 'string', 'max:255'],
            'player_name' => ['sometimes', 'string', 'max:255'],
            'country' => ['sometimes', 'string', 'max:255'],
            'min_price' => ['sometimes', 'integer', 'min:0'],
            'max_price' => ['sometimes', 'integer', 'min:0'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        /** @var array<string, mixed> $data */
        $data = $this->only(['team_name', 'player_name', 'country', 'min_price', 'max_price']);

        return $data;
    }

    public function perPage(): int
    {
        $value = $this->integer('per_page', 15);

        return $value >= 1 ? $value : 15;
    }
}
