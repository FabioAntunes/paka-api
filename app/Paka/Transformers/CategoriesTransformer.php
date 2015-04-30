<?php namespace App\Paka\Transformers;

use App\Category;
use Tokenizer;
use JWTAuth;

class CategoriesTransformer extends Transformer {

    /**
     * Creates a new category for the current user
     *
     * @param $name
     * @return array
     */
    public function insert($name)
    {
        $category = Category::create(['name' => $name]);

        return $this->transform(JWTAuth::parseToken()->toUser()->categories()->save($category));
    }

    /**
     * Returns all generic categories
     *
     * @return array with categories
     */
    public function all()
    {
        return $this->transformCollection(JWTAuth::parseToken()->toUser()->categories()->get()->all());
    }

    /**
     * Updates the category with the given id
     *
     * @param $id
     * @param $name
     * @return array
     */
    public function update($id, $name)
    {
        $category = Category::find($id);

        $category->name = $name;
        $category->save();

        return $this->transform($category);
    }

    /**
     * Destroys the category with the given id
     *
     * @param $id
     * @return int
     */
    public function destroy($id)
    {
        return Category::destroy($id);
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