<?php
use App\TokenType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class TokenTypeTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

	    TokenType::create([
	      'type' => 'mobile'
	    ]);

	    TokenType::create([
	      'type' => 'web'
	    ]);
	}

}
