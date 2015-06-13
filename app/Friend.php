<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model {

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function friendable()
    {
        return $this->morphTo();
    }

    public function expenses()
    {
        return $this->belongsToMany('App\Expense')->withPivot('value', 'is_paid', 'version')->withTimestamps();
    }
}
