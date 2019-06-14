<?php
    use think\Cache;
    use think\Db;
	function https_request($url, $data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		 if (!empty($data)){
			 curl_setopt($curl, CURLOPT_POST, 1);
			 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		 }
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}  
    /*获取access_token*/
    function get_access_token(){
        $access_token_arr = Cache::get('access_token');  //从缓存获取token
        if(!$access_token_arr){
          Cache::rm('access_token'); //  需要先清除缓存access_token
          $appId = config('appId');
          $appSecret = config('appSecret');
          $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appId.'&secret='.$appSecret;
          $res = https_request($url);
          $access_token_arr = json_decode($res,true);
          $access_token_arr['createtime'] = date('Y-m-d H:i:s',time());
            // var_dump($access_token_arr['access_token']);  
          Cache::set('access_token',$access_token_arr,6000); //设置缓存 7100秒
        }    
        return $access_token_arr;
          // dump($url);
    }

    /*
    /**
     * 图片路径转换  
     * @param  [type]  $img_path [图片地址]
     * @param  integer $db       [数据库编号]
     * @return [type]            [拼接网络图片地址]
     *
    function img_path($img_path,$db=0)
    {
        if ($img_path) {
            $url = config('IMAGE_URL')[$db].str_replace('\\', '/',  $img_path); */
    /*图片路径转换*/
    function img_path($img_path,$token)
    {
        // dump($url);die;
        $url = 'http://'.Cache::get("$token")['HOST'].'/'.Cache::get("$token")['V'].'/server'.str_replace('\\', '/',  $img_path);
        return $url;
    }
	function assoc_unique($arr, $key) {

		$tmp_arr = array();
		foreach ($arr as $k => $v) {
			if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
				unset($arr[$k]);
			} else {
				$tmp_arr[] = $v[$key];
			}
		}
		sort($arr); //sort函数对数组进行排序
		return $arr;
	}

	//获取查询时间  用于拼接sql
	function getTimes(){
		$thismonth = getTheMonth(date("Ymd",mktime(0, 0 , 0,date("m"),1,date("Y")))); //获取本月
		$lastmonth = getTheMonth(date("Ymd",mktime(0, 0 , 0,date("m")-1,1,date("Y")))); //获取上月
		$year = date("Y",time());
		$time['today'] = date("Ymd"); //今天
		$time['yesterday'] = date("Ymd",strtotime('-1 day')); //昨天
		$time['thisweekstart'] = date("Ymd",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"))); //本周开始时间
		$time['thisweekend'] = date("Ymd",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"))); //本周结束时间
		$time['lastweekstart'] = date("Ymd",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"))); //上周开始时间
		$time['lastweekend'] = date("Ymd",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y"))); //上周结束时间
		$time['thismonthstart'] = $thismonth[0]; //本月第一天
		$time['thismonthend'] = $thismonth[1]; //本月最后一天
		$time['lastmonthstart'] =  $lastmonth[0]; //上月第一天
		$time['lastmonthend'] =  $lastmonth[1]; //上月最后一天
		$time['yearstart'] = $year."0101";; //本年
		$time['yearend'] = $year."1231";; //本年
		return $time;
	}

	//获取指定日期所在月的第一天和最后一天
	function getTheMonth($date){
	$firstday = date("Ym01",strtotime($date));
	$lastday = date("Ymd",strtotime("$firstday +1 month -1 day"));
	return array($firstday,$lastday);
	}

    /**
     * 查询时间sql条件
     *  昨天  yesterday  0
        今天  today      1
        上周  lastweek   2
        本周  week       3
        上月  lastmonth  4
        本月  month      5
        本年  year       6
     * @param  [type] $time [时间标识，0,1,2,3...]
     * @param  [type] $sign [类型标识，0 固定时间  1 时间段]
     * @return [type]       [description]
     */
   	function getBilldate($time,$sign,$alias='')
    {
        $date = getTimes();          //获取时间
        $sign = $sign ? $sign : 0;  //如果是1，说明是自定义时间，0则是固定时间
        // dump($sign);
        $billdate = '';
        if($sign !== 0)             //不等于0情况
        {
            switch ($time[0]) {
                case '0':           //昨天 yesterday 0 between '{$start}' and '{$end}'
                    $billdate = 'and '.$alias.'billdate between '.$date['yesterday'].' and '.$date['yesterday'];
                    break;
                case '1':           //今天  today      1
                    $billdate = 'and '.$alias.'billdate between '.$date['today'].' and '.$date['today'];
                    break;
                case '2':           //上周  lastweek   2
                    $billdate = 'and '.$alias.'billdate between '.$date['lastweekstart'].' and '.$date['lastweekend']; 
                    break;
                case '3':           //本周  week       3
                    $billdate = 'and '.$alias.'billdate between '.$date['thisweekstart'].' and '.$date['thisweekend']; 
                    break;
                case '4':           //上月  lastmonth  4
                    $billdate = 'and '.$alias.'billdate between '.$date['lastmonthstart'].' and '.$date['lastmonthend'];
                    break;
                case '5':           //本月  month      5
                    $billdate = 'and '.$alias.'billdate between '.$date['thismonthstart'].' and '.$date['thismonthend'];
                    break;
                case '6':           //本年  year       6
                    $billdate = 'and '.$alias.'billdate between '.$date['yearstart'].' and '.$date['yearend'];
                    break;
            }           
        }else
        {
            // $start=preg_replace("/[a-zA-Z.]/",'',$time[0]);str_replace(' ', '', 'ab    ab')
            $start=str_replace('-','',$time[0]);
            $end=str_replace('-','',$time[1]);
            // explode(delimiter, string)
            $billdate = "and {$alias}billdate between '{$start}' and '{$end}'";          //自定义时间                    
        }
        return $billdate;
    }
    /**
     * 查询时间sql条件
     *  昨天  yesterday  0
        今天  today      1
        上周  lastweek   2
        本周  week       3
        上月  lastmonth  4
        本月  month      5
        本年  year       6
     * @param  [type] $time [时间标识，0,1,2,3...]
     * @param  [type] $sign [类型标识，0 固定时间  1 时间段]
     * @return [type]       [description]
     */
   	function getCreatetime($time,$sign,$alias='')
    {
        $date = getTimes();          //获取时间
        $sign = $sign ? $sign : 0;  //如果是1，说明是自定义时间，0则是固定时间
        // dump($sign);
        $createtime = '';
        if($sign !== 0)             //不等于0情况
        {
            switch ($time[0]) {
                case '0':           //昨天 yesterday 0
                    $createtime = 'and TO_CHAR(' . $alias . "createtime,'yyyymmdd') between " . $date['yesterday'] . ' and ' . $date['yesterday'];
                    break;
                case '1':           //今天  today      1
                    $createtime = 'and TO_CHAR(' . $alias . "createtime,'yyyymmdd') between " . $date['today'] . ' and ' . $date['today'];
                    break;
                case '2':           //上周  lastweek   2
                    $createtime = 'and TO_CHAR(' . $alias . "createtime,'yyyymmdd') between " . $date['lastweekstart'] . ' and ' . $date['lastweekend'];
                    break;
                case '3':           //本周  week       3
                    $createtime = 'and TO_CHAR(' . $alias . "createtime,'yyyymmdd') between " . $date['thisweekstart'] . ' and ' . $date['thisweekend'];
                    break;

                case '4':           //上月  lastmonth  4
                    $createtime = 'and TO_CHAR(' . $alias . "createtime,'yyyymmdd') between " . $date['lastmonthstart'] . ' and ' . $date['lastmonthend'];
                    break;
                case '5':           //本月  month      5
                    $createtime = 'and TO_CHAR(' . $alias . "createtime,'yyyymmdd') between " . $date['thismonthstart'] . ' and ' . $date['thismonthend'];
                    break;
                case '6':           //本年  year       6
                    $createtime = 'and TO_CHAR(' . $alias . "createtime,'yyyymmdd') between " . $date['yearstart'] . ' and ' . $date['yearend'];
                    break;       
            }           
        }else
        {
            // $start=preg_replace("/[a-zA-Z.]/",'',$time[0]);
            // $end=preg_replace("/[a-zA-Z.]/",'',$time[1]);
            $start=str_replace('-','',$time[0]);
            $end=str_replace('-','',$time[1]);
            // explode(delimiter, string)
            $createtime = "and TO_CHAR({$alias}createtime,'yyyymmdd') between '{$start}' and '{$end}'";          //自定义时间                    
        }
        return $createtime;
    }
    
	/*获取token*/
	function getUserMsg($token)
	{


	    $sql = "SELECT a.cid, a.userid,b.username, b.storeid ,c.storename,b.buyerid,d.buyername,e.companycode 
	            from syslogin a, sysuser b,defstore c,defbuyer d ,syscompany e
	            where a.userid = b.id and b.storeid = c.id and b.buyerid = d.id and a.cid = e.id and sourcetype = 'APP' and a.cid = 1 and a.token = '{$token}'";
	    $res = DB::query($sql);
	    if($res)
	    {
	        $data['CID'] = $res[0]['CID'];  //公司ID
	        $data['STOREID'] = $res[0]['STOREID'];  //店仓ID
	        $data['USERID'] = $res[0]['USERID'];  //用户ID
	        $data['USERNAME'] = $res[0]['USERNAME'];  //用户名称
	        $data['BUYERID'] = $res[0]['BUYERID'];  //经销商ID
	        $data['COMPANYCODE'] = $res[0]['COMPANYCODE'];  //公司编号
	    }else
	    {       
	        $data = ''; 
	    }
	    return $data;       
	}
    /*判断数组为空*/
    function array_not_null($arr){
        if(is_array($arr) && $arr){                         //判断是否数组
            foreach($arr as $k=>$v){                // 循环验证
                if(empty($v) && !is_array($v)){     //$v 为空  $v 不是数组
                    return false;
                }
                $t=array_not_null($v);                  //如果不为空 或者 是一个数组  再次调用方法
                if($t){                            //如果返回false 直接return
                    return true;
                }else{
                    return false;                    
                }
            }
            return true;
        }else{
            if(!empty($arr)){
                return true;
            }
            return false;
        }
    }
    // /*获取访问数据库配置*/
    // function getDbConfig($db=null,$host){
    //     if (!empty($db)) {            
    //         $data = 'oracle://eboss'.$db.':abc123@'.$host.':1521/orcl';
    //     }else{  
    //         $data = 'oracle://eboss:abc123@'.$host.':1521/orcl';
    //     }
    //     return $data;
    // }
    /*获取当前访问用户服务器配置*/
    function getThisDb($token,$host,$appId)
    {
        $getDb = getDbConfig();
        if ($getDb) {
            $arr = Cache::get('config');
            foreach ($arr as $key => &$val) {
                # 判断账套和服务器        
                if ($val['HOST'] == $host && $val['APPID'] == $appId) {
                    /**
                     * 多账套判断
                     * @var host IP
                     * @var eboss_name  库名
                     */
                    $str = strstr($val['HOST'],'/');
                    if ($str) {
                        $host_arr = explode('/',$host);  //分割字符串  提取库名
                        $host = $host_arr[0];                        
                        $eboss_name = $host_arr[1];                        
                    } else {
                        $host = $val['HOST'];
                        $eboss_name = 'eboss_yun';                        
                    }
                    $val['ORCL'] = 'oracle://'.$val['EBOSS'].':abc123@'.$host.':1521/orcl';
                    $val['HOST'] = $host;
                    $val['V'] = $eboss_name;
                    Cache::set("$token",$val);
                    return true;
                }
            }
            return false;
            // Cache::get('config');
        }
    }

    /*获取访问数据库配置*/
    function getDbConfig()
    {
        /*请求头定义开始*/
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*请求头定义结束*/
        $orcl = "oracle://eboss:abc123@yun.ebosserp.com:1521/orcl";
        $sql = "SELECT a.* from login_config a,SYSCOMPANY b where a.cid = b.id and a.isallow = 1 and b.COMPANYCODE = 160111 ";
        $db = Db::connect($orcl);
        $arr = $db -> query($sql);
        if ($arr) {
            Cache::set('config',$arr);
            return true;
        } else {
            return false;
        }

    }
