<?php namespace App\Paka\Transformers;

use CouchDB;

class ExpensesTransformer extends Transformer {

    protected $views;

    public function __construct()
    {
        $this->views['by_date'] = '_design/expenses/_view/by_date';
        $this->views['by_user'] = '_design/expenses/_view/by_user';
        $this->views['by_category'] = '_design/expenses/_view/by_category';
    }

    /**
     * Returns all the expenses for the given category or by month
     *
     * @param string $id Category ID
     * @param array $date
     * @return array
     */
    public function categoryExpenses($id, $date = [])
    {
        if($date){
            $rawExpenses = CouchDB::executeAuth('get', $this->buildUrlForMonth('by_category', $date, [
                'startkey' => [$id],
                'endkey' => [$id]
            ]));
        }else{
            $rawExpenses = CouchDB::executeAuth('get', $this->buildUrl('by_category', [
                'startkey' => [$id],
                'endkey' => [$id]
            ]));

        }

        return $this->transformCollection($rawExpenses->rows);
    }

    /**
     * Returns all expenses for the given month
     *
     * @param $date
     * @return array
     */
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
        return $this->transformCollectionForCategories($rawExpenses->rows, $categories, $categoriesMap);
    }

    /**
     * Creates a new expense for the current user
     *
     * @param array $expenseData
     * @return array
     */
    public function insert($expenseData)
    {
        $user = CouchDB::getUser();

        $doc = new \stdClass();
        $doc->value = $expenseData['value'];
        $doc->description = $expenseData['description'];
        $doc->type = 'expense';
        $doc->category_id = $expenseData['category_id'];
        $doc->user_id = $user->name;
        $doc->date = $expenseData['date'];

        if(count($expenseData['shared']) > 1){
            $doc->shared = [];
            foreach ($expenseData['shared'] as $friend)
            {
                $doc->shared[] = [
                    'friend_id' => $friend['friend_id'],
                    'value' => $friend['value'],
                ];
            }

        }

        $response = CouchDB::executeAuth('post', $this->database, [
            'json' => $doc
        ]);

        return $response;
    }

    /**
     * Find the expense with the given id
     *
     * @param $id
     * @return array
     */
    public function find($id)
    {
        $expenseInfo = CouchDB::executeAuth('get',  $this->buildUrl('by_user',[
            'startkey' => [$id],
            'endkey' => [$id]
        ]));

        if(!$expenseInfo->rows){
            abort(404, "Expense Not found");
        }

        $reversed = array_reverse($expenseInfo->rows);
        $expense = $this->recursiveTransform($reversed, array_pop($reversed));
        return $expense;
    }

    /**
     * Updates the expense with the given id
     *
     * @param string $id
     * @param $expenseData
     * @return array
     */
    public function update($id, $expenseData)
    {
        $expense = $this->find($id);

        $expense->_rev = $expenseData['_rev'] ? $expenseData['_rev'] : $expense->_rev ;
        $expense->description = $expenseData['description'] ? $expenseData['description'] : $expense->description ;
        $expense->value = $expenseData['value'] ? $expenseData['value'] : $expense->value ;
        $expense->category_id = $expenseData['category_id'] ? $expenseData['category_id'] : $expense->category_id ;
        $expense->date = $expenseData['date'] ? $expenseData['date'] : $expense->date ;

        $expense->shared = [];
        if(count($expenseData['shared']) > 1){
            foreach ($expenseData['shared'] as $friend)
            {
                $expense->shared[] = [
                    'friend_id' => $friend['friend_id'],
                    'value' => $friend['value'],
                ];
            }
        }

        $response = CouchDB::executeAuth('post', $this->database.$id, [
            'json' => $expense
        ]);

        return $response;
    }

    /**
     * Destroys the expense with the given id
     *
     * @param $id
     * @return int
     */
    public function destroy($id)
    {
        $expense = $this->find($id);
        $expense->_deleted = true;

        $response = CouchDB::executeAuth('put', 'paka/'.$id, [
            'json' => $expense
        ]);

        return $response;
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