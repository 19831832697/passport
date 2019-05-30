<?php

namespace App\Http\Controllers\login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * 注册执行
     * @param Request $request
     * @return false|string
     */
    public function reg_do(Request $request){
        $arrInfo=file_get_contents("php://input");
        $dataInfo=base64_decode($arrInfo);
//        var_dump($dataInfo);
        $k=openssl_get_publickey("file://".storage_path('app/key/pub.pem'));
        openssl_public_decrypt($dataInfo,$dec_data,$k);
        $data=json_decode($dec_data,true);
        $user_name=$data['user_name'];
        $userPwd=$data['user_pwd'];
        $user_email=$data['user_email'];
        $user_pwd=password_hash($userPwd,PASSWORD_BCRYPT);
        if(empty($user_name)){
            $res=[
                'code'=>1,
                'msg'=>'用户名不能为空'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
        if(empty($userPwd)){
            $res=[
                'code'=>1,
                'msg'=>'密码不能为空'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
        if(empty($user_email)){
            $res=[
                'code'=>1,
                'msg'=>'邮箱不能为空'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
        $arrInfo=DB::table('user')->where('user_email',$user_email)->first();
        if($arrInfo){
            $user_email=$arrInfo->user_email;
            if($user_email){
                $res=[
                    'code'=>1,
                    'msg'=>'该邮箱已被注册'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }
        }
        $dataInfo=[
            'user_name'=>$user_name,
            'user_pwd'=>$user_pwd,
            'user_email'=>$user_email
        ];
        $res=DB::table('user')->insert($dataInfo);
        if($res){
            $arr=[
                'code'=>0,
                'msg'=>'注册成功'
            ];
            return json_encode($arr,JSON_UNESCAPED_UNICODE);
        }else{
            $arr=[
                'code'=>1,
                'msg'=>'注册失败'
            ];
            return json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 登录执行
     * @param Request $request
     * @return false|string
     */
    public function login_do(Request $request){
        $data=$request->input();
        $user_name=$data['user_name'];
        $user_pwd=$data['user_pwd'];
        $dataInfo=DB::table('user')->where('user_name',$user_name)->first();
        if($dataInfo){
            $pwd=$dataInfo->user_pwd;
            $user_id=$dataInfo->user_id;
            if(password_verify($user_pwd,$pwd)){
                $key="login_list";
//                $token=$this->token($user_id);
//                $token_login=$token.$user_id;
                Redis::set($key,1);
                $res=[
                    'code'=>1,
                    'msg'=>'登录成功'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }else{
                $res=[
                    'code'=>2,
                    'msg'=>'密码错误'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }
        }else{
            $res=[
                'code'=>2,
                'msg'=>'没有此账号'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 生成token
     * @param $user_id
     * @return bool|string
     */
    public function token($user_id){
        return substr(Str::random(11).md5($user_id),5,15);
    }

    /**
     * 登录视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login(){
        return view('login');
    }

    /**
     * 登录执行
     */
    public function loginDo(Request $request){
        $dataInfo=$request->input();
        $user_name=$dataInfo['user_name'];
        $user_pwd=$dataInfo['user_pwd'];
        $dataInfo=DB::table('user')->where('user_name',$user_name)->first();
        if($dataInfo) {
            $pwd = $dataInfo->user_pwd;
            $user_id = $dataInfo->user_id;
            if (password_verify($user_pwd, $pwd)) {
                $key = "login_list";
                Redis::set($key,2);
                $res = [
                    'code' => 1,
                    'msg' => '登录成功'
                ];
                return json_encode($res, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * 互踢
     */
    public function come(){
        $key="login_list";
        $num=Redis::get($key);
        if($num==2){
            $res=[
                'code'=>40020,
                'msg'=>'欢迎登陆'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }else if($num==1){
            $res=[
                'code'=>40021,
                'msg'=>'您的账号在另一端登录,强制下线'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }
}