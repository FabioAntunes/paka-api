<?php namespace App\Http\Controllers\V2;

use CouchDB;
use App\Paka\Transformers\ExpensesTransformer;

class CategoriesExpensesController extends ApiController{
    /**
     * @var ExpensesTransformer
     */
    protected $expensesTransformer;

    public function __construct(){
        $this->middleware('couch.auth');
        $this->views['by_user'] = '_design/categories/_view/by_user';
        $this->views['by_category'] = '_design/expenses/_view/by_category';
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Category
     * @return \Response
     */
    public function index($id)
    {
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get', $this->buildUrl('by_user', [
            'key' => [$user->name, $id]
        ]));
        $categoryView = $this->parseStream($response);

        $response = CouchDB::executeAuth('get', $this->buildUrlCurrentMonth('by_category', [
            'startkey' => [$id],
            'endkey' => [$id]
        ]));
        $expensesViews = $this->parseStream($response);
        if(!$categoryView->rows){
            return $this->setStatusCode(404)->respondWithError('Category not found');
        }

        $expenses = [];
        if(count($expensesViews->rows)){
            $lastCat = null;
            foreach ($expensesViews->rows as $row)
            {
                if(!$row->doc){
                    end($expenses);
                    $lastExp = key($expenses);

                    $shared = $expenses[$lastExp]->shared[$row->key[4]];
                    $shared->type = 'me';
                    $shared->name = 'Me';
                    $expenses[$lastExp]->shared[$row->key[4]] = $shared;

                    continue;

                }

                if($row->doc->type == 'expense'){
                    $row->doc->shared = property_exists($row->doc, 'shared') ? $row->doc->shared : [];
                    $expenses[] = $row->doc;
                    continue;
                }

                if($row->doc->type == 'friend'){
                    $lastExp = key($expenses);

                    $shared = $expenses[$lastExp]->shared[$row->key[4]];
                    $shared->type = 'friend';
                    $shared->name = $row->doc->name;
                    $shared->email = $row->doc->email;
                    $expenses[$lastExp]->shared[$row->key[4]] = $shared;

                    continue;
                }
            }
        }
        return $this->respond($expenses);
    }
}