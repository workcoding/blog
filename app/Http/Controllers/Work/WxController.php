<?php

namespace App\Http\Controllers\Work;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Model\Actinfo;

class WxController extends Controller
{
    //抢红包活动
    public function index()
    {


        $starttime = self::microtime_float();


        sleep(2);
        $user['userId'] = rand(1, 50);
        Log::info('抢红包用户:' . $user['userId']);
        $time = time();
//      DB::connection('mysql2')->enableQueryLog();
        $curHb = DB::connection('mysql2')->table('game_wx.hongbao_info')->where('start_time', '<', $time)->where('end_time', '>', $time)->where('status', '=', 1)->orderBy('id', 'desc')->first();         //当前进行的红包活动  DB方式
        //ORM方式
       // $curHb = Actinfo::where('start_time', '<', $time)->where('end_time', '>', $time)->where('status', '=', 1)->orderBy('id', 'desc')->count();

//        echo "<pre>";
//        print_r($curHb);
//        exit;


        $nextHb = DB::connection('mysql2')->table('game_wx.hongbao_info')->where('start_time', '>', $time)->where('status', '=', 1)->orderBy('start_time', 'asc')->take(1)->get();//下一期活动

        //抢过红包
        if ($hb = $this->checkUserHb($curHb->id, $user['userId'])) {
            Log::warning('用户ID:' . $user['userId'] . ' 抢过红包了.');
            echo "抢过红包了";
            exit;
        } else if (!($hongbao_prize = $this->getKbHb($curHb->id, $curHb->type, $user['userId']))) {
            //未抢红包
            Log::warning('用户ID:' . $user['userId'] . ' 更新失败.' . '参数:' . json_encode($curHb));
            echo "更新失败";
            exit;
        } elseif (!$hbId = $this->snatchHb($user['userId'], $curHb->id, $hongbao_prize['id'])) {
            //未抢红包
            Log::warning('用户ID:' . $user['userId'] . ' 插入失败.' . '参数1:' . json_encode($curHb) . '参数2:' . json_encode($hongbao_prize));
            echo "插入失败";
            exit;
        } else {
            $prize = $this->getHbPrizeByRecord($user['userId'], $curHb->id);
            $message = "微信抢红包充值测试_" . $user['userId'];
            $pay = $this->payByRpc($user['userId'], $user['userId'], $prize->num, $message);
            Log::warning('用户ID:' . $user['userId'] . '充值成功 ' . '参数1:' . json_encode($pay) . '参数2:' . json_encode($prize));
        }

        if($pay){

            $runtime = number_format((self::microtime_float() - $starttime),4).'s';
            Log::info('充值成功：用户ID为:' . $user['userId'].'充值金额:' .$prize->num.'运行时间:' .$runtime);
            echo "success".$runtime;
            exit;

        }else{

            $runtime = number_format((self::microtime_float() - $starttime),4).'s';
            Log::info('充值失败：用户ID为:' . $user['userId'].'充值金额:' .$prize->num.'运行时间:' .$runtime);
            echo 'faild'.$runtime;
            exit;
        }

    }


    /**
     *获取红包奖品
     */
    public function getHbPrizeByRecord($uid, $HbId)
    {
        $uinfo = DB::connection('mysql2')->table('game_wx.prize_kubi_new as a')
            ->join('game_wx.hongbao_record as b', 'a.id', '=', 'b.prize_id')
            ->select('a.*')
            ->first();
        return $uinfo;
    }

    public function payByRpc($wxUid, $duoKuUid, $amount, $message)
    {

        $wxUid = intval($wxUid);
        $duoKuUid = intval($duoKuUid);
        $amount = intval($amount) * 100;
        $create_time = time();


        $rs = DB::connection('mysql2')->table('game_wx.pay_record')->insertGetId(
            ['uid' => $wxUid, 'amount' => $amount, 'res' => '接口返回值', 'create_time' => $create_time, 'message' => $message]
        );
        return $rs;

    }


    public function snatchHb($uid, $HbId, $prizeId)
    {

        $time = time();
        $rs = DB::connection('mysql2')->table('game_wx.hongbao_record')->insert(
            ['uid' => $uid, 'hongbao_id' => $HbId, 'create_time' => $time, 'prize_id' => $prizeId]
        );
        return $rs;


    }


    public function getKbHb($HbId, $prizeType, $uid, $lotteryId = 738)
    {
        $HbId = intval($HbId);
        $uid = intval($uid);
        $lotteryId = intval($lotteryId);
        if ($prizeType == "KUBI") {
            $prizes = $this->getPrizeKubiByHb($HbId);
            if (!empty($prizes)) {
                $prize = self::getRandPrize($prizes);
                if (!$prize) {
                    return false;
                } else {
                    $up_rs = DB::connection('mysql2')->table('game_wx.prize_kubi_new')
                        ->where('id', $prize['id'])
                        ->where('count', '>', 0)
                        ->update(['count' => `count` - 1]);
                    if (!$up_rs) {
                        return false;
                    } else {
                        return $prize;
                    }
                }
            }
        }


    }


    /**
     *随机取值
     */
    public static function getRandPrize($prizes)
    {

        $prizes = json_encode($prizes);
        $prizes = json_decode($prizes, true);
        $source = array();
        $res = array();
        $max = 0;
        if (!empty($prizes)) {
            foreach ($prizes as $key => $value) {
                # code...
                $next = $max;
                $max += $value['count'];
                $source[$max]['value'] = $value;
                $source[$max]['next'] = $next;
            }

            $rand = rand(1, $max);
            foreach ($source as $key => $value) {
                # code...
                if ($rand > $value['next'] && $rand <= $key) {
                    $res = $value['value'];
                }
            }


        } else {
            return false;
        }

        return $res;
    }


    /**
     *获取红包奖品
     */
    public function getPrizeKubiByHb($HbId)
    {

        $uinfo = DB::connection('mysql2')->table('game_wx.prize_kubi_new')->where('hongbao_id', '=', $HbId)->where('count', '>', 0)->get();//下一期活动
        return $uinfo;

    }


    public function checkUserHb($curHbid, $uid)
    {
        $uinfo = DB::connection('mysql2')->table('game_wx.hongbao_record')->where('hongbao_id', '=', $curHbid)->where('uid', '=', $uid)->first();//下一期活动
        return $uinfo;
    }



    //获取脚本运行时间
    public static function microtime_float()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }


    //
}
