<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Token
 *
 * @property-read \App\User $user
 * @property integer $id 
 * @property integer $user_id 
 * @property string $key 
 * @property integer $type_id 
 * @property string $expires 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereExpires($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Token whereUpdatedAt($value)
 */
class Token extends Model {
	use SoftDeletes;
	protected $table = 'tokens';
	protected $fillable = ['user_id', 'type_id', 'key', 'expires'];

	public function user(){
		return $this->belongsTo('App\User');
	}

}
