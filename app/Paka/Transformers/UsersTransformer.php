<?php namespace App\Paka\Transformers;

use App\Friend;
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
            'name'  => $user->name,
            'email' => $user->email,
        ];
    }

    /**
     * @param \App\Friend $friend
     * @return array with transformed friend
     */
    public function transformFriend($friend)
    {
        return [
            'id'    => $friend->id,
            'name'  => $friend->name,
            'email' => $friend->friendable->email,
        ];
    }

    /**
     * @param \App\Friend $friend
     * @return array with transformed friend
     */
    public function transformFriendWithExpense($friend)
    {
        return array_merge($this->transformFriend($friend), [
            'value'   => $friend->pivot->value,
            'isPaid'  => (bool) $friend->pivot->is_paid,
            'version' => $friend->pivot->version,
        ]);
    }

    /**
     * Transforms a collection of friends
     *
     * @param array $items of friends
     * @return array transformed items
     */
    public function transformFriendCollection(array $items)
    {
        return array_map([$this, 'transformFriend'], $items);
    }

    /**
     * Transforms a collection of friends with expenses
     *
     * @param array $items of friends
     * @return array transformed items
     */
    public function transformFriendWithExpenseCollection(array $items)
    {
        return array_map([$this, 'transformFriendWithExpense'], $items);
    }

    /**
     * @return array with user's friends
     */
    public function friends()
    {
        return $this->transformFriendCollection(
            JWTAuth::parseToken()->toUser()->friends()
                ->with('friendable')->userFriends()->get()->all()
        );
    }

    /**
     * @param array $friendData
     * @return array|bool
     */
    public function attachFriend($friendData)
    {
        $friend = new Friend;
        $friend->name = $friendData['name'];
        try
        {
            $user = User::whereEmail($friendData['email'])->firstOrFail();
            $friend->friendable()->associate($user);

        } catch (\Exception $e)
        {
            try
            {
                $invite = Invite::firstOrCreate(['email' => $friendData['email']]);
                $friend->friendable()->associate($invite);

            } catch (\Exception $e)
            {
                $friend->delete();

                return false;
            }
        }

        JWTAuth::parseToken()->toUser()->friends()->save($friend);

        return $this->transformFriend($friend);
    }

    /**
     * @param int $friendId
     * @return array|bool
     */
    public function detachFriend($friendId)
    {
        return JWTAuth::parseToken()->toUser()->friends()->where('id', $friendId)->delete();
    }

    /**
     * Get User info
     * @return array|bool
     */
    public function userInfo($token = false)
    {
        if($token){
            $user =  JWTAuth::authenticate($token);
        }else{
            $user = JWTAuth::parseToken()->toUser();
        }

        $user['self']=  $this->transformFriend($user->friends()->with('friendable')->self()->first());
        return $user;
    }
}