<?php namespace App\Http\Controllers\API;

use App\Category;
use App\Paka\Transformers\ExpensesTransformer;

class CategoriesExpensesController extends ApiController{
    /**
     * @var ExpensesTransformer
     */
    protected $expensesTransformer;

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->expensesTransformer = new ExpensesTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Category
     * @return \Response
     */
    public function index(Category $category)
    {

        return $this->respond($this->expensesTransformer->transformCollection($category->expenses->all()));
    }
}