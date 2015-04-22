<?php namespace App\Paka\Transformers;

use App\Device;
use Tokenizer;

class DevicesTransformer extends Transformer {

    /**
     * @var  UsersTransformer
     */
    protected $userTransformer;

    public function __construct()
    {
        $this->userTransformer = new UsersTransformer();
    }

    /**
     * @param \App\Device $device
     * @return array with transformed token
     */
    public function transform($device)
    {
        return [
            'model'      => $device->model,
            'platform'   => $device->platform,
            'uuid'       => $device->uuid,
            'version'    => $device->version,
            'created_at' => $device->created_at,
            'updated_at' => $device->updated_at,
        ];
    }

    /**
     * @param \App\Device $device
     * @return array with transformed token plus it's relationships
     */
    public function transformWithUser($device)
    {
        return array_add($this->transform($device), 'user',
            $this->userTransformer->transform($device->user));
    }

    /**
     * Transforms a collection of users, with permissions
     *
     * @param array $items
     * @return array transformed items
     */
    public function transformCollectionWithUsers(array $items)
    {
        return array_map([$this, 'transformWithUser'], $items);
    }
}