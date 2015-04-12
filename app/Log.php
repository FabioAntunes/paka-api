<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Log
 *
 * @property integer $id 
 * @property integer $token_id 
 * @property integer $ip_addres 
 * @property string $request 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereTokenId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereIpAddres($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereRequest($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Log whereUpdatedAt($value)
 */
class Log extends Model {

	//

}
