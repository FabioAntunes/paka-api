<?php namespace App\Paka\Transformers;

use CouchDB;

class FriendsTransformer extends Transformer {

    protected $views;

    public function __construct()
    {
        $this->views['by_user'] = '_design/friends/_view/by_user';
    }

    /**
     * Returns all the friends
     *
     * @return array
     */
    public function all()
    {
        $friends = CouchDB::executeAuth('get', $this->buildUrl('by_user'));

        return $friends->rows ? $this->transformCollection($friends->rows) : [];
    }

    /**
     * Creates a new friend for the current user
     *
     * @param array $friendData
     * @return array
     */
    public function insert($friendData)
    {
        $user = CouchDB::getUser();

        $doc = new \stdClass();
        $doc->email = $friendData['email'];
        $doc->name = $friendData['name'];
        $doc->type = 'friend';
        $doc->user_id = $user->name;

        $response = CouchDB::executeAuth('post', $this->database, [
            'json' => $doc
        ]);

        return $response;
    }

    /**
     * Find the friend with the given id
     *
     * @param $id
     * @return array
     */
    public function find($id)
    {
        $friend = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$id]
        ]));

        if(!$friend->rows){
            abort(404, "Friend Not found");
        }

        return $this->transform($friend->rows[0]);
    }

    /**
     * Updates the friend with the given id
     *
     * @param string $id
     * @param array $friendData with updated data
     * @return mixed
     */
    public function update($id, $friendData)
    {

        $friend = $this->find($id);

        $friend->_rev = $friendData['_rev'];
        $friend->name = $friendData['name'];
        $friend->email = $friendData['email'];

        $response = CouchDB::executeAuth('put', $this->database.$id, [
            'json' => $friend
        ]);
        return $response;
    }

    /**
     * Destroys the friend with the given id
     *
     * @param $id
     * @return int
     */
    public function destroy($id)
    {
        $expense = $this->find($id);
        $expense->_deleted = true;

        $response = CouchDB::executeAuth('put', 'paka/'.$id, [
            'json' => $expense
        ]);

        return $response;
    }

    /**
     * @param \stdClass $friend
     * @return array with transformed friend
     */
    public function transform($friend)
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
    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }
}
