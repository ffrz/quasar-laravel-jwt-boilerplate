<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah jika perlu otorisasi
    }

    public function rules(): array
    {
        $userId = $this->route('user'); // asumsi route menggunakan {user}

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                $userId ? 'nullable' : 'required',
                'string',
                'min:6',
            ],
            'active' => ['nullable', 'boolean'],
            'role' => ['required', 'string', Rule::in(array_keys(User::Roles))],
        ];
    }
}
