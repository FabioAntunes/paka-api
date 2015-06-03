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
            'color' => '#AD242D',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Food',
            'color' => '#BEE6CE',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Transportation',
            'color' => '#D98D07',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Leisure',
            'color' => '#21A179',
            'user_id' => 1,
        ]);

        Category::create([
            'name'    => 'Education',
            'color' => '#073B3A',
            'user_id' => 1,
        ]);


    }

}
