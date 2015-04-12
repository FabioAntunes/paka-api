<?php
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class UserTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

        $user1 = User::create([
          'name' => 'Fabio',
          'email' => 'fabioantuness@gmail.com',
          'password' => bcrypt('paka'),
        ]);

        User::create([
            'name' => 'Fabio Imaginario',
            'email' => 'fabioantunes@boldint.com',
            'password' => bcrypt('paka'),
        ]);
	}

}
