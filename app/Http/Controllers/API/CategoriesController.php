<?php namespace App\Http\Controllers\API;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Paka\Transformers\CategoriesTransformer;

use Illuminate\Http\Request;

class CategoriesController extends ApiController {

    /**
     * @var \App\Paka\Transformers\CategoriesTransformer
     */
    protected $categoriesTransformer;

    public function __construct()
    {
        $this->middleware('auth.token');
        $this->categoriesTransformer = new CategoriesTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->respond($this->categoriesTransformer->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CategoryRequest $request)
    {
        return $this->respond($this->categoriesTransformer->insert($request->input('name')));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param CategoryRequest $request
     * @return Response
     */
    public function update($id, CategoryRequest $request)
    {
        return $this->respond($this->categoriesTransformer->update($id, $request->input('name')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $response = $this->categoriesTransformer->destroy($id);
        return $response ? $this->respond('Category deleted successfully'): $this->respondWithError('Cannot delete category');
    }

}
