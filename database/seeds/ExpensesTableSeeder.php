<?php
use App\Expense;
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
            $expense = Expense::create([
                'value'       => rand(1, 200) / 10,
                'description' => 'Expense ' . $i,
            ]);

            $expense->users()->attach([
                1 => [
                    'is_owner'    => true,
                    'permissions' => 6
                ]
            ]);

            $expense->categories()->attach([1]);
        }

    }

}
