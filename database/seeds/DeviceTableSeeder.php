<?php
use App\Device;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class DeviceTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Device::create([
            'user_id'   => 1,
            'model'     => 'nexus 5',
            'platform'  => 'android',
            'uuid'      => '123456789',
            'version'   => '3',
        ]);

        Device::create([
            'user_id'   => 1,
            'model'     => '4s',
            'platform'  => 'ios',
            'uuid'      => '1234567890',
            'version'   => '3',
        ]);
    }

}
