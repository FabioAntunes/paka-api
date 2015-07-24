<?php namespace App\Http\Controllers\V2;

use App\Http\Requests;
use App\Http\Requests\CategoryRequest;
use App\Paka\Transformers\CategoriesTransformer;
use Illuminate\Http\Request ;
use CouchDB;



class CategoriesController extends ApiController {

    protected $categoriesTransformer;

    public function __construct()
    {
        $this->categoriesTransformer = new CategoriesTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Response
     */
    public function index(Request $request)
    {
        $date = $this->parseDate($request);
        $categories = $this->categoriesTransformer->allWithExpenses($date);
        return $this->respond($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return \Response
     */
    public function store(CategoryRequest $request)
    {

        $requestData = $request->only('name', 'color');
        $response = $this->categoriesTransformer->insert($requestData);

        return $this->respond($response);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $category = $this->categoriesTransformer->find($id);

        return $category ? $this->respond($category) :
            $this->setStatusCode(404)->respondWithError('Category not found');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @param CategoryRequest $request
     * @return \Response
     */
    public function update($id, CategoryRequest $request)
    {
        $requestData = $request->only('_rev', 'name', 'color');
        $category = $this->categoriesTransformer->update($id, $requestData);
        return $category ? $this->respond($category) : $this->setStatusCode(404)->respondWithError('Category not found');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Response
     */
    public function destroy($id)
    {
        $this->categoriesTransformer->delete($id);
//        $user = CouchDB::getUser();
//
//        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
//            'key' => [$user->name, $id]
//        ]));
//        $category = $this->parseStream($response);
//        if($category->rows){
//
//            $doc = $category->rows[0]->doc;
//            $doc->_deleted = true;
//
//            $response = CouchDB::executeAuth('put', 'paka/'.$id, [
//                'json' => $doc
//            ]);
//
//            return $this->respondWithStream($response);
//        }
//
//        return $this->setStatusCode(404)->respondWithError('Category not found');
    }

    /**
     * Return categories with expenses
     *
     * @return \Response
     */
    public function expenses()
    {
        //
    }

}
