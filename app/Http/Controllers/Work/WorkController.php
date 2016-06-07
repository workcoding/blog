<?php

namespace App\Http\Controllers\Work;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Model\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class WorkController extends Controller
{

    public function index()
    {
        //abort(404);

        $exitCode = Artisan::call('command:test', [
            'id' => 1, '--queue' => 'default'
        ]);


        echo 123;
        //print_r($exitCode);
        exit;

        Log::info('This is some useful information.');
        Log::warning('Something could be going wrong.');

        Log::error('Something is really going wrong.');
        Log::info('Log message', ['context' => 'Other helpful information']);
//        print_r(Log);
//        exit;
        DB::connection()->enableQueryLog();
        $results = DB::connection('mysql2')->select('select * from blog_article where art_id = 11 order by art_id desc');
        $queries = DB::getQueryLog();
        echo "<pre>";
        echo (json_encode($results));

        exit;



        foreach ($results as $key => $value){
         print_r($value->art_tag);
            exit;
        }





        var_dump($results);
        var_dump($queries);
        exit;
        $user = User::first();
      echo "<pre>";
      print_r($user->user_name);


    }



    //
}
