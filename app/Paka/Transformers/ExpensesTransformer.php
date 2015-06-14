<?php namespace App\Paka\Transformers;

use JWTAuth;
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
            'category'    => $this->categoriesTransformer->transform($expense->category),
            'friends'     => $this->userTransformer->transformFriendWithExpenseCollection($expense->friends->all()),
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
        $user = JWTAuth::parseToken()->toUser();
        $expense = new Expense;
        $expense->value = $data['value'];
        $expense->description = $data['description'];
        $expense->category_id = $data['category_id'];
        $self = $user->friends()->where('friendable_type', 'App\User')->where('friendable_id', $user->id)->first();

        $user->expenses()->save($expense);

        $expense->friends()->attach([
            $self->id => [
                'value'   => $data['value'],
                'is_paid' => true,
                'version' => 1
            ]
        ]);

        return $this->transform($expense);
    }

    /**
     * Updates the expense with the given id
     *
     * @param \App\Expense
     * @param $data
     * @return array
     */
    public function update(Expense $expense, $data)
    {
        $expense->value = $data['value'];
        $expense->description = $data['description'];
        $expense->category_id = $data['category_id'];

        $expense->save();

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
            $user->expenses()->with('friends.friendable',
                'category'

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