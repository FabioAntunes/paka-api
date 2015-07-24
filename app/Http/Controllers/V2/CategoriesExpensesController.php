<?php namespace App\Http\Controllers\V2;

use App\Paka\Transformers\ExpensesTransformer;
use Illuminate\Http\Request;

class CategoriesExpensesController extends ApiController{
    /**
     * @var ExpensesTransformer
     */
    protected $expensesTransformer;

    public function __construct(){
        $this->expensesTransformer = new ExpensesTransformer();
        $this->views['by_user'] = '_design/categories/_view/by_user';
        $this->views['by_category'] = '_design/expenses/_view/by_category';
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $id Category id
     * @param Request $request
     * @return \Response
     */
    public function index($id, Request $request)
    {
        $expenses = $this->expensesTransformer->categoryExpenses($id, $this->parseDate($request));
        return $this->respond($expenses);
    }
}