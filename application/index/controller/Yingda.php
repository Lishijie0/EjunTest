<?php
namespace app\index\controller;
use think\Controller;
use think\Cache;
use think\Db; 
use qrcode\qrcode; 
class Yingda extends Controller//应大专项开发类
{
    public function getStore()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 

        $sql = "select b.id,b.storename from syscompany a,defstore b where a.id = b.cid and a.companycode = '160101'";
        $arr = Db::query($sql);
        if ($arr) {
            $data['code'] = '200';
            $data['msg'] = '店仓获取成功';
            $data['data'] = $arr;
        } else {
            $data['code'] = '201';
            $data['msg'] = '店仓获取为空';
            $data['data'] = '';            
        }
        return json($data);        
    }

    //获取店铺二维码
    public function getStoreQrcode()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        // header('content-type:image/png');  //设置gif Image
        // 启用缓冲区
        $storeid = input('post.storeid'); //获取店仓ID
        if (!$storeid) {
            $data['code'] = '201';
            $data['msg'] = '店仓未获取到，请重新获取';
            return json($data);            
        }
        ob_start();
        $url = urldecode("http://116.ebosserp.com/kljy/yingda/index.html?storeid=$storeid");
        $png = QRcode::png($url,false,"L",16,1);
        $imageString = base64_encode(ob_get_contents()); //ob_get_contents() 得到缓冲区的数据
        $png = "data:image/jpg;base64,".$imageString;
        ob_end_clean();
        if ($png) {
            $data['code'] = '200';
            $data['msg'] = '二维码生成成功';
            $data['data'] = $png;
        } else {
            $data['code'] = '201';
            $data['msg'] = '二维码生成失败';
            $data['data'] = '';            
        }
        return json($data);
    }
    /*权限可见店仓*/
    public function getStyle()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 
        $sku = input('post.sku'); //获取sku
        $storeid = input('post.storeid'); //获取店仓ID
        if (!$sku) {
            $data['code'] = '200';
            $data['msg'] = '条码不能为空';
            $data['data'] = ''; 
            return json($data);        
        }
        //getrprice2(店铺ID,商品ID,当前时间)  查询实时调价
        $sql = "SELECT d.image1_path,d.style,d.stylename,getrprice2($storeid,d.id,to_char(sysdate,'yyyymmdd')) price
                from defskuview a,busretailadj b,busretailadjdt c,defstyle d
                where a.styleid = c.styleid and b.id = c.masterid and a.styleid = d.id 
                and a.sku = '$sku'
                group by d.image1_path,d.style,d.stylename,getrprice2($storeid,d.id,to_char(sysdate,'yyyymmdd'))";
        /*"SELECT d.image1_path,d.style,d.stylename,case when c.fprice is not null then c.fprice else d.dprice end price
                from defskuview a,busretailadj b,busretailadjdt c,defstyle d 
                where a.styleid = c.styleid and b.id = c.masterid and a.styleid = d.id and a.sku = '$sku'
                group by d.image1_path,d.style,d.stylename,d.dprice,c.fprice";*/
        $arr = Db::query($sql);
        if (array_not_null($arr)) {
            $arr[0]['IMAGE1_PATH'] = img_path($arr[0]['IMAGE1_PATH']);
            $data['code'] = '200';
            $data['msg'] = '查询成功';
            $data['data'] = $arr[0];
        } else {
            $data['code'] = '201';
            $data['msg'] = '查询失败，请重新扫码';
            $data['data'] = '';            
        }
        return json($data);
	}


    /**
     * 获取微信 ticket
     * @return [type] [description]
     */
    public function getTicket()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        $url = input('post.url'); // 获取访问的html  
        // $res = $this -> Table -> name('crm_access_token') 
        //                       -> order('access_token_last_time desc')
        //                       -> limit(1)
        //                       -> find();
        $access_token_arr = Cache::get('access_token');  //从缓存获取token
        //非 判断token是否存在 
        if(!$access_token_arr){
            $access_token_arr = get_access_token();
        }
            // dump(Cache::get('ticket'));die;
        if (!Cache::get('ticket')) {
            Cache::rm('ticket'); //  需要先清除缓存
            $weixin_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token_arr['access_token'].'&type=jsapi';
            $apisdk = json_decode(https_request($weixin_url),true);    // 获取apisdk  转为数组
            // dump($apisdk);die;
            if ($apisdk['errmsg'] == 'ok') {               
                Cache::set('ticket',$apisdk['ticket'],3600);     //缓存
                Cache::set('ticket_createtime',date('Y-m-d H:i:s',time()),0);     //缓存
                $ticket = $apisdk['ticket'];  //赋值
            } else {
                $data['code'] = '201';
                $data['msg'] = 'access_token过期,请重试';
                return json($data);
            }

        } else {
            $ticket = Cache::get('ticket'); //缓存            
        }
        if ($ticket) {
            $data['code'] = '200';
            $data['msg'] = 'apiSDK获取成功';
            $data['data'] = $this -> addSign(config('appId'),$ticket,$url);            
        } else {            
            $data['code'] = '201';
            $data['msg'] = 'apiSDK获取失败';
            $data['data'] = '';
        }
        return json($data);
    }

    /**
     * 加入验证
     * @param unknown $ticket
     * @param unknown $url
     * @return multitype:unknown string NULL number
     */
    private function addSign($appid,$ticket, $url)
    {
        // 生成时间戳
        $timestamp = time();
        // 生成随机字符串
        $nonceStr = $this->createNoncestr();
        $array = array("noncestr" => $nonceStr, "jsapi_ticket" => $ticket, "timestamp" => $timestamp, "url" => $url);
        ksort($array);
        $signPars = "";

        foreach ($array as $k => $v ) {
                if (("" != $v) && ("sign" != $k)) {
                        if ($signPars == "") {
                                $signPars .= $k . "=" . $v;
                        }
                        else {
                                $signPars .= "&" . $k . "=" . $v;
                        }
                }
        }

        $result = array("appId" => $appid, "timestamp" => $timestamp, "nonceStr" => $nonceStr, "signature" => SHA1($signPars), "jsApiList" => array(0 => 'onMenuShareAppMessage'));
        return $result;
    }


 
    // 创建随机字符串
    private function createNoncestr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i ++) {
            $str .= substr ( $chars, mt_rand ( 0, strlen ( $chars ) - 1 ), 1 );
        }
        return $str;
    }
}	