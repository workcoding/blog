@extends('layouts.admin')
@section('content')
        <!--面包屑导航 开始-->
<div class="crumb_warp">
    <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
    <i class="fa fa-home"></i> <a href="{{url('admin/info')}}">首页</a> &raquo; 活动管理
</div>
<!--面包屑导航 结束-->

<!--结果集标题与导航组件 开始-->
<div class="result_wrap">
    <div class="result_title">
        <h3>添加活动</h3>
        @if(count($errors)>0)
            <div class="mark">
                @if(is_object($errors))
                    @foreach($errors->all() as $error)
                        <p>{{$error}}</p>
                    @endforeach
                @else
                    <p>{{$errors}}</p>
                @endif
            </div>
        @endif
    </div>

</div>
<!--结果集标题与导航组件 结束-->

<div class="result_wrap">
    <form action="{{url('admin/actinfo')}}" method="post">
        {{csrf_field()}}
        <table class="add_tab">
            <tbody>

            <tr>
                <th><i class="require">*</i> 活动标题：</th>
                <td>
                    <input type="text" class="lg" name="title">
                </td>
            </tr>
            <tr>
                <th>活动开始时间：</th>
                <td>
                    <input type="text" class="lg" name="start_time">
                </td>
            </tr>
            <tr>
                <th>活动结束时间：</th>
                <td>
                    <input type="text" class="lg" name="end_time">
                </td>
            </tr>
            <tr>
                <th>备注：</th>
                <td>
                    <input type="text" class="lg" name="content">
                </td>
            </tr>
            <tr>
                <th>显示总额：</th>
                <td>
                    <input type="text" class="lg" name="countNum">
                </td>
            </tr>
            <tr>
                <th>红包总值：</th>
                <td>
                    <input type="text" class="lg" name="all_num">
                </td>
            </tr>
            <tr>
                <th>1~3元权重：</th>
                <td>
                    <input type="text" class="sm" name="num_1">
                </td>
            </tr>
            <tr>
                <th>3~5元权重：</th>
                <td>
                    <input type="text" class="sm" name="num_2">
                </td>
            </tr>
            <tr>
                <th>5~10元权重</th>
                <td>
                    <input type="text" class="sm" name="num_3">
                </td>
            </tr>

                <th></th>
                <td>
                    <input type="submit" value="提交">
                    <input type="button" class="back" onclick="history.go(-1)" value="返回">
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

@endsection
