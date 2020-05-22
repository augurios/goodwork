<?php

namespace App\Ticket\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateTicketCreation extends FormRequest
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
            'name'        => 'required|string|max:255',
            'content'     => 'required|string',
            'raw_content' => 'required|string',
            'draft'       => 'required|boolean',
            'group_type'  => 'required|string|in:project,team,office',
            'group_id'    => 'required|integer',
            'subject_id'  => 'required|integer|exists:subjects,id',
        ];
    }
}
