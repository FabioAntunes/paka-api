<?php namespace App\Paka\Transformers;

use App\Category;
use Tokenizer;

class CategoriesTransformer extends Transformer {

    /**
     * Creates a new category for the current user
     *
     * @param $data
     * @return array
     */
    public function insert($data)
    {
        $category = Category::create($data);

        return $this->transform(Tokenizer::getUser()->categories()->save($category));
    }

    /**
     * Returns generic and user's categories combined
     *
     * @return array
     */
    public function combined()
    {
        return $this->transformCollection(Tokenizer::getUser()->categories->all());
    }

    /**
     * Returns all generic categories
     *
     * @return array with categories
     */
    public function generic()
    {
        return $this->transformCollection(Category::has('user', '<', 1)->get()->all());
    }


    /**
     * @param \App\Category $category
     * @return array with transformed category
     */
    public function transform($category)
    {
        return $category ? [
            'id'         => $category->id,
            'name'       => $category->name,
            'created_at' => $category->created_at,
            'update_at'  => $category->updated_at,
        ] : [];
    }
}