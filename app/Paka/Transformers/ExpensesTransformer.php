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
        $expense = Expense::create([
            'value'       => $data['value'],
            'description' => $data['description'],
        ]);

        $relationAttributes = [
            'is_owner'    => true,
            'permissions' => 6,
        ];

        Tokenizer::getUser()->expenses()->attach($expense->id, $relationAttributes);
        $expense->categories()->attach($data['category_id']);

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
        $userId = Tokenizer::getUser()->id;
        
        return $this->transformCollection(
            Tokenizer::getUser()->expenses()->with(
                [
                    'users' => function ($query) use ($userId)
                    {
                        $query->where('user_id', $userId);
                    },
                    'categories' => function ($query) use ($userId)
                    {
                        $query->where('user_id', $userId);
                    }
                ]
            )->get()->all()
        );
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
            'category'    => $this->categoriesTransformer->transform($expense->categories->first()),
            'users'       => $this->userTransformer->transformCollection($expense->users->all()),
            'created_at'  => $expense->created_at,
            'update_at'   => $expense->updated_at,
        ];
    }
}