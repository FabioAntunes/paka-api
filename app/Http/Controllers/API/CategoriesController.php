<?php namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Requests;
use App\Http\Requests\CategoryRequest;
use App\Paka\Transformers\CategoriesTransformer;



class CategoriesController extends ApiController {

    /**
     * @var CategoriesTransformer
     */
    protected $categoriesTransformer;

    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->categoriesTransformer = new CategoriesTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Response
     */
    public function index()
    {
        return $this->respond($this->categoriesTransformer->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return \Response
     */
    public function store(CategoryRequest $request)
    {
        return $this->respond($this->categoriesTransformer->insert($request->only('name', 'color')));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Category $category
     * @return Response
     */
    public function show(Category $category)
    {
        return $this->respond($this->categoriesTransformer->transform($category));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Category $category
     * @param CategoryRequest $request
     * @return \Response
     */
    public function update(Category $category, CategoryRequest $request)
    {
        return $this->respond($this->categoriesTransformer->update($category, $request->only('name', 'color')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Category $category
     * @return \Response
     */
    public function destroy(Category $category)
    {
        return  $category->delete() ? $this->respond('Category deleted successfully'): $this->respondWithError('Cannot delete category');
    }

    /**
     * Return categories with expenses
     *
     * @return \Response
     */
    public function expenses()
    {
        return $this->respond($this->categoriesTransformer->allWithExpenses());
    }

}
