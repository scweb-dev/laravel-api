<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Client;

class CreateUserNote extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return (true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return ([
            "user"       => "uuid|exists:users,user_uuid",
            "user_notes" => "required|string",
            "files"      => "array|min:1",
            "files.*"    => "required|file|mimes:png,bmp,jpg,jpeg,tiff,pdf,doc,docx,txt",
        ]);
    }

    protected function getValidatorInstance() {
        $v = parent::getValidatorInstance();
        $v->sometimes("user", "required|uuid", function () {
            return (Client::app()->app_name == "office");
        });

        return ($v);
    }
}
