<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Actinfo extends Model
{

    protected $table = 'game_wx.hongbao_info'; //不制定默认读类名的复数形式
    public $timestamps=false;  //默认时间字段关闭
    public $connection ='mysql2'; // 链接到db2
    protected $guarded=[];


}
