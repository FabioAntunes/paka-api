<?php namespace App\Paka\Transformers;

use App\User;
use App\Invite;
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
            'name'  => $user->pivot->name,
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
        $friends = $this->transformCollection(JWTAuth::parseToken()->toUser()->friends()->get()->all());
        $invites = $this->transformCollection(JWTAuth::parseToken()->toUser()->invites()->get()->all());
        return array_merge($friends, $invites);
    }

    /**
     * @param array $friendData
     * @return array|bool
     */
    public function attachFriend($friendData)
    {
        try{
            $user = User::whereEmail($friendData['email'])->firstOrFail();
            JWTAuth::parseToken()->toUser()->friends()->sync([$user->id =>['name' => $friendData['name']]], false);

            return [
                'id'    => $user->id,
                'name'  => $friendData['name'],
                'email' => $friendData['email'],
            ];

        }catch(\Exception $e){
            try{
                $invite = Invite::firstOrCreate(array('email' => $friendData['email']));;
                JWTAuth::parseToken()->toUser()->invites()->sync([$invite->id => ['name' => $friendData['name']]], false);

                return [
                    'name'  => $friendData['name'],
                    'email' => $friendData['email'],
                ];

            }catch(\Exception $e){
                return false;
            }
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