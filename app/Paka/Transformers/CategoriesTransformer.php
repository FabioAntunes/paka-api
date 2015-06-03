<?php namespace App\Paka\Transformers;

use App\Category;
use App\Paka\Transformers\ExpensesTransformer;
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

    public function allWithExpenses()
    {
        return $this->transformColletionWithExpenses(JWTAuth::parseToken()->toUser()->categories()->with('expenses')->get()->all());
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
            'color'       => $category->color,
            'created_at' => $category->created_at,
            'update_at'  => $category->updated_at,
        ] : [];
    }

    /**
     * @param $category
     * @return array of transformed items
     */
    public function transformWithExpenses($category)
    {
        $expensesTransformer = new ExpensesTransformer();
        return array_add($this->transform($category), 'expenses', $expensesTransformer->transformCollection($category->expenses->all()));
    }

    /**
     * Transforms a collection of categories, with expenses
     *
     * @param array $items
     * @return array of transformed items
     */
    public function transformColletionWithExpenses(array $items)
    {
        return array_map([$this, 'transformWithExpenses'], $items);
    }

}