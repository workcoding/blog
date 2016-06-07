<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test{id}{--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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




         sleep(1);
        //获取名字为user的参数的值
        $userId = $this->argument();

        dd($userId);

        //$name = $this->ask('What is your name?');
       //$password = $this->secret('What is the password?');
        //$name = $this->choice('What is your name?', ['Taylor', 'Dayle'], false);
      //  $name = $this->anticipate('What is your name?', ['Taylor', 'Dayle']);
        Log::info('This is some useful information.'.json_encode($userId));
        exit;

        $this->error($userId);
        exit;
        DB::connection()->enableQueryLog();
        $results = DB::connection('mysql2')->select('select * from blog_article where art_id = 11 order by art_id desc');


        $queries = DB::getQueryLog();
        echo "<pre>";
        echo (json_encode($results));

        exit;


        //
    }
}
