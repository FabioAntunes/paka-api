<?php
use App\Token;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class TokenTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

	    Token::create([
	      'user_id' => 1,
	      'type_id' => 1,
	      'key' => bcrypt('paka-api'. 1),
            'expires' => date('Y-m-d H:i:s', strtotime("+1 month"))
	    ]);

		Token::create([
			'user_id' => 2,
			'type_id' => 2,
			'key' => bcrypt('paka-api'. 2),
            'expires' => date('Y-m-d H:i:s', strtotime("+1 month"))
		]);
	}

}
