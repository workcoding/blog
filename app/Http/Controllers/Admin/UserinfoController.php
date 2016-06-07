<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserinfoController extends Controller
{
    //  全部用户信息
    public function index()
    {
        //$data = DB::connection('mysql2')->select('select * from game_wx.hb_user_info order by create_time desc')->paginate(15);

        $data = DB::connection('mysql2')->table('game_wx.hb_user_info')->paginate(7);
        //$data = Article::orderBy('art_id','desc')->paginate(10);
        return view('admin.userinfo.index',compact('data'));
    }


    //
}
