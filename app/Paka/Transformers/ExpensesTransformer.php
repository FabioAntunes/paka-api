<?php namespace App\Paka\Transformers;

use App\Expense;

class ExpensesTransformer extends Transformer {

    protected $categoriesTransformer;

    public function __construct()
    {
        $this->categoriesTransformer = new CategoriesTransformer();
        $this->categoriesTransformer = new UsersTransformer();
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
            'users'       => $expense->users,
        ];
    }
}