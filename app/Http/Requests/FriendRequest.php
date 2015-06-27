<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class FriendRequest extends Request {

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

        $rules = [
            'email' => 'required|email',
            'name' => 'required|max:255',
        ];
        if($this->is('api/v2/friends') && $this->isMethod('put')){
            $rules['_rev'] = 'required|max:255';
        }

        return $rules;
	}

}
