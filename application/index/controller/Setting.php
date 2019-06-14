<?php
namespace app\index\controller;
class Setting extends Common     //个人资料 
{
	/**
	 * 个人资料详情 修改前的 展示
	 * @return [type] [description]
	 */
    public function userMsg()
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
		$userid = $this -> Token['USERID'];

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

		$data = $this -> Sql -> getMsgSet($cid,$storeid,$userid,$orcl);
		return json($data);
	}
    /**
     * 修改用户信息
     * @return [type] [description]
     */
	public function updateUserMsg()
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
		$userid = $this -> Token['USERID'];
		$field = input('post.field');  //更新的字段名
        $value = input('post.value');  //更新的值
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
		$data = $this -> Setting -> updateMsg($cid,$storeid,$userid,$field,$value,$orcl); //更新参数
		return json($data);
	}

}	