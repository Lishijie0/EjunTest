<?php
namespace app\index\controller;
use think\Controller;
use think\Request;  
use think\Db;
use lib\Sql;  
use lib\Token;  
use lib\Query;  
use lib\Setting;  
class Common extends Controller
{
	protected $Token;  //token 函数验证
    protected $Sql;  // sql 类
    protected $Query;  // query 类
    protected $Setting;  // setting 类
	protected $Table;  // Table 类
    protected $verifyTokenJsonData;  // 验证token返回值
    protected $verifyVipJsonData;  // 验证vip返回值
    protected $db;  // 数据库
	// 调用类 
    // $Event = \think\Loader::controller('index/common');
    // $Event -> verifyToken();
    public function __construct() 
    {	

        $request = Request::instance()->param();  //获取传入值
        $this -> Token = $this -> verifyToken();            //引入Token   
        $this -> Sql = new Sql();       //引入sql类;
        $this -> Query = new Query();       //引入query类;
        $this -> Setting = new Setting();       //引入setting类;
        $this -> verifyVipJsonData = $this -> verifyVipJsonData();          //vipid   返回值   
        $this -> verifyTokenJsonData = $this -> verifyTokenJsonData();      //Token   返回值  
        
        $this -> Table = Db::connect('oracle://eboss6:abc123@116.62.65.161:1521/orcl');  // 连接杭州服务器 v6数据库
        // $this -> token = [];     //Token   返回值   
    }    
    /**
     * 验证Token
     */
    protected function verifyToken()
    {
    	$Token = new Token();   	//引入Token类; 
        $token = $Token -> getUser(input('post.token')); //获取token

        if(!$token)  //验证token
        {
        	$token = '';
        }        
        return $token;
    }     
    /*验证vipid*/
    private function verifyRequest($request)
    {

        return $data;
    }    
    /*验证token*/
    private function verifyTokenJsonData()
    {
        $data['code'] = '500';
        $data['msg'] = '令牌失效，或许有其他人登录，请重新登录';
        return $data;
    }    
    /*验证vipid*/
    private function verifyVipJsonData()
    {
        $data['code'] = '500';
        $data['msg'] = '会员ID丢失，请重新发送';
        return $data;
    }	




}
