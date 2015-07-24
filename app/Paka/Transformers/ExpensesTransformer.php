<?php namespace App\Paka\Transformers;

use JWTAuth;
use App\Expense;
use App\Paka\Transformers\UsersTransformer;
use CouchDB;

class ExpensesTransformer extends Transformer {

    protected $views;
    protected $usersTransformer;

    public function __construct()
    {
        $this->views['by_date'] = '_design/expenses/_view/by_date';
        $this->views['by_user'] = '_design/expenses/_view/by_user';
        $this->views['by_category'] = '_design/expenses/_view/by_category';
        $this->usersTransformer = new UsersTransformer();
    }

    /**
     * Creates a new expense for the current user
     *
     * @param $data
     * @return array
     */
    public function insert($data)
    {
        $expense = new Expense;
        $expense->value = $data['value'];
        $expense->description = $data['description'];
        $expense->category_id = $data['category']['id'];

        JWTAuth::parseToken()->toUser()->expenses()->save($expense);
//        $self = $user->friends()->where('friendable_type', 'App\User')->where('friendable_id', $user->id)->first();


//        $expense->friends()->attach([
//            $self->id => [
//                'value'   => $data['value'],
//                'is_paid' => true,
//                'version' => 1
//            ]
//        ]);
        $syncFriends = [];
        foreach ($data['friends'] as $friend)
        {
            $syncFriends[$friend['id']] = [
                'value' => $friend['value']
            ];
        }
        $expense->friends()->sync($syncFriends);

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
        $expense->category_id = $data['category']['id'];

        $expense->save();

        $syncFriends = [];
        foreach ($data['friends'] as $friend)
        {
            $syncFriends[$friend['id']] = [
              'value' => $friend['value']
            ];
        }
        $expense->friends()->sync($syncFriends);

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

    public function categoryExpenses($id)
    {
        $rawExpenses = CouchDB::executeAuth('get', $this->buildUrl('by_category', [
            'startkey' => [$id],
            'endkey' => [$id]
        ]));

        return $this->transformCollection($rawExpenses->rows);
    }

    public function monthlyExpenses($date)
    {
        $rawExpenses = CouchDB::executeAuth('get', $this->buildUrlForMonth('by_date',$date));

        return $this->transformCollection($rawExpenses->rows);
    }
    /**
     * Returns categories filled with user's expenses,
     * if $date is false expenses are from the current month
     *
     * @param array $categories
     * @param array $categoriesMap
     * @param array $date
     * @return array
     */
    public function monthlyCategoriesExpenses($categories, $categoriesMap, $date = [])
    {

        $rawExpenses = CouchDB::executeAuth('get', $this->buildUrlForMonth('by_date',$date));

//        if(count($rawExpenses->rows)){
//            $lc = null; // Aux variable to store Last Category
//            $le = null; //Aux variable to store Last Expense
//            foreach ($rawExpenses->rows as $row)
//            {
//                if(!$row->doc){
//                    end($categories[$lc]->expenses);
//                    $le= key($categories[$lc]->expenses);
//
//                    $shared = $categories[$lc]->expenses[$le]->shared[$row->key[4]];
//                    $shared->type = 'me';
//                    $shared->name = 'Me';
//                    $categories[$lc]->expenses[$le]->shared[$row->key[4]] = $shared;
//
//                    continue;
//
//                }
//
//                if($row->doc->type == 'expense'){
//                    $lc = $categoriesMap[$row->doc->category_id];
//                    $categories[$lc]->expenses[] = $this->transform($row);
//                    $categories[$lc]->total += $row->doc->value;
//                    continue;
//                }
//
//                if($row->doc->type == 'friend'){
//                    $le= key($categories[$lc]->expenses);
//
//                    $shared = $categories[$lc]->expenses[$le]->shared[$row->key[4]];
//                    $shared->type = 'friend';
//                    $shared->name = $row->doc->name;
//                    $shared->email = $row->doc->email;
//                    $categories[$lc]->expenses[$le]->shared[$row->key[4]] = $shared;
//
//                    continue;
//                }
//            }
//        }

        return $this->transformCollectionForCategories($rawExpenses->rows, $categories, $categoriesMap);
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

    /**
     * @param \stdClass $expense
     * @return array with transformed expense
     */
    public function transform($expense)
    {
        $expenseObj = new \stdClass();
        $expenseObj->_id = $expense->doc->_id;
        $expenseObj->_rev = $expense->doc->_rev;
        $expenseObj->value = $expense->doc->value;
        $expenseObj->description = $expense->doc->description;
        $expenseObj->type = $expense->doc->type;
        $expenseObj->category_id = $expense->doc->category_id;
        $expenseObj->user_id = $expense->doc->user_id;
        $expenseObj->date = $expense->doc->date;
        $expenseObj->shared = property_exists($expense->doc, 'shared') ? $expense->doc->shared : [];

        return $expenseObj;
    }

    public function transformCollection(array $rows){
        $rows = array_reverse($rows);
        $expenses = [];
        while(!empty($rows)){

            $expenses[] = $this->recursiveTransform($rows, array_pop($rows));

        }
        return $expenses;
    }

    public function transformCollectionForCategories(array $rows, &$categories, $categoriesMap){
        $rows = array_reverse($rows);
        while(!empty($rows)){

            $expense = $this->recursiveTransform($rows, array_pop($rows));
            $categoryIndex = $categoriesMap[$expense->category_id];
            $categories[$categoryIndex]->expenses[] = $expense;
            $categories[$categoryIndex]->total += $expense->value;

        }
    }

    private function recursiveTransform(&$items, $expense)
    {
        $expense = $this->transform($expense);
        $lastItem = last($items);

        while($lastItem && (!$lastItem->doc || $lastItem->doc->type != "expense")){
            $item = array_pop($items);
            $lastItem = last($items);
            $itemIndex = last($item->key);

            $shared = $expense->shared[$itemIndex];

            if(!$item->doc){
                $shared->type = 'me';
                $shared->name = 'Me';
            }else if($item->doc->type == 'friend'){
                $shared->type = 'friend';
                $shared->name = $item->doc->name;
                $shared->email = $item->doc->email;
            }
            $expense->shared[$itemIndex] = $shared ;
        }

        return $expense;
    }

}