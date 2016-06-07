@extends('layouts.admin')
@section('content')
        <!--面包屑导航 开始-->
<div class="crumb_warp">
    <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
    <i class="fa fa-home"></i> <a href="{{url('admin/info')}}">首页</a> &raquo; 玩家管理
</div>
<!--面包屑导航 结束-->

<!--搜索结果页面 列表 开始-->
<form action="#" method="post">
    <div class="result_wrap">
        <!--快捷导航 开始-->
        <div class="result_title">
            <h3>用户信息列表</h3>
        </div>

        <!--快捷导航 结束-->
    </div>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc">微信ID</th>
                    <th>微信用户名</th>
                    <th>微信手机</th>
                    <th>微信openid</th>
                    <th>微信地址</th>
                    <th>时间</th>
                </tr>
                @foreach($data as $v)
                <tr>
                    <td class="tc">{{$v->userid}}</td>
                    <td>
                        <a href="#">{{$v->user_name}}</a>
                    </td>
                    <td>{{$v->phone}}</td>
                    <td>{{$v->open_id}}</td>
                    <td>{{$v->province}}</td>
                    <td>{{date('Y-m-d H:i:s',$v->create_time)}}</td>

                </tr>
                @endforeach
            </table>

            <div class="page_list">
                {{$data->links()}}
            </div>
        </div>
    </div>
</form>
<!--搜索结果页面 列表 结束-->

<style>
    .result_content ul li span {
        font-size: 15px;
        padding: 6px 12px;
    }
</style>

@endsection
