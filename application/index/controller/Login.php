<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Cache;
use lib\Sql;
class Login extends Controller
{
	public function test()
	{
        if(!getThisDb('123','116.90.87.49','160101')){
			$data['code'] = '201';
			$data['msg'] = '未授权，请联系管理员';
			return json($data);
		}
	}


	/**
	 * 验证服务器 账套
	 * @return [type] [description]
	 */
    public function checkHost()
    {
        /*请求头定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*请求头定义结束*/
		$host = input('post.host');
		$appid = input('post.appid');
		/*开始验证授权*/
        if(!getThisDb('tmp',$host,$appid)){
			$data['code'] = '201';
			$data['msg'] = '未授权，请联系管理员';
			return json($data);
		}

		/*通过c#接口 验证登录*/
		$data = '{"appid": "'.Cache::get('tmp')['APPID'].'","appkey": "'.Cache::get('tmp')['APPKEY'].'","method": "eboss.sys.login","usercode": "'.Cache::get('tmp')['USERNAME'].'","password": "'.Cache::get('tmp')['PWD'].'","source": "kljy"}';
		$url = 'http://'.Cache::get('tmp')['HOST'].'/'.Cache::get('tmp')['V'].'/ebossapi.ashx';
		$relStr = https_request($url,$data);
		// dump($url);die;
		$retArr = json_decode($relStr,true);
		if($retArr){
			if(!$retArr['success']){			
				$res['code'] = '201';
				$res['msg'] = '验证失败 '.$retArr['error'];
			}else{
				$res['code'] = '200';
				$res['msg'] = '验证通过';
			}
			
		}else{
				$res['code'] = '201';
				$res['msg'] = '验证失败,请联系管理员';
		}
		return json($res);

    }
	/**
	 * 验证账号密码
	 * @return [type] [description]
	 */
	public function checkUser()
    {
		/*报文开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*请求头定义结束*/ 

		
		$sql = new Sql();   					//引入sql类

		$host = input('post.host');  			//读取IP地址
		$appid = input('post.appid');			//读取账套
		$usercode = strtoupper(input('post.username'));		//用户名(转大写)
		$password = input('post.password');		//密码

		/*开始验证授权*/
        if(!getThisDb('tmp',$host,$appid)){
			$data['code'] = '201';
			$data['msg'] = '未授权，请联系管理员';
			return json($data);
		}

		/*通过c#接口 验证登录*/
		$data = '{"appid": "'.Cache::get('tmp')['APPID'].'","appkey": "'.Cache::get('tmp')['APPKEY'].'","method": "eboss.sys.login","usercode": "'.$usercode.'","password": "'.$password.'","source": "kljy"}';
		$url = 'http://'.Cache::get('tmp')['HOST'].'/'.Cache::get('tmp')['V'].'/ebossapi.ashx';

		// /*通过接口验证登录*/
		$relStr = https_request($url,$data);
		$retArr = json_decode($relStr,true);
		if($retArr){
			if($retArr['success']){
				$res['code'] = '200';				
				$res['msg'] = '登录成功';				
				$res['user'] = $sql->queryuser($appid,$usercode,Cache::get('tmp')['ORCL']);  // 查询相应信息	
				Cache::rm('tmp');  //删除临时缓存
			}else{
				$res['code'] = '201';				
				$res['msg'] = '登录失败：'. $retArr['error'];	
			}			 
		}else{
			$res['code'] = '201';				
			$res['msg'] = '登录失败';
		}
		return json($res);	
    }

	//验证服务器IP 是否正确
	private function getServer($host,$appid){
		$server = config('serverInfo');
		foreach($server as $k => $v){
			if($v['server'] == $host && $v['appid'] == $appid){
				return $v;
			}
		}
	}
	
}