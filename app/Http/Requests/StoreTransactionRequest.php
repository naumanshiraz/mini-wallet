<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      return [
        'receiver_id' => [
          'required',
          'integer',
          Rule::exists('users', 'id'),
          function ($attribute, $value, $fail) {
            if ($this->user() && $this->user()->id == (int)$value) {
              $fail('You cannot send money to yourself.');
            }
          },
        ],
        'amount' => ['required', 'numeric', 'min:0.01'],
      ];
    }
}
