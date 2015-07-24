<?php namespace App\Paka\Transformers;

use CouchDB;

class UsersTransformer extends Transformer {

    protected $views;

    public function __construct()
    {
        $this->views['by_user'] = '_design/friends/_view/by_user';
    }

    /**
     * @param \stdClass $user
     * @return array with transformed user
     */
    public function transform($user)
    {
        $userObj = new \stdClass();
        $userObj->_id = $user->doc->_id;
        $userObj->_rev = $user->doc->_rev;
        $userObj->name = $user->doc->name;
        $userObj->email = $user->doc->email;
        $userObj->type = $user->doc->type;

        return $userObj;
    }

    /**
     * @param \stdClass $friend
     * @return array with transformed friend
     */
    public function transformFriend($friend)
    {
        $friendObj = new \stdClass();
        $friendObj->_id = $friend->doc->_id;
        $friendObj->_rev = $friend->doc->_rev;
        $friendObj->email = $friend->doc->email;
        $friendObj->name = $friend->doc->name;
        $friendObj->type = $friend->doc->type;
        $friendObj->user_id = $friend->doc->user_id;

        return $friendObj;
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