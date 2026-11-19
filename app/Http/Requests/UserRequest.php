<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $target = $this->route('user');
        if ($target && $target->is($this->user())) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        $target = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($target?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'distinct',
                Rule::in($this->user()->assignableRoles())],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('This email has been used before (can also be a deleted user,  see the Deleted filter).'),
            'roles.required' => __('Select at least one role.'),
            'roles.*.in' => __('You cannot assign a role you do not have access to.'),
        ];
    }
}
