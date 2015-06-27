<?php namespace App\Http\Controllers\V2;

use App\Http\Requests;
use App\Http\Requests\CategoryRequest;
use CouchDB;



class CategoriesController extends ApiController {



    public function __construct()
    {
        $this->middleware('couch.auth');
        $this->views['by_user'] = '_design/categories/_view/by_user';
        $this->views['by_date'] = '_design/categories/_view/by_date';
        $this->views['exp_by_date'] = '_design/expenses/_view/by_date';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Response
     */
    public function index()
    {
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get', $this->buildUrl('by_user', [
            'startkey' => [$user->name],
            'endkey' => [$user->name, json_decode ("{}")]
        ]));
        $categoriesViews = $this->parseStream($response);

        $response = CouchDB::executeAuth('get', $this->buildUrlCurrentMonth('exp_by_date', [
            'startkey' => [$user->name],
            'endkey' => [$user->name]
        ]));
        $expensesViews = $this->parseStream($response);

        $categoriesTree = [];
        $categoriesMap = [];
        if(count($categoriesViews->rows)){
            $counter = 0;
            foreach ($categoriesViews->rows as $row)
            {
                if($row->doc->type == 'category'){
                    $row->doc->expenses = [];
                    $row->doc->total = 0;
                    $categoriesTree[] = $row->doc;
                    $categoriesMap[$row->doc->_id] = $counter;
                    $counter++;

                    continue;
                }
            }
        }

        if(count($expensesViews->rows)){
            $lastCat = null;
            foreach ($expensesViews->rows as $row)
            {
                if(!$row->doc){
                    end($categoriesTree[$lastCat]->expenses);
                    $lastExp = key($categoriesTree[$lastCat]->expenses);

                    $shared = $categoriesTree[$lastCat]->expenses[$lastExp]->shared[$row->key[4]];
                    $shared->type = 'me';
                    $shared->name = 'Me';
                    $categoriesTree[$lastCat]->expenses[$lastExp]->shared[$row->key[4]] = $shared;

                    continue;

                }

                if($row->doc->type == 'expense'){
                    $lastCat = $categoriesMap[$row->doc->category_id];
                    $row->doc->shared = property_exists($row->doc, 'shared') ? $row->doc->shared : [];
                    $categoriesTree[$lastCat]->expenses[] = $row->doc;
                    $categoriesTree[$lastCat]->total += $row->doc->value;
                    continue;
                }

                if($row->doc->type == 'friend'){
                    $lastExp = key($categoriesTree[$lastCat]->expenses);

                    $shared = $categoriesTree[$lastCat]->expenses[$lastExp]->shared[$row->key[4]];
                    $shared->type = 'friend';
                    $shared->name = $row->doc->name;
                    $shared->email = $row->doc->email;
                    $categoriesTree[$lastCat]->expenses[$lastExp]->shared[$row->key[4]] = $shared;

                    continue;
                }
            }
        }


        return $this->respond($categoriesTree);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return \Response
     */
    public function store(CategoryRequest $request)
    {
        $user = CouchDB::getUser();

        $requestData = $request->only('name', 'color');
        $doc = new \stdClass();
        $doc->name = $requestData['name'];
        $doc->color = $requestData['color'];
        $doc->type = 'category';
        $doc->user_id = $user->name;

        $response = CouchDB::executeAuth('post', 'paka/', [
            'json' => $doc
        ]);

        return $this->respondWithStream($response);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$user->name, $id]
        ]));
        $category = $this->parseStream($response);
        if($category->rows){
            $category->rows[0]->doc;
            return $this->respond($category->rows[0]->doc);
        }

        return $this->setStatusCode(404)->respondWithError('Category not found');
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
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$user->name, $id]
        ]));
        $category = $this->parseStream($response);
        if($category->rows){
            $requestData = $request->only('_rev', 'name', 'color');
            $doc = $category->rows[0]->doc;
            $doc->_rev = $requestData['_rev'];
            $doc->name = $requestData['name'];
            $doc->color = $requestData['color'];

            $response = CouchDB::executeAuth('put', 'paka/'.$id, [
                'json' => $doc
            ]);

            return $this->respondWithStream($response);
        }

        return $this->setStatusCode(404)->respondWithError('Category not found');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Response
     */
    public function destroy($id)
    {
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$user->name, $id]
        ]));
        $category = $this->parseStream($response);
        if($category->rows){

            $doc = $category->rows[0]->doc;
            $doc->_deleted = true;

            $response = CouchDB::executeAuth('put', 'paka/'.$id, [
                'json' => $doc
            ]);

            return $this->respondWithStream($response);
        }

        return $this->setStatusCode(404)->respondWithError('Category not found');
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
