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
            'name' => 'Health',
        ]);

        Category::create([
            'name' => 'Food',
        ]);

        Category::create([
            'name' => 'Transportation',
        ]);

        Category::create([
            'name' => 'Leisure',
        ]);

        Category::create([
            'name' => 'Education',
        ]);

        Category::create([
            'name'    => 'User 1 Category',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'User 1 Category 2',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'User 2 Category',
            'user_id' => 2,
        ]);

    }

}
