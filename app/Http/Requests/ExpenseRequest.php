<?php namespace App\Http\Requests;

use App\Http\Requests\Request;
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
            'category_id' => 'required',
            'value'       => 'required|numeric',
            'description' => 'sometimes|string',
            'relationships' => 'sometimes|array',
        ];
    }

    public function categoryBelongsToUser($validator)
    {

        // Use an "after validation hook" (see laravel docs)
        $validator->after(function ($validator)
        {
            try
            {
                $user = \Tokenizer::getUser();
                Category::where('id', $this->input('category_id'))->where(function ($query) use ($user)
                {
                    $query->where('user_id', $user->id)->orWhereNull('user_id');
                })->firstOrFail();

            } catch (ModelNotFoundException $e)
            {
                $validator->errors()->add('category_id', 'Category id does not exist');
            }

        });
    }

}
