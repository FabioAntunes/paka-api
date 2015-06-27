<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class CategoryRequest extends Request {

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
            'name' => 'required|max:255',
            'color' => 'required|max:7',
        ];
        if($this->is('api/v2/categories') && $this->isMethod('put')){
            $rules['_rev'] = 'required|max:255';
        }

        return $rules;
    }

}
