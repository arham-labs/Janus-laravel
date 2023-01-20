<?php

namespace Arhamlabs\Authentication\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use App\Traits\v1\ResponseAPI;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistrationALRequest extends FormRequest
{
    public $message = '';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        dd('s');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        dd('s');
        return [
            'email' => 'required|email:users|max:255',
            'password' => 'required',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        $this->message = $validator->errors()->first();
        throw new HttpResponseException($this->getResponse($this->statusCode, $this->errorKey, $this->message, $this->data,  $this->type, $this->primaryAction, $this->primaryActionLabel, $this->secondaryAction, $this->secondaryActionLabel, $this->token, $this->redirect, $this->isSuccess));
    }
}
