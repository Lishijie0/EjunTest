<?php
namespace app\index\controller;
use think\Cache;
class Sales extends Common     //销售分析
{
	/**
	 * 销售分析 首页报表详情
	 * @return [type] [description]
	 */
    public function getData()
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
		$storeid = input('post.storeid/s');  // 店铺ID
		$storeid = !empty($storeid) ? $storeid : $this -> Token['STOREID'];
		$time = input('post.time/a');  //时间
		$sign = input('post.sign/s');  //时间标识  布尔 0 固定时间 1 自定义
		if(empty($time[0])) { $time[0] = 5; $sign = 1;}  //时间为空 
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
		$data['code'] = '200'; 
		$data['msg'] = '销售分析数据获取成功'; 
		$data['data'] = $this -> Sql -> getSales($cid,$storeid,$time,$sign,$orcl);
		return json($data);
	}
    /**
     * 销售回访列表
     */
	public function salesVisitedVipList()
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
		$begin = input('post.begin/s'); 		//开始条数
		$end = input('post.end/s'); 			//结束条数
		$type = input('post.type/s'); 			//类型

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

		$data = $this -> Sql -> salesVisited($cid,$storeid,$begin,$end,$type,$orcl);
		return json($data);
	}
    /**
     * 销售查询
     * 
     */      
	public function salesQuery()
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
		$pageBeg = input('post.pageBeg/s'); 			//开始条数
		$pageEnd = input('post.pageEnd/s'); 			//结束条数
		$dateBeg = input('post.dateBeg/s'); 			//开始时间
		$dateEnd = input('post.dateEnd/s'); 			//结束时间

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

		$data = $this -> Sql -> salesQuery($cid,$storeid,$pageBeg,$pageEnd,$dateBeg,$dateEnd,$orcl);
		return json($data);

	}
}	