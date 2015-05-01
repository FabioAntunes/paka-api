<?php namespace App\Http\Controllers\API;

use App\Http\Requests\FriendRequest;
use App\Paka\Transformers\UsersTransformer;

class FriendsController extends ApiController {

    /**
     * @var UsersTransformer
     */
    protected $usersTransformer;

    public function __construct(){
        $this->middleware('jwt.auth');
        $this->usersTransformer = new UsersTransformer();
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Response
	 */
	public function index()
	{
        return $this->respond($this->usersTransformer->friends());
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param FriendRequest $request
     * @return \Response
     */
	public function store(FriendRequest $request)
	{
        $response = $this->usersTransformer->attachFriend($request->only('email'));
        return $response ? $this->respond($response) : $this->setStatusCode(500)->respond('Cannot add friend') ;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Response
	 */
	public function destroy($id)
	{
        $response = $this->usersTransformer->detachFriend($id);
        return $response ? $this->respond('Friend removed') : $this->setStatusCode(500)->respond('Cannot remove friend') ;
	}

}
