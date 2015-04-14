<?php namespace App\Paka\Transformers;

use Carbon\Carbon;
use Tokenizer;
use App\Expense;

class ExpensesTransformer extends Transformer {


    protected $categoriesTransformer;
    protected $userTransformer;

    public function __construct()
    {
        $this->categoriesTransformer = new CategoriesTransformer();
        $this->userTransformer = new UsersTransformer();
    }

    /**
     * Creates a new expense for the current user
     *
     * @param $data
     * @return array
     */
    public function insert($data)
    {
        $expense = Expense::create($data);

        $relationAttributes = [
            'is_owner'    => true,
            'permissions' => 4,
        ];

        Tokenizer::getUser()->expenses()->attach($expense->id, $relationAttributes);

        return $this->transform($expense);
    }

    /**
     * Shares the expense with the users
     *
     * @param int $expenseId
     * @param array $users
     * @return array
     */
    public function share($expenseId, $users)
    {
        $expense = Expense::find($expenseId);

        try
        {
            $expense->users()->attach($users);

            return $this->transform($expense);

        } catch (\Exception $e)
        {
            return false;
        }

    }

    /**
     * Returns user's expenses from the current month
     *
     * @return array
     */
    public function currentMonth()
    {
        return $this->transformCollection(Tokenizer::getUser()->expenses()->where('expenses.created_at', '>=', Carbon::now()->startOfMonth())->get()->all());
    }

    /**
     * @param \App\Expense $expense
     * @return array with transformed expense
     */
    public function transform($expense)
    {
        return [
            'id'          => $expense->id,
            'value'       => $expense->value,
            'description' => $expense->description,
            'category'    => $this->categoriesTransformer->transform($expense->category),
            'users'       => $this->userTransformer->transformCollection($expense->users->all()),
        ];
    }
}