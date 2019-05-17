<?php

namespace App\Http\Controllers\goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    /**
     * 加入购物车
     * @return false|string
     */
    public function cart(){
        $dataInfo=file_get_contents("php://input");
        $data=json_decode($dataInfo,true);

        $goods_id=$data['goods_id'];
        $user_id=$data['user_id'];

        if(empty($user_id)){
            $res=[
                'code'=>40005,
                'msg'=>'请先登录'
            ];
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }

        $where=[
            'goods_id'=>$goods_id
        ];
        $dataInfo=DB::table('shop_goods')->where($where)->first();
        if($dataInfo){
            $goods_name=$dataInfo->goods_name;
            $goods_num=$dataInfo->goods_num;
            if($goods_num==0){
                $res=[
                    'code'=>40005,
                    'msg'=>'库存不足'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }
            $whereInfo=[
                'user_id'=>$user_id,
                'goods_id'=>$goods_id
            ];
            $arrInfo=DB::table('shop_cart')->where($whereInfo)->first();
            $num=1;
            if($arrInfo){
                $status=$arrInfo->status;
                if($status==2){
                    DB::table('shop_cart')->where($whereInfo)->update(['status'=>1]);
                }
                $buy_number=$arrInfo->buy_num;
                $buy_num=$buy_number+$num;
                $updateInfo=[
                    'buy_num'=>$buy_num
                ];
                $arr=DB::table('shop_cart')->where($whereInfo)->update($updateInfo);
            }else{
                $data=[
                    'goods_name'=>$goods_name,
                    'goods_id'=>$goods_id,
                    'user_id'=>$user_id,
                    'status'=>1,
                    'buy_num'=>1,
                    'create_time'=>time(),
                ];
                $arr=DB::table('shop_cart')->insert($data);
            }
            if($arr){
                $res=[
                    'code'=>200,
                    'msg'=>'加入购物车成功'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }else{
                $res=[
                    'code'=>40005,
                    'msg'=>'未能加入购物车'
                ];
                return json_encode($res,JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
