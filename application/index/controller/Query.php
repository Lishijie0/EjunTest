<?php
namespace app\index\controller;
use think\Cache;
class Query extends Common//销售分析
{
    /*权限可见店仓*/
    public function getStore()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }	
		$cid = $this -> Token['CID']; 
		$userid = $this -> Token['USERID']; 
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
		$data = $this -> Query -> getStore($cid,$userid,$orcl);
		return json($data);
	}


    /*着装顾问*/
    public function getEmp()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }   
        $cid = $this -> Token['CID']; 
        $storeid = input('post.storeid');  //店铺id
        $storeid = !empty($storeid) ? $storeid : $this -> Token['STOREID'];
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
        // dump($storeid);die;
        $emp = $this -> Query -> getEmpList($cid,$storeid,$orcl);
        return json($emp);

    }

    /*会员卡类型*/
    public function getVipCard ()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }   
        $cid = $this -> Token['CID']; 
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
        // dump($storeid);die;
        $card = $this -> Query -> getVipCard($cid,$orcl);
        return json($card);

    }

    /*标签组*/
    public function getTags () 
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }   
        $cid = $this -> Token['CID']; 
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
        // dump($storeid);die;
        $tags = $this -> Query -> getTags($cid,$orcl);
        return json($tags);

    }
    /**
     * 添加会员->推荐人查找
     * @return [type] [description]
     */
    public function getVip()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }   
        $cid = $this -> Token['CID']; 
        $storeid = $this -> Token['STOREID']; 
        $keywords = input('post.keywords'); // 手机号 or  姓名 or 卡号
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
        $data = $this -> Query -> getVip($cid,$storeid,$keywords,$orcl);      
        return json($data);
    }
    //        $status1 = $this -> Sql -> getStatusDetail('FLOW_回访方式');
    //    $status2 = $this -> Sql -> getStatusDetail('FLOW_回访类型');
    /**
     * 获取回访方式
     * @return [type] [description]
     */
    public function getVisitedWay()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }           
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
        $visitedWay = $this -> Sql -> getStatusDetail('FLOW_回访方式','',$orcl);
        if (array_not_null($visitedWay)) {
            foreach ($visitedWay as $k => &$v) {
                unset($v['ID'],$v['STATUS']);
            }
            $data['code'] = '200';
            $data['msg'] = '回访方式获取成功';
            $data['data'] = $visitedWay;
        } else {
            $data['code'] = '201';
            $data['msg'] = '回访方式获取失败';
            $data['data'] = '';
        }
        return json($data);
    }
    /**
     * 获取回访类型
     * @return [type] [description]
     */
    public function getVisitedType()
    {
        /*报文定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/ 
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }   
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
        $visitedType = $this -> Sql -> getStatusDetail('FLOW_回访类型','',$orcl);
        if (array_not_null($visitedType)) {
            foreach ($visitedType as $k => &$v) {
                unset($v['ID'],$v['STATUS']);
            }
            $data['code'] = '200';
            $data['msg'] = '回访类型获取成功';
            $data['data'] = $visitedType;
        } else {
            $data['code'] = '201';
            $data['msg'] = '回访类型获取失败';
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
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }  

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