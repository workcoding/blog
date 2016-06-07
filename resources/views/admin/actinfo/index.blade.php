@extends('layouts.admin')
@section('content')
        <!--面包屑导航 开始-->
<div class="crumb_warp">
    <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
    <i class="fa fa-home"></i> <a href="{{url('admin/info')}}">首页</a> &raquo; 活动管理
</div>
<!--面包屑导航 结束-->

<!--搜索结果页面 列表 开始-->
<form action="#" method="post">
    <div class="result_wrap">
        <!--快捷导航 开始-->
        <div class="result_title">
            <h3>活动信息列表</h3>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/actinfo/create')}}"><i class="fa fa-plus"></i>添加活动</a>
            </div>
        </div>
        <!--快捷导航 结束-->
    </div>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc">ID</th>
                    <th>活动标题</th>
                    <th>活动类型</th>
                    <th>活动开始时间</th>
                    <th>活动结束时间</th>
                    <th>备注</th>
                    <th>创建时间<th>
                </tr>
                @foreach($data as $v)
                <tr>
                    <td class="tc">{{$v->id}}</td>
                    <td>
                        <a href="#">{{$v->title}}</a>
                    </td>
                    <td>{{$v->type}}</td>
                    <td>{{date('Y-m-d H:i:s',$v->start_time)}}</td>
                    <td>{{date('Y-m-d H:i:s',$v->end_time)}}</td>
                    <td>{{$v->content}}</td>
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
