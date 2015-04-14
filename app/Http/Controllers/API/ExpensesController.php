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
	 * @return Response
	 */
	public function index()
	{
		return $this->respond($this->expensesTransformer->currentMonth());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param ExpenseRequest|Request $request
     * @return Response
     */
	public function store(ExpenseRequest $request)
	{
        return $this->respond($this->expensesTransformer->insert($request->all()));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
