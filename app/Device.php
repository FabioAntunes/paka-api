<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Device
 *
 * @property integer $id 
 * @property integer $user_id 
 * @property string $model 
 * @property string $platform 
 * @property string $uuid 
 * @property string $version 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property-read \App\User $user 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Token[] $tokens 
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereModel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device wherePlatform($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereUuid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Device whereUpdatedAt($value)
 */
class Device extends Model {
    use SoftDeletes;
    protected $table = 'devices';
    protected $fillable = ['user_id', 'model', 'platform', 'uuid', 'version'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function tokens()
    {
        return $this->hasMany('App\Token');
    }

}
