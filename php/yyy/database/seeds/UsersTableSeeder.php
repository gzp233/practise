<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'id' => 1,
                'username' => 'ggg',
                'email' => '281550060@qq.com',
                'password' => bcrypt('gggggg'),
                'avatar' => '',
                'is_admin' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'username' => 'yyy',
                'email' => '281550060@qq.com',
                'password' => bcrypt('yyyyyy'),
                'avatar' => '',
                'is_admin' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
