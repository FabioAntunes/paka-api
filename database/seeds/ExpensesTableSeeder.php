<?php
use App\Expense;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class ExpensesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        for ($i = 0; $i < 10; $i ++)
        {
            $rand = rand(1, 200) / 10;
            $expense = Expense::create([
                'user_id'     => 1,
                'value'       => $rand,
                'description' => 'Expense ' . $i,
                'category_id' => rand(1, 5)
            ]);

            $expense->friends()->attach([
                1 => [
                    'value'   => $rand / 2,
                    'is_paid' => true,
                    'version' => 1
                ],
                2 => [
                    'value'   => $rand / 2,
                    'is_paid' => false,
                    'version' => 1
                ]
            ]);

            User::find(1)->expenses()->save($expense);
        }

    }

}
