<?php namespace App\Paka\Transformers;

class UsersTransformer extends Transformer{

    /**
     * @param \App\User $user
     * @return array with transformed user
     */
    public function transform($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}