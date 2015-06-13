<?php
use App\Invite;
use App\User;
use App\Friend;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class InvitesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $user =  User::find(1);
        $friend = new Friend;
        $friend->name = 'Me';
        $friend->friendable()->associate($user );
        $user->friends()->save($friend);

        $friend = new Friend;
        $friend->name = 'Rita Ramalhete';
        $friend->friendable()->associate(
            Invite::create([
            'email' => 'rcramalhete@gmail.com',
            ])
        );
        $user->friends()->save($friend);

        $friend = new Friend;
        $friend->name = 'João Antunes';
        $friend->friendable()->associate(
            Invite::create([
                'email' => 'bonus.j.g@gmail.com',
            ])
        );
        $user->friends()->save($friend);
    }

}
