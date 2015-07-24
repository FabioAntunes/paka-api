<?php namespace App\Http\Controllers\V2;

use App\Http\Requests\FriendRequest;
use App\Paka\Transformers\FriendsTransformer;

class FriendsController extends ApiController {

    /**
     * @var FriendsTransformer
     */
    protected $friendsTransformer;

    public function __construct(){
        $this->friendsTransformer = new FriendsTransformer();
        $this->views['by_user'] = '_design/friends/_view/by_user';
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Response
	 */
	public function index()
	{
        $friends = $this->friendsTransformer->all();
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
        $requestData = $request->only('_rev', 'email', 'name');
        $response = $this->friendsTransformer->insert($requestData);

        return $this->respond($response);
	}

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        $friend = $this->friendsTransformer->find($id);

        return $this->respond($friend);
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
        $requestData = $request->only('_rev', 'name', 'email');
        $response = $this->friendsTransformer->update($id, $requestData);

//        $user = CouchDB::getUser();
//
//        $response = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
//            'key' => [$user->name, $id]
//        ]));
//        $friend = $this->parseStream($response);
//        if($friend->rows){
//            $doc = $friend->rows[0]->doc;
//            $doc->_rev = $requestData['_rev'];
//            $doc->email = $requestData['email'];
//            $doc->name = $requestData['name'];
//
//            $response = CouchDB::executeAuth('put', 'paka/'.$id, [
//                'json' => $doc
//            ]);
//
//            return $this->respondWithStream($response);
//        }

        return $this->respond($response);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Response
	 */
	public function destroy($id)
	{
        $response = $this->friendsTransformer->destroy($id);
        return $this->respond($response);
	}

}
