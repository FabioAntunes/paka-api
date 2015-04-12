<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Invite
 *
 * @property integer $id
 * @property integer $expense_id
 * @property boolean $has_accepted
 * @property boolean $permissions
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereExpenseId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereHasAccepted($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite wherePermissions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Invite whereUpdatedAt($value)
 */
class Invite extends Model {

	//

}
