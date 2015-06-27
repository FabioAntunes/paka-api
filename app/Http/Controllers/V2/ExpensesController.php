<?php namespace App\Http\Controllers\V2;

use App\Expense;
use CouchDB;
use App\Http\Requests\ExpenseRequest;
use App\Paka\Transformers\ExpensesTransformer;

use Illuminate\Http\Request;

class ExpensesController extends ApiController {

    /**
     * @var ExpensesTransformer
     */
	protected $expensesTransformer;

    public function __construct(){
        $this->middleware('couch.auth');
//        $this->expensesTransformer = new ExpensesTransformer();
        $this->views['by_date'] = '_design/expenses/_view/by_date';
        $this->views['by_user'] = '_design/expenses/_view/by_user';
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Response
     */
	public function index(Request $request)
	{
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get', $this->buildUrlCurrentMonth('by_date', [
            'startkey' => [$user->name],
            'endkey' => [$user->name]
        ]));
        $expensesViews = json_decode($response->getBody()->getContents());
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

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpenseRequest $request
     * @return \Response
     */
	public function store(ExpenseRequest $request)
	{

        $user = CouchDB::getUser();

        $requestData = $request->only('value', 'description', 'category_id', 'shared', 'date');

        $doc = new \stdClass();
        $doc->value = $requestData['value'];
        $doc->description = $requestData['description'];
        $doc->type = 'expense';
        $doc->category_id = $requestData['category_id'];
        $doc->user_id = $user->name;
        $doc->date = $requestData['date'];

        if(count($requestData['shared']) > 1){
            $doc->shared = [];
            foreach ($requestData['shared'] as $friend)
            {
                $doc->shared[] = [
                    'friend_id' => $friend['friend_id'],
                    'value' => $friend['value'],
                ];
            }

        }

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
            'startkey' => [$user->name, $id],
            'endkey' => [$user->name, $id, json_decode ("{}")]
        ]));

        $expensesViews = $this->parseStream($response);
        if(count($expensesViews->rows)){
            $expense = null;
            foreach ($expensesViews->rows as $row)
            {
                if(!$row->doc){
                    $shared = $expense->shared[$row->key[3]];
                    $shared->_id = $row->value->_id;
                    $shared->type = 'me';
                    $shared->name = 'Me';
                    $expense->shared[$row->key[3]] = $shared;

                    continue;

                }

                if($row->doc->type == 'expense'){
                    $row->doc->shared = property_exists($row->doc, 'shared') ? $row->doc->shared : [];
                    $expense = $row->doc;
                    continue;
                }

                if($row->doc->type == 'friend'){

                    $shared = $expense->shared[$row->key[3]];
                    $shared->type = 'friend';
                    $shared->name = $row->doc->name;
                    $shared->email = $row->doc->email;
                    $expense->shared[$row->key[3]] = $shared;

                    continue;
                }
            }

            return $this->respond($expense);
        }

        return $this->setStatusCode(404)->respondWithError('Expense not found');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Expense
     * @param ExpenseRequest $request
     * @return \Response
     */
	public function update($id, ExpenseRequest $request)
	{
        $user = CouchDB::getUser();
        $requestData = $request->only('value', 'description', 'category_id', 'shared', '_rev', 'date');

        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'startkey' => [$user->name, $id],
            'endkey' => [$user->name, $id, json_decode ("{}")]
        ]));

        $expense = $this->parseStream($response);

        if($expense->rows){
            $doc = $expense->rows[0]->doc;
            $doc->_rev = $requestData['_rev'] ? $requestData['_rev'] : $doc->_rev ;
            $doc->description = $requestData['description'] ? $requestData['description'] : $doc->description ;
            $doc->value = $requestData['value'] ? $requestData['value'] : $doc->value ;
            $doc->type = 'expense';
            $doc->category_id = $requestData['category_id'] ? $requestData['category_id'] : $doc->category_id ;
            $doc->user_id = $user->name;
            $doc->date = $requestData['date'] ? $requestData['date'] : $doc->date ;

            $doc->shared = [];
            if(count($requestData['shared']) > 1){
                foreach ($requestData['shared'] as $friend)
                {
                    $doc->shared[] = [
                        'friend_id' => $friend['friend_id'],
                        'value' => $friend['value'],
                    ];
                }

            }

            $response = CouchDB::executeAuth('post', 'paka/', [
                'json' => $doc
            ]);

            return $this->respondWithStream($response);
        }

        return $this->setStatusCode(404)->respondWithError('Expense not found');
	}

	/**
	 * Remove the specified resource from storage.
	 *
     * @param  \App\Expense
	 * @return \Response
	 */
	public function destroy(Expense $expense)
	{
        return $expense->delete() ? $this->respond('Expense deleted successfully'): $this->respondWithError('Cannot delete expense');
	}

}
