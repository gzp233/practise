<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化项目';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@xxx.com',
            'password' => bcrypt('admin123'),
            'avatar' => '',
            'is_admin' => true,
        ]);
    }
}
