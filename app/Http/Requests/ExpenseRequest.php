<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
use JWTAuth;
use App\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExpenseRequest extends Request {

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
        $rules =  [
            'category_id'    => 'required|string',
            'value'       => 'required|numeric',
            'description' => 'sometimes|string',
            'shared'     => 'sometimes|array',
            'date'     => 'required|array',
        ];

        if($this->is('api/v2/friends') && $this->isMethod('put')){
            $rules['_rev'] = 'required|max:255';
        }

        return $rules;
    }

}
