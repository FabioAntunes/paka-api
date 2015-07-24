<?php namespace App\Paka\Transformers;
use CouchDB;

abstract class Transformer {

    protected $database = 'paka/';

    public function buildUrl($view, $keys = []){
        $url = $this->database;
        $url .= $this->views[$view].'?include_docs=true';
        $user = CouchDB::getUser();

        if(count($keys)){
            if(array_key_exists('key', $keys)){
                array_unshift($keys['key'], $user->name);
                $url.='&key='.json_encode($keys['key']);
            }else if(array_key_exists('startkey', $keys) && array_key_exists('endkey', $keys)){
                array_unshift($keys['startkey'], $user->name);
                array_unshift($keys['endkey'], $user->name);
                $keys['endkey'][] = json_decode ("{}");

                $url.='&startkey='.json_encode($keys['startkey']).'&endkey='.json_encode($keys['endkey']);
            }else{
                $defaultKeys =[
                    'startkey' => [$user->name],
                    'endkey' => [$user->name, json_decode ("{}")]
                ];
                $url.='&startkey='.json_encode($defaultKeys['startkey']).'&endkey='.json_encode($defaultKeys['endkey']);
            }
        }

        return $url;
    }

    /**
     *
     * @param string $view View name of the CouchDB
     * @param array $date Date with year and month for filtering,
     * @param array $keys Keys for the filtering
     * if empty current month and year are used
     * @return string
     */
    public function buildUrlForMonth($view, $date, $keys = []){

        $keys['startkey'][] = [$date['year'], $date['month'], null];
        $keys['endkey'][] = [$date['year'], $date['month'], 31];

        return $this->buildUrl($view, $keys);
    }

    /**
     * Transforms a paka item
     *
     * @param array $item
     * @return array transformed item
     */
    public abstract function transform($item);

    /**
     * Transforms a collection of paka items
     *
     * @param array $items
     * @return array transformed items
     */
//    public abstract function transformCollection(array $items);
}