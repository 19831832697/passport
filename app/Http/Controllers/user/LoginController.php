<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * 登录执行接口
     * @param Request $request
     * @return false|string
     */
    public function login(Request $request)
    {
//        $dataInfo = file_get_contents("php://input");
//        $data = base64_decode($dataInfo);
//        $method = "AES-256-CBC";
//        $option = OPENSSL_RAW_DATA;
//        $key = "123wer";
//        $iv = "12345tgvfred2346";
//        $arr = openssl_decrypt($data, $method, $key, $option, $iv);
//        $arrInfo = json_decode($arr, true);
        $arrInfo=$request->input();
        $user_name = $arrInfo['user_name'];
        $user_pwd = $arrInfo['user_pwd'];
        $userInfo = DB::table('user')->where('user_name', $user_name)->first();
        if ($userInfo) {
            $user_id = $userInfo->user_id;
            if (password_verify($user_pwd, $userInfo->user_pwd)) {
                $token = $this->token($user_id);
                setcookie("token", $token, time()+3600, "/", "1809a.com");
                setcookie("user_id", $user_id, time()+3600, "/", "1809a.com");
                $info = DB::table('user')->where('user_name', $user_name)->first();
                if ($info) {
                    DB::table('user')->where('user_name', $user_name)->update(['token' => $token]);
                }
                $user_key = "user_list" . $user_id;
                Redis::set($user_key, $token);
                Redis::expire($user_key, 120);
                $res = [
                    'code' => 1,
                    'msg' => '登录成功',
                ];
                return json_encode($res, JSON_UNESCAPED_UNICODE);
            } else {
                $u_key = "fail_list";
                $ip = $_SERVER['REMOTE_ADDR'];
                Redis::set($u_key, $ip);
                $num = Redis::incr($ip);
                Redis::set($u_key, $num);
                if ($num >= 5) {
                    echo "调用频繁，请稍后重试";
                }
                Redis::expire($u_key, 60);
                $res = [
                    'code' => 2,
                    'msg' => '密码错误'
                ];
                return json_encode($res, JSON_UNESCAPED_UNICODE);
            }
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
}