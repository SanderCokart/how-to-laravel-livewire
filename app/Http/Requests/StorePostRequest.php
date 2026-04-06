<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates `POST` create payloads for posts (used by the JSON API controller).
 *
 * The Livewire page validates inline; sharing this request keeps API validation in one place if you prefer.
 *
 * @see https://laravel.com/docs/13.x/validation#form-request-validation Form request validation
 */
class StorePostRequest extends FormRequest
{
    /**
     * Only the `{user}` in the URL may create posts for that user.
     */
    public function authorize(): bool
    {
        /** @var User $owner */
        $owner = $this->route('user');

        return $this->user() !== null && $this->user()->is($owner);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }
}
