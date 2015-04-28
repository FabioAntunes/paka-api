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
     * @param array $deviceData
     * @return \App\Token
     */
    public function insert($deviceData)
    {
        $device = $this->devicesTransformer->getDevice($deviceData);
        $token = new Token([
            'key'     => bcrypt('paka-api' . 2),
            'expires' => date('Y-m-d H:i:s', strtotime("+1 month"))
        ]);

        $token->user()->associate(Tokenizer::getUser());
        $token->device()->associate($device);

        $token->save();

        return $token;
    }

    /**
     * @param \App\Token $token
     * @return array with transformed token
     */
    public function transform($token)
    {
        return [
            'id'        => $token->id,
            'key'        => $token->key,
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

    /**
     * Get a token for a given device, if it doesn't exists creates a new one
     *
     * @param $device
     * @return array
     */
    public function getToken($device)
    {
        $token = Tokenizer::getUser()->tokens()->forDevice($device)->get()->first();

        if ($token)
        {
            return $this->transform($token);
        } else
        {
            return $this->transform($this->insert($device));
        }
    }
}