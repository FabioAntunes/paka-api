<?php namespace App\Paka\Transformers;

use App\Token;
use Tokenizer;

class TokensTransformer extends Transformer {


    /**
     * @var DevicesTransformer
     */
    protected $devicesTransformer;
    /**
     * @var  UsersTransformer
     */
    protected $userTransformer;

    public function __construct()
    {
        $this->devicesTransformer = new DevicesTransformer();
        $this->userTransformer = new UsersTransformer();
    }

    /**
     * Creates a new token for the current user
     *
     * @param $data
     * @return array
     */
    public function insert($data)
    {
        $token = Token::create([
            'key' => bcrypt('paka-api'. 2),
            'expires' => date('Y-m-d H:i:s', strtotime("+1 month"))
        ]);

        $relationAttributes = [
            'is_owner'    => true,
            'permissions' => 6,
        ];

        Tokenizer::getUser()->tokens()->save($token);

        return $this->transform($expense);
    }

    /**
     * @param \App\Token $token
     * @return array with transformed token
     */
    public function transform($token)
    {
        return [
            'key'        => $token->id,
            'expires'    => $token->expires,
            'created_at' => $token->created_at,
            'updated_at' => $token->updated_at,
        ];
    }

    /**
     * @param \App\Token $token
     * @return array with transformed token plus it's relationships
     */
    public function transformWithRelationships($token)
    {
        return array_merge($this->transform($token), [
            'user'   => $this->userTransformer->transform($token->user),
            'device' => $this->devicesTransformer->transform($token->device),
        ]);
    }

    /**
     * Transforms a collection of tokens with their relationships, respectively
     *
     * @param array $items
     * @return array transformed items
     */
    public function transformCollectionWithRelationships(array $items)
    {
        return array_map([$this, 'transformWithRelationships'], $items);
    }
}