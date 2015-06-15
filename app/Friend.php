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

    public function scopeUserFriends($query)
    {
        return $query->where('friendable_type', '!=', 'App\User')->whereRaw('friendable_id != user_id')
            ->orWhere('friendable_type', 'App\Invite');
    }

    public function scopeSelf($query){
        return $query->whereRaw('friendable_id = user_id')
            ->where('friendable_type', 'App\User');
    }
}
