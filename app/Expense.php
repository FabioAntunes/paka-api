<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Expense
 *
 * @property integer $id
 * @property integer $category_id
 * @property float $value
 * @property string $description
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Expense whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Expense whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Expense whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Expense whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Expense whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Expense whereUpdatedAt($value)
 * @property-read \App\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $categories
 */
class Expense extends Model {

    use SoftDeletes;
    protected $fillable = ['category_id', 'value', 'description'];

    public function friends()
    {
        return $this->belongsToMany('App\Friend')->withPivot('value', 'is_paid', 'version')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }
}
