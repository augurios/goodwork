<?php

namespace App\Base\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ValidateCommentTicketCreation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body'             => 'required',
            'commentable_type' => 'required|string',
            'commentable_id'   => 'required|integer|exists:tickets,id',
        ];
    }
}
