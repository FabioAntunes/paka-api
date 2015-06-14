<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Category
 *
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereUpdatedAt($value)
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Expense[] $expenses
 */
class Category extends Model {

    use SoftDeletes;
    protected $fillable = ['name'];
    protected $casts = [
        'total'   => 'float',
        'version' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function expenses()
    {
        return $this->hasMany('App\Expense');
    }

}
