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
        return [
            'category'    => 'required|array',
            'value'       => 'required|numeric',
            'description' => 'sometimes|string',
            'friends'     => 'sometimes|array',
        ];
    }

    public function categoryBelongsToUser($validator)
    {

        // Use an "after validation hook" (see laravel docs)
        $validator->after(function ($validator)
        {
            $category = $this->input('category');
            try
            {
                JWTAuth::parseToken()->toUser()->categories()->where('id', $category['id'])->firstOrFail();

            } catch (ModelNotFoundException $e)
            {
                $validator->errors()->add('category_id', 'Category id does not exist');
            }

        });
    }

}
