<?php namespace App\Http\Controllers\V2;

use App\Http\Requests\ExpenseRequest;
use App\Paka\Transformers\ExpensesTransformer;
use Illuminate\Http\Request ;


class ExpensesController extends ApiController {

    /**
     * @var ExpensesTransformer
     */
	protected $expensesTransformer;

    public function __construct(){
        $this->expensesTransformer = new ExpensesTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Response
     */
	public function index(Request $request)
	{
        return $this->respond($this->expensesTransformer->monthlyExpenses($this->parseDate($request)));
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpenseRequest $request
     * @return \Response
     */
	public function store(ExpenseRequest $request)
	{
        $requestData = $request->only('value', 'description', 'category_id', 'shared', 'date');
        $response = $this->expensesTransformer->insert($requestData);

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
        $expense = $this->expensesTransformer->find($id);
        return  $this->respond($expense);
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
        $requestData = $request->only('value', 'description', 'category_id', 'shared', '_rev', 'date');
        $response = $this->expensesTransformer->update($id, $requestData);
        return $this->respond($response);
	}

	/**
	 * Remove the specified resource from storage.
	 *
     * @param  string $id
	 * @return \Response
	 */
	public function destroy($id)
	{
        $response = $this->expensesTransformer->destroy($id);
        return $this->respond($response);
	}

}
