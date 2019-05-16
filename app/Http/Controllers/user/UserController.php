<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * 注册passport
     * @return false|string
     */
   public function register(){
       $dataInfo=file_get_contents("php://input");
       $data=json_decode($dataInfo,true);
//       var_dump($data);die;
       $user_name=$data['user_name'];
       $user_email=$data['user_email'];
       $user_pwd=$data['user_pwd'];

       if(empty($user_name)) {
           $res = [
               'code' => 40002,
               'msg' => '用户名不能为空'
           ];
           return json_encode($res, JSON_UNESCAPED_UNICODE);
       }
       if(empty($user_pwd)){
           $res=[
               'code'=>40002,
               'msg'=>'密码不能为空'
           ];
           return json_encode($res,JSON_UNESCAPED_UNICODE);
       }
       $where=[
           'user_email'=>$user_email
       ];
       $dataInfo=DB::table('register')->where($where)->first();
       if($dataInfo){
           $res=[
               'code'=>40002,
               'msg'=>'此邮箱已存在'
           ];
           return json_encode($res,JSON_UNESCAPED_UNICODE);
       }
       $arrInfo=[
           'user_name'=>$user_name,
           'user_email'=>$user_email,
           'user_pwd'=>password_hash($user_pwd,PASSWORD_BCRYPT)
       ];
       $arr=DB::table('register')->insert($arrInfo);
       if($arr){
           $res=[
               'code'=>200,
               'msg'=>'注册成功'
           ];
           return json_encode($res,JSON_UNESCAPED_UNICODE);
       }else {
           $res = [
               'code' => 40002,
               'msg' => '注册失败'
           ];
           return json_encode($res, JSON_UNESCAPED_UNICODE);
       }
   }

    /**
     * 登录通行
     * @return false|string
     */
   public function login(){
       $dataInfo=file_get_contents("php://input");
       $data=json_decode($dataInfo,true);
       $user_name=$data['user_name'];
       $user_pwd=$data['user_pwd'];
       $where=[
           'user_name'=>$user_name
       ];
       if(empty($user_name)){
           $res=[
               'code'=>40020,
               'msg'=>'用户名不能为空'
           ];
           return json_encode($res,JSON_UNESCAPED_UNICODE);
       }
       $dataInfo=DB::table('register')->where($where)->first();
       if($dataInfo){
           $user_name=$dataInfo->user_name;
           $user_id=$dataInfo->user_id;
           if(password_verify($user_pwd,$dataInfo->user_pwd)){
               $token=$this->token($user_name,$user_pwd);
               $key="token$user_id";
               Redis::set($key,$token);
               Redis::expire($key,60*60*24*7);
               $res=[
                   'code'=>200,
                   'msg'=>'登录成功',
                   'user_id'=>$user_id,
                   'token'=>$token
               ];
               return json_encode($res,JSON_UNESCAPED_UNICODE);
           }else{
               $res=[
                   'code'=>40020,
                   'msg'=>'账号或密码错误'
               ];
               return json_encode($res,JSON_UNESCAPED_UNICODE);
           }
       }else{
           $res=[
               'code'=>40020,
               'msg'=>'没有此账号'
           ];
           return json_encode($res,JSON_UNESCAPED_UNICODE);
       }
   }

    /**
     * 生成token
     * @param $user_id
     * @param $user_name
     * @return string
     */
    public function token($user_id,$user_name){
        return substr(sha1(Str::random(11).md5($user_name)),5,15)."user_id".$user_id;
    }

    /**
     * 个人中心
     * @return false|string
     */
    public function center(){
        $user_id=file_get_contents("php://input");
        $where=[
            'user_id'=>$user_id
        ];
        $dataInfo=DB::table('register')->where($where)->first();
        $data=json_encode($dataInfo);
        return $data;
    }
}
