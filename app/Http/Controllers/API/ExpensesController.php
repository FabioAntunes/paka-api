<?php namespace App\Http\Controllers\API;

use App\Expense;
use App\Http\Requests\ExpenseRequest;
use App\Paka\Transformers\ExpensesTransformer;

use Illuminate\Http\Request;

class ExpensesController extends ApiController {

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
     * @param \Illuminate\Http\Request $request
     * @return \Response
     */
	public function index(Request $request)
	{
        $month = $request->input('month', null);
		return $this->respond($this->expensesTransformer->monthlyExpenses($month));
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpenseRequest $request
     * @return \Response
     */
	public function store(ExpenseRequest $request)
	{
        return $this->respond($this->expensesTransformer->insert($request->only('value', 'description', 'category_id')));
	}

    /**
     * Display the specified resource.
     *
     * @param \App\Expense $expense
     * @return Response
     */
    public function show(Expense $expense)
    {
        return $this->respond($this->expensesTransformer->transform($expense));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Expense
     * @param ExpenseRequest $request
     * @return \Response
     */
	public function update(Expense $expense, ExpenseRequest $request)
	{
        return $this->respond($this->expensesTransformer->update($expense, $request->only('value', 'description', 'category_id')));
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
