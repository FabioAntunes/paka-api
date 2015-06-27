<?php namespace App\Http\Controllers\V2;

use App\Http\Requests\FriendRequest;
use App\Paka\Transformers\UsersTransformer;
use CouchDB;

class FriendsController extends ApiController {

    /**
     * @var UsersTransformer
     */
    protected $usersTransformer;

    public function __construct(){
        $this->middleware('couch.auth');
        $this->views['by_user'] = '_design/friends/_view/by_user';
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Response
	 */
	public function index()
	{
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get', $this->buildUrl('by_user', [
            'startkey' => [$user->name],
            'endkey' => [$user->name, json_decode ("{}")]
        ]));
        $friendViews = $this->parseStream($response);

        $friends = [];
        if(count($friendViews->rows)){
            foreach ($friendViews->rows as $row)
            {
                $friends[] = $row->doc;
            }
        }


        return $this->respond($friends);
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param FriendRequest $request
     * @return \Response
     */
	public function store(FriendRequest $request)
	{
        $user = CouchDB::getUser();

        $requestData = $request->only('_rev', 'email', 'name');
        $doc = new \stdClass();
        $doc->email = $requestData['email'];
        $doc->name = $requestData['name'];
        $doc->type = 'friend';
        $doc->user_id = $user->name;

        $response = CouchDB::executeAuth('post', 'paka/', [
            'json' => $doc
        ]);

        return $this->respondWithStream($response);
	}

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$user->name, $id]
        ]));
        $friend = $this->parseStream($response);
        if($friend->rows){
            $friend->rows[0]->doc;
            return $this->respond($friend->rows[0]->doc);
        }

        return $this->setStatusCode(404)->respondWithError('Friend not found');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $id
     * @param FriendRequest $request
     * @return \Response
     */
    public function update($id, FriendRequest $request)
    {
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$user->name, $id]
        ]));
        $friend = $this->parseStream($response);
        if($friend->rows){
            $requestData = $request->only('_rev', 'name', 'email');
            $doc = $friend->rows[0]->doc;
            $doc->_rev = $requestData['_rev'];
            $doc->email = $requestData['email'];
            $doc->name = $requestData['name'];

            $response = CouchDB::executeAuth('put', 'paka/'.$id, [
                'json' => $doc
            ]);

            return $this->respondWithStream($response);
        }

        return $this->setStatusCode(404)->respondWithError('Friend not found');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Response
	 */
	public function destroy($id)
	{
        $user = CouchDB::getUser();

        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$user->name, $id]
        ]));
        $friend = $this->parseStream($response);
        if($friend->rows){

            $doc = $friend->rows[0]->doc;
            $doc->_deleted = true;

            $response = CouchDB::executeAuth('put', 'paka/'.$id, [
                'json' => $doc
            ]);

            return $this->respondWithStream($response);
        }

        return $this->setStatusCode(404)->respondWithError('Friend not found');
	}

}
