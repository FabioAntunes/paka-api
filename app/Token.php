<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Token extends Model {
	use SoftDeletes;
	protected $table = 'tokens';
	protected $fillable = ['user_id', 'type_id', 'key', 'expires'];

	public function user(){
		return $this->belongsTo('App\User');
	}

}
