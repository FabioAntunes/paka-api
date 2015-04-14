<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest {

    public function validator(){

        $v = \Validator::make($this->input(), $this->rules(), $this->messages(), $this->attributes());

        if(method_exists($this, 'categoryBelongsToUser')){
            $this->categoryBelongsToUser($v);
        }

        return $v;
    }

}
