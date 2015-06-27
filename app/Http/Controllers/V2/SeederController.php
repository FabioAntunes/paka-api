<?php namespace App\Http\Controllers\V2;

use App\Http\Requests;
use CouchDB;
use Illuminate\Http\Request;

class SeederController extends ApiController {

    public function __construct()
    {
        $this->middleware('couch.auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function categories()
    {
        $user = CouchDB::getUser();
        $categories = [
            'docs' => [
                [
                    'type' => 'category',
                    'name'    => 'Health',
                    'color'   => '#AD242D',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Food',
                    'color'   => '#BEE6CE',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Transportation',
                    'color'   => '#D98D07',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Leisure',
                    'color'   => '#21A179',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Education',
                    'color'   => '#073B3A',
                    'user_id' => $user->name,
                ]
            ]
        ];

        $response = CouchDB::executeAuth('POST', 'paka/_bulk_docs', [
            'headers' => [
                'Content-Type'=> 'application/json'
            ],
            'body' => json_encode($categories)
        ]);

        return $this->respond($response->getReasonPhrase());
    }

    public function friends()
    {
        $user = CouchDB::getUser();
        $friends = [
            'docs' => [
                [
                    'type' => 'friend',
                    'name'    => 'Rita Ramalhete',
                    'email' => 'rcramalhete@gmail.com',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'friend',
                    'name'    => 'João Antunes',
                    'email' => 'bonus.j.g@gmail.com',
                    'user_id' => $user->name,
                ],
            ]
        ];

        $response = CouchDB::executeAuth('POST', 'paka/_bulk_docs', [
            'headers' => [
                'Content-Type'=> 'application/json'
            ],
            'body' => json_encode($friends)
        ]);

        return $this->respond($response->getReasonPhrase());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function expenses()
    {
        $categories = [
            '4bd79a1aad58a109c04d8ece70000591',
            '4bd79a1aad58a109c04d8ece70000ee9',
            '4bd79a1aad58a109c04d8ece70001429',
            '4bd79a1aad58a109c04d8ece70001af6',
            '4bd79a1aad58a109c04d8ece70002a39'
        ];
        $user = CouchDB::getUser();
        $expenses= [
            'docs' => []
        ];
        for ($i = 0; $i < 10; $i ++)
        {
            $rand = rand(1, 200) / 10;
            $expenses['docs'][] = [
                'type'        => 'expense',
                'category_id' => $categories[rand(0, 4)],
                'user_id'     => $user->name,
                'value'       => $rand,
                'description' => 'Expense ' . $i,
                'date' => [rand(1, 30), 6, 2015],
                'shared' => [
                    [
                        'value' => $rand/3,
                        'friend_id' => $user->name,
                    ],
                    [
                        'value' => $rand/3,
                        'friend_id' => '4bd79a1aad58a109c04d8ece70003175',
                    ],
                    [
                        'value' => $rand/3,
                        'friend_id' => '4bd79a1aad58a109c04d8ece70003e65',
                    ]
                ]
            ];
        }

        $response = CouchDB::executeAuth('POST', 'paka/_bulk_docs', [
            'headers' => [
                'Content-Type'=> 'application/json'
            ],
            'body' => json_encode($expenses)
        ]);

        return $this->respond($response->getReasonPhrase());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
