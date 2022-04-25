<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        App\User::create([
            'userId' => 'user1',
            'name' => 'loganath',
            'email' => 'logu.nath001@gmail.com',
            'mobile_number' => '9629188839',
        ]);

        App\User::create([
            'userId' => 'user2',
            'name' => 'Raj',
            'email' => 'raj@gmail.com',
            'mobile_number' => '7629188838',
        ]);

        App\User::create([
            'userId' => 'user3',
            'name' => 'Sonu',
            'email' => 'sonu@gmail.com',
            'mobile_number' => '8645667899',
        ]);

        App\User::create([
            'userId' => 'user4',
            'name' => 'Pino',
            'email' => 'pino@gmail.com',
            'mobile_number' => '7445667899',
        ]);

        App\User::create([
            'userId' => 'user5',
            'name' => 'David',
            'email' => 'david@gmail.com',
            'mobile_number' => '9253636666',
        ]);

    }
}
