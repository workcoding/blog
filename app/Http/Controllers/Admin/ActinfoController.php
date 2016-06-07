<?php

namespace App\Http\Controllers\Admin;
use App\Http\Model\Actinfo;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActinfoController extends CommonController
{

    public function index()
    {

        $data = Actinfo::orderBy('id', 'desc')->take(2)->paginate(7);
//      $data = DB::connection('mysql2')->table('game_wx.hongbao_info')->orderby('id')->paginate(7);

        return view('admin.actinfo.index', compact('data'));
    }


    public function create()
    {

        return view('admin.actinfo.add');
    }


    public function store()
    {
        $input = Input::except('_token');
        //参数处理
        $title = $input['title'];
        $stm = $input['start_time'];
        $etm = $input['end_time'];
        $content = $input['content'];
        $countNum = $input['countNum'];
        $all_num = $input['all_num'];
        $num_1 = $input['num_1'];
        $num_2 = $input['num_2'];
        $num_3 = $input['num_3'];
        $award = array();


        $rules = [
            'title' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'content' => 'required',
            'countNum' => 'required|numeric',
            'all_num' => 'required|numeric',
            'num_1' => 'required|numeric',
            'num_2' => 'required|numeric',
            'num_3' => 'required|numeric',
        ];

        $message = [
            'title.required' => '文章名称不能为空！',
            'start_time.required.date' => '开始时间不能为空！',
            'end_time.required.date' => '结束时间不能为空！',
            'content.required' => '备注不能为空！',
            'countNum.required' => '显示总值不能为空！',
            'all_num.required' => '红包总值不能为空！',
            'num_1.required' => '1~3权重不能为空！',
            'num_2.required' => '3~5权重不能为空！',
            'num_3.required' => '5~10权重不能为空！',
        ];

        $validator = Validator::make($input, $rules, $message);
        if (!$validator->passes()) {
            return back()->withErrors($validator);
        }


        $stm_time = strtotime($stm);
        $etm_time = strtotime($etm);
        if ($stm_time >= $etm_time && !empty($stm_time) && !empty($etm_time)) {

            return back()->with('errors', '开始时间和结束时间有问题');


        }
        if (!((0 <= $num_1 && $num_1 <= 1) && (0 <= $num_2 && $num_2 <= 1) && (0 <= $num_3 && $num_3 <= 1))) {

            return back()->with('errors', '1~3 3~5 5~10权重必须是小数');

        }


        $max_prize = 10;
        if (empty($num_3)) {
            $max_prize = 5;
        }
        if (empty($num_3) && empty($num_2)) {
            $max_prize = 3;
        }

        $hbConfig = array('all_num' => $all_num, 'max_prize' => $max_prize);
        if (!empty($num_1)) {
            $hbConfig['num'][0] = array('num_weight' => $num_1, 'num_start' => 1, 'num_end' => 3, 'count' => 0);
        }
        if (!empty($num_2)) {
            $hbConfig['num'][1] = array('num_weight' => $num_2, 'num_start' => 3, 'num_end' => 5, 'count' => 0);
        }
        if (!empty($num_3)) {
            $hbConfig['num'][2] = array('num_weight' => $num_3, 'num_start' => 5, 'num_end' => 10, 'count' => 0);
        }
        $i = 0;
        foreach ($award as $val) {
            $hbConfig['large_prize'][$i]['num'] = $val['num'];
            $hbConfig['large_prize'][$i]['count'] = $val['count'];
            $i++;
        }
        $num_list = $this->getRandNumUpgrades($hbConfig);

        $data = array(
            'num_list' => $num_list,
            'title' => $title,
            'content' => $content,
            'countNum' => $countNum,
            'type' => 'KUBI',
            'start_time' => strtotime($stm),
            'end_time' => strtotime($etm),
            'status' => '1',
            'create_time' => time()
        );


        if (!$this->addkuInfo($data)) {
            return back()->with('errors', '添加失败，系统异常');
        } else {
            return redirect('admin/actinfo');
        }

    }

    /**
     * 得到奖品目录
     */
    public function getRandNumUpgrades($hbConfig)
    {
        $all_num = intval($hbConfig['all_num']) * 100;
        $max_prize = intval($hbConfig['max_prize']) * 100;
        $res_num_list = array();
        $tmp_area = array();
        $tmp_area_weight = 0;
        if (!empty($hbConfig['large_prize'])) {
            foreach ($hbConfig['large_prize'] as $key => $value) {
                # code...
                $res_num_list[$value['num'] * 100] = $value['count'];
                $all_num = $all_num - $value['count'] * 100 * $value['num'];
            }
        }
        if (!empty($hbConfig['num'])) {
            foreach ($hbConfig['num'] as $key => $value) {
                # code...
                $tmp_area[$key] = $tmp_area_weight + $value['num_weight'] * 100;
                $tmp_area_weight = $tmp_area[$key];
            }
            while (true) {
                if ($all_num == 0) {
                    //echo "分完了<br />";
                    break;
                }
                if ($max_prize >= $all_num) {
                    //剩余量小于奖金最大值时直接将剩余量作为一个奖品
                    $res_num_list[$all_num] = isset($res_num_list[$all_num]) ? ($res_num_list[$all_num] + 1) : 1;
                    //echo "剩余量小于奖金最大值时直接将剩余量作为一个奖品<br />";
                    break;
                }
                $tmp_rand = rand(1, 100);
                $rand_area = 0;
                foreach ($tmp_area as $key => $value) {
                    # code...
                    if ($key == 0) {
                        if ($tmp_rand > 0 && $tmp_rand <= $value) {
                            $rand_area = $key;
                            break;
                        }
                    } else {
                        if ($tmp_rand > $tmp_area[$key - 1] && $tmp_rand <= $value) {
                            $rand_area = $key;
                            break;
                        }
                    }
                }
                $num_rand = rand($hbConfig['num'][$rand_area]['num_start'] * 100 + 1, $hbConfig['num'][$rand_area]['num_end'] * 100);
                $res_num_list[$num_rand] = isset($res_num_list[$num_rand]) ? ($res_num_list[$num_rand] + 1) : 1;
                $all_num = $all_num - $num_rand;
            }
        }
        return $res_num_list;
    }


    //添加酷币入库  已经屏蔽
    public function addkuInfo($data)
    {


        $static = true;
        $num_list = $data['num_list'];
        //消掉没有数据库中的字段
        unset($data['num_list']);
        //插入主表中

//        $parent_id=Actinfo::create($data);
        $parent_id = DB::connection('mysql2')->table('game_wx.hongbao_info')->insertGetId(
            ['title' => $data['title'], 'content' => $data['content'], 'countNum' => $data['countNum'], 'type' => $data['type'], 'start_time' => $data['start_time'], 'end_time' => $data['end_time'], 'status' => $data['status'], 'create_time' => $data['create_time']]);

        //判断是否插入成功
        if (empty($parent_id)) {
            return false;
        }
        //插入pt_toAppid_standalone表中
        foreach ($num_list as $key => $val){
            if (!empty($val)) {
                DB::connection('mysql2')->table('game_wx.prize_kubi_new')->insertGetId(
                    ['num' => ($key / 100), 'count' => $val, 'hongbao_id' => $parent_id, 'all_count' => $val]);
            }
        }
        return $static;
    }


}
