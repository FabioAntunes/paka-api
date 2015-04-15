<?php namespace App\Http\Controllers\API;

use App\Http\Requests\ExpenseRequest;
use App\Paka\Transformers\ExpensesTransformer;

use Illuminate\Http\Request;

class ExpensesController extends ApiController {

    /**
     * @var \App\Paka\Transformers\ExpensesTransformer
     */
	protected $expensesTransformer;

    public function __construct(){
        $this->middleware('auth.token');
        $this->expensesTransformer = new ExpensesTransformer();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
	public function index(Request $request)
	{
        $month = $request->input('month', null);
		return $this->respond($this->expensesTransformer->monthlyExpenses($month));
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpenseRequest|Request $request
     * @return Response
     */
	public function store(ExpenseRequest $request)
	{
        return $this->respond($this->expensesTransformer->insert($request->only('value', 'description', 'category_id')));
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param ExpenseRequest $request
     * @return Response
     */
	public function update($id, ExpenseRequest $request)
	{
        return $this->respond($this->expensesTransformer->update($id, $request->only('value', 'description', 'category_id', 'relationships')));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $response = $this->expensesTransformer->destroy($id);
        return $response ? $this->respond('Expense deleted successfully'): $this->respondWithError('Cannot delete expense');
	}

}
