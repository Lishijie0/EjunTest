<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Cache;
use lib\Token;  
use lib\Sql;  
class Index extends Controller
{
	public function test()
	{
		$str = strval("valFMOUTWRATIO = FMOUTTHISWEEK && FMOUTLASTYEARWEEK ? number_format(FMOUTTHISWEEK / FMOUTLASTYEARWEEK,1) :");
		dump($str);die;
		dump(date("Ymd",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y")-1)));
		dump(date("Ymd",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y")-1)));
		die;
		header('Content-type:text/html;charset=UTF-8');//utf-8编码格式
		header("Access-Control-Allow-Origin:*");
        // ob_end_flush();//输出全部内容到浏览器
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*请求头定义结束*/
        $host = input('post.host'); // 获取访问的html  
        $arr = explode('/', $host);
        $token = input('token');
		$config = "oracle://eboss:abc123@116.90.87.49:1521/orcl";
	    $db = Db::connect($config);	
		dump($db -> query('select * from dual'));die;
        Cache::set("token",$token,3600);
        return json(Cache::get("token"));
        // dump(Cache::get("$token"));die;		
	}

	public function testToken()
	{
		// Cache::connect($options);
		// $cache = new Cache();
		// dump(Cache::rm('data'));die;
		$result = Cache::get('data');
		if ($result) {
			echo "从缓存获取";
			dump($result);
		} else {
			$data = date('Y-m-d H:i:s',time());
			$cacheResult = Cache::store('redis')->set('data',"$data",5);
			if ($cacheResult) {
				echo "已写入缓存";
				dump($cacheResult);
			}
		}
		die;

		$access_token_arr = Cache::get('access_token');  //从缓存获取token
        //非 判断token是否存在 
        if(!$access_token_arr){
            $access_token_arr = get_access_token();
        }
    	Cache::set("test",$access_token_arr,10);
        dump(Cache::get('test'));die;
	}
	//基类
	public function verifyUrl()
	{
		header('Content-type:text/html;charset=UTF-8');//utf-8编码格式
		header("Access-Control-Allow-Origin:*");
        // ob_end_flush();//输出全部内容到浏览器
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*请求头定义结束*/
        $url = input('post.url'); // 获取访问的html  
        $arr_str = parse_url($url);
        $str = ltrim($arr_str['fragment'],'/?');
        parse_str($str, $arr);  // 截取路径
        // dump($arr);die;
        $host = $_SERVER['SERVER_ADDR'];
    	$token = $arr['token'];  //token
    	$host = $arr['host'];  //host
        if (!empty($arr['db'])) {
        	$db = $arr['db']; //截取用户名
			$config = 'oracle://eboss' . $db . ':abc123@' . $host . ':1521/orcl';
        } else {
			$config = 'oracle://eboss:abc123@' . $host . ':1521/orcl';	
        }
        // dump($config);die;

	    try {
		    $table = Db::connect($config);	                      
		    $res = $table -> name('syslogin')  -> find();
		    if ($res) {
		    	$Token = new Token();   	//引入Token类; 
		    	$Sql = new Sql();   	//引入Token类; 
		        $userMsg = $Token -> getUser($arr['token']); //获取token
		        // dump($userMsg);die;
		        if (!$userMsg) {		        	
			        $data['code'] = '500';
			        $data['msg'] = '令牌失效，或许有其他人登录，请重新登录';
        			return json($data);
		        }
		    	$data['code'] = '200';
		    	$data['msg'] = '登录成功';
		        $data['token'] = $token;
		        $data['data'] = $Sql -> queryuser($userMsg['CID'],$userMsg['USERID'],1);
		    } else {
		    	$data['code'] = '201';
		    	$data['msg'] = '获取数据失败';		    	
		    }	    	
	    } catch (\Exception $e) {	    	
		    	$data['code'] = '201';
		    	$data['msg'] = '获取用户信息失败';	
	    }
        return json($data);
	}

}
