<?php namespace App;

use Illuminate\Database\Eloquent\Model;

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
 */
class Expense extends Model {

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

}
