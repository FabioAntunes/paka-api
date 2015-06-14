<?php namespace App\Paka\Transformers;

use App\Category;
use App\Paka\Transformers\ExpensesTransformer;
use JWTAuth;

class CategoriesTransformer extends Transformer {

    /**
     * Creates a new category for the current user
     *
     * @param array $categoryData
     * @return array
     */
    public function insert($categoryData)
    {
        $category = new Category;
        $category->name = $categoryData['name'];
        $category->color = $categoryData['color'];

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
     * @param \App\Category $category
     * @param array $categoryData with updated data
     * @return array
     */
    public function update(Category $category, $categoryData)
    {
        if($category->name != $categoryData['name'] || $category->color != $categoryData['color']){
            $category->name = $categoryData['name'];
            $category->color = $categoryData['color'];
            $category->version = $category->version+1;

            $category->save();

        }


        return $this->transform($category);
    }

    /**
     * Find the category with the given id
     *
     * @param $id
     * @return array
     */
    public function find($id)
    {
        return $this->transform(Category::find($id));
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
            'total'       => $category->total,
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