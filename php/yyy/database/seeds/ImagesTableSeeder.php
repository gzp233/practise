<?php

use Illuminate\Database\Seeder;
use App\Models\ImageDirectory;

class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ImageDirectory::insert([
            [
                'directory_name' => 'QQ群',
                'desc' => '来自QQ群',
                'user_id' => 1,
                'is_forbidden' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'directory_name' => '个人收藏',
                'desc' => 'admin的个人收藏',
                'user_id' => 1,
                'is_forbidden' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
