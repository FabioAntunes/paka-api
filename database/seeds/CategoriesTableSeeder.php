<?php
use App\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class CategoriesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Category::create([
            'name'    => 'Health',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Food',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Transportation',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Leisure',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Education',
            'user_id' => 1,
        ]);


    }

}
