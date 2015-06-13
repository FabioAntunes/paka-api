<?php namespace App\Paka\Transformers;

use App\Expense;

use Carbon\Carbon;

class ExpensesTransformer extends Transformer {


    /**
     * @var CategoriesTransformer
     */
    protected $categoriesTransformer;
    /**
     * @var  UsersTransformer
     */
    protected $userTransformer;

    public function __construct()
    {
        $this->categoriesTransformer = new CategoriesTransformer();
        $this->userTransformer = new UsersTransformer();
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
            'friends'     => $this->userTransformer->transformFriendCollection($expense->friends->all()),
            'created_at'  => $expense->created_at,
            'update_at'   => $expense->updated_at,
        ];
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

        JWTAuth::parseToken()->toUser()->expenses()->attach($expense->id, $relationAttributes);
        $expense->categories()->attach($data['category_id']);

        return $this->transform($expense);
    }

    /**
     * Updates the expense with the given id
     *
     * @param $id
     * @param $data
     * @return array
     */
    public function update($id, $data)
    {
        $expense = Expense::find($id);

        $expense->value = $data['value'];
        $expense->description = $data['description'];

        $expense->save();

        $expense->users()->sync($data['relationships'], false);

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
     * Returns user's expenses, if $month is null expenses from the current month are returned
     *
     * @param int $month
     * @return array
     */
    public function monthlyExpenses($month = null)
    {
        $user = JWTAuth::parseToken()->toUser();
        $carbon = Carbon::create(null, $month);


        return $this->transformCollection(
            $user->expenses()->with(
                [
                    'friends.friendables',
                    'categories' => function ($query) use ($user)
                    {
                        $query->where('user_id', $user->id);
                    }
                ]
            )->whereBetween('expenses.created_at', [
                $carbon->startOfMonth()->toDateString(),
                $carbon->addMonth()->toDateString()
            ])->get()->all()
        );
    }

    /**
     * Destroys the expense with the given id
     *
     * @param $id
     * @return int
     */
    public function destroy($id)
    {
        return Expense::destroy($id);
    }

}