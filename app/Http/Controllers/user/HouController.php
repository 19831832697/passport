<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HouController extends Controller
{
    /**
     * 后台展示
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function hou(){
        $dataInfo=DB::table('user')->get()->toArray();
        return view('hou',['dataInfo'=>$dataInfo]);
    }

    /**
     * 设置过期时间
     * @param Request $request
     */
    public function usertime(Request $request){
        $user_id=session('user_id');
        $key="user_list".$user_id;
        $time=$request->input('time');
        $info=Redis::get($key);
        if($info){
            $cc=Redis::expire($key,$time);
            if($cc){
                $res=[
                    'code'=>1,
                    'msg'=>'设置成功'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $res=[
                'code'=>2,
                'msg'=>'设置失败'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }
}
