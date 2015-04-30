<?php namespace App\Paka\Transformers;

use App\User;
use JWTAuth;

class UsersTransformer extends Transformer {

    /**
     * @param \App\User $user
     * @return array with transformed user
     */
    public function transform($user)
    {
        return [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ];
    }

    public function transformWithPermissions($user)
    {
        return array_add($this->transform($user), 'permissions', [
            'is_owner'    => (bool) $user->pivot->is_owner,
            'permissions' => $user->pivot->permissions,
        ]);
    }

    /**
     * Transforms a collection of users, with permissions
     *
     * @param array $items
     * @return array transformed items
     */
    public function transformCollectionWithPermissions(array $items)
    {
        return array_map([$this, 'transformWithPermissions'], $items);
    }

    /**
     * @return array with user's friends
     */
    public function friends()
    {
        return $this->transformCollection(JWTAuth::parseToken()->toUser()->friends()->get()->all());
    }

    /**
     * @param int $userId
     * @return array|bool
     */
    public function attachFriend($userId)
    {
        try
        {
            JWTAuth::parseToken()->toUser()->friends()->sync([$userId], false);

            return $this->transform(User::find($userId));

        } catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * @param int $userId
     * @return array|bool
     */
    public function detachFriend($userId)
    {
        return JWTAuth::parseToken()->toUser()->friends()->detach($userId);
    }
}