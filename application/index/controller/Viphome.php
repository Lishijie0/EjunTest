<?php
namespace app\index\controller;
use think\Request;
use think\Cache;
class Viphome extends Common
{
	//会员首页三条数据
    public function getIndexData()
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

		$cid = $this -> Token['CID'];                      //公司ID
		$storeid = input('post.storeid/s');  // 店铺ID
		$storeid = !empty($storeid) ? $storeid : $this -> Token['STOREID'];
		$time = input('post.time/a'); //时间
		$sign = input('post.sign/s'); //时间标识  布尔 0 固定时间 1 自定义
		if(empty($time[0])) { $time[0] = 5; $sign = 1;}  //时间为空 
        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
		$data['code'] = '200'; 
		$data['msg'] = '首页统计数据获取成功'; 
		$data['data']['famount'] = number_format($this -> Sql -> getPrise($cid,$storeid,$time,$sign,$orcl));  	//店铺营业额
		$data['data']['vipcount'] = $this -> Sql -> vipCount($cid,$storeid,$time,$sign,$orcl);   			//获取VIP总数
		$data['data']['visited'] = $this -> Sql -> getVipVisitedCount($cid,$storeid,$time,$sign,$orcl);		//回访总数（跟进总数）
		return json($data);
    }

    /*会员列表*/
    public function vipList()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }	

		$cid = $this -> Token['CID'];			//公司ID
		$storeid = input('post.storeid/s');  	// 店铺ID
		$storeid = !empty($storeid) ? $storeid : $this -> Token['STOREID'];
		$begin = input('post.begin/s'); 		//开始条数
		$end = input('post.end/s'); 			//结束条数
		$order_By = input('post.order_by/s'); 	//排序 1-5
		$empid = input('post.empid/s'); 		//员工ID 单选
		$vipType = input('post.vipType/s'); 	//卡类型 单选
		$tags = input('post.tags/a'); 			//标签 多选
		$adddateBegin = input('post.adddateBegin/s');   //开卡时间开始
		$adddateEnd = input('post.adddateEnd/s');  		//开卡时间结束
		$lastdayBegin = input('post.lastdayBegin/s');   //最后消费时间开始
		$lastdayEnd = input('post.lastdayEnd/s');  		//最后消费时间结束
		$visitedDateBegin = input('post.visitedDateBegin/s');   //最后回访时间开始
		$visitedDateEnd = input('post.visitedDateEnd/s');  		//最后回访时间结束
		$birthdayBegin = input('post.birthdayBegin/s');   //最后回访时间开始
		$birthdayEnd = input('post.birthdayEnd/s');  		//最后回访时间结束
		$not_consumption_day = input('post.not_consumption_day/s');  		//最后回访时间结束

		$totlecostBegin = input('post.totlecostBegin/s');  		//消费金额开始
		$totlecostEnd = input('post.totlecostEnd/s');  		//消费金额结束
		$tcntBegin = input('post.tcntBegin/s');  		//消费次数开始
		$tcntEnd = input('post.tcntEnd/s');  		//消费次数结束
		$keywords = input('post.keywords/s');  		//查询关键字  姓名、卡号、手机号

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
		// dump($keywords);
		$data = $this -> Sql -> vipList($begin,$end,$order_By,$cid,$storeid,$empid,$vipType,$tags,$adddateBegin,$adddateEnd,$lastdayBegin,$lastdayEnd,$visitedDateBegin,$visitedDateEnd,$birthdayBegin,$birthdayEnd,$not_consumption_day,$totlecostBegin,$totlecostEnd,$tcntBegin,$tcntEnd,$keywords,$orcl); 
		return json($data);
		// $arr = urlencode($vip_Arr);
		// $json=urldecode(json_encode($arr));
		// echo json_encode($vip_Arr,JSON_UNESCAPED_UNICODE);		
    }
    //会员详情
    /**/
    public function vipDetail()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }	        
		$cid = $this -> Token['CID'];	//公司ID
		$storeid = input('post.storeid/s');	// 店铺ID
		$storeid = !empty($storeid) ? $storeid : $this -> Token['STOREID'];
		$vipid = input('post.vipid/s');	//开始条数
		if (!$vipid) {
			return json($this -> verifyVipJsonData);  //返回错误信息
		}

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

		$data = $this -> Sql -> vipDetail($cid,$storeid,$vipid,$orcl);
		return json($data);


    }
    /**
     * 更新会员详情
     * @return [type] [description]
     */
    public function updateVipDetai()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }	        
		$cid = $this -> Token['CID'];	//公司ID
		$storeid = input('post.storeid/s');	// 店铺ID 
		$storeid = !empty($storeid) ? $storeid : $this -> Token['STOREID']; 
		$vipid = input('post.vipid/s');	// 会员ID  
		$field = input('post.field/s');	// 字段编号  
		$value = input('post.value/s');	// 店铺ID  

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

		$data = $this -> Sql -> updateVipDetailField($cid,$storeid,$vipid,$field,$value,$orcl);
		return json($data);

    }
	/**
	 * 修改会员标签
	 * @return [type] [description]
	 */
    public function updateVipTags()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  

        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }	        
		$cid = $this -> Token['CID'];	//公司ID
		$userid = $this -> Token['USERID'];	// 会员ID  修改标签创建人 
		$vipid = input('post.vipid/s');	// 会员ID  
		$tags = input('post.tags/a');	// 标签数组

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL']; 

		$data = $this -> Sql -> updateVipTags($cid,$vipid,$userid,$tags,$orcl);
		return json($data);
    }

  	/**
  	 * 修改会员头像
  	 * @return [type] [description]
  	 */
    public function updateVipImage()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }	        
		$cid = $this -> Token['CID'];	//公司ID		
		$company = $this -> Token['COMPANYCODE'];	//公司编号
		$storeid = input('post.storeid/s');	// 店铺ID 
		$storeid = !empty($storeid) ? $storeid : $this -> Token['STOREID'];		
    	$vipid = input('post.vipid');  //卡号
    	$vipno = input('post.vipno');  //卡号
    	$image = input('post.image');  //图片文件 base 64 格式
    	// dump($this -> uploadsImg($image,$company,$vipno));die;

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

        $data = $this -> Sql -> updateVipImage($cid,$vipid,$this -> uploadsImg($image,$company,$vipno),$orcl);
        return json($data);
    }
    /**
     * 生日回访
     * @return [type] [description]
     */
    public function birthdayBlessing()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }	        
		$cid = $this -> Token['CID'];	//公司ID	
		$storeid = $this -> Token['STOREID'];	//店铺ID
		$begin = input('post.begin/s'); 		//开始条数
		$end = input('post.end/s'); 			//结束条数	
        $type = input('post.type/s');           //类型区分  0 今日生日 1 本月生日

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

        $data = $this -> Sql -> birthdayBlessing($cid,$storeid,$begin,$end,$type,$orcl);
        return json($data);

    }

    /**
     * 门店活动
     * @return [type] [description]
     */
    public function storeActivity()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }           
        $cid = $this -> Token['CID'];   //公司ID  
        $storeid = $this -> Token['STOREID'];   //店铺ID
        $type = input('post.type/s');           //类型区分  0 正在进行 1 即将结束  2  已结束

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];
        
        $data = $this -> Sql -> storeActivity($cid,$storeid,$type,$orcl);
        return json($data);
        // dump($this -> Sql -> getStatusDetail('策略优惠方式'));

    }
    /**
     * 节日回访
     * @return [type] [description]
     */
    public function holidayVisit()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }           
        $cid = $this -> Token['CID'];   //公司ID  
        $data = $this -> Sql -> holidayVisit($cid,$orcl);
        return json($data);
        // dump($this -> Sql -> getStatusDetail('策略优惠方式'));

    }
    /**
     * 休眠激活
     * @return [type] [description]
     */
    public function dormancyActivation()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }           
        $cid = $this -> Token['CID'];   //公司ID  
        $storeid = $this -> Token['STOREID'];   //店铺ID
        $begin = input('post.begin/s');         //开始条数
        $end = input('post.end/s');             //结束条数

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

        $up_day = $this -> Sql -> userMsg($cid,$storeid,$this->Token['USERID'],$orcl)['UP_DAY'];
        $data = $this -> Sql -> dormancyActivation($cid,$storeid,$up_day,$begin,$end,$orcl);
        return json($data);
        // dump($this -> Sql -> getStatusDetail('策略优惠方式'));

    }

    //会员消费
    public function vipSales()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }           
        $cid = $this -> Token['CID'];   //公司ID  
        $storeid = $this -> Token['STOREID'];     //店铺ID
        $pageBeg = input('post.begin/s');         //开始条数
        $pageEnd = input('post.end/s');           //结束条数
        $keywords = input('post.keywords/s');     //关键字 (手机号)

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

        $data = $this -> Sql -> vipSales($cid,$storeid,$pageBeg,$pageEnd,$keywords,$orcl);
        return json($data);

    }

    /**
     * 添加会员
     */
    public function addVip()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息
        }           
        $request = Request::instance()->param();

        $request['cid'] = $this -> Token['CID'];               //公司ID  
        $request['storeid'] = $this -> Token['STOREID'];       //店铺ID
        $request['createid'] = $this -> Token['USERID'];       //用户ID

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

        // $Verify =  \think\Loader::validate('Verify.add');  // 命名空间引用 类
        // 调用当前模型对应的User验证器类进行数据验证
        $result = $this->validate($request,'Verify.add');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $data['code'] = '201';
            $data['msg'] = $result;
        } else {
            if (!empty($request['image'])) {
                //执行上传方法
                $image_path = $this -> uploadsImg($request['image'],$this -> Token['COMPANYCODE'],$request['mobil'],input('post.token'));
                //获取文件名
                $image_Name = basename($image_path);
                //判断文件名是否存在
                if (!$image_Name) {
                    $data['code'] = '401';
                    $data['msg'] = '图片上传失败'; 
                    return json($data);
                }
                $request['image1'] = $image_Name;  //图片名  手机号+后缀
                $request['image1_path'] = $image_path;  //路径                
            } else {
                $request['image1'] = '';  //图片名  手机号+后缀
                $request['image1_path'] = '';  //路径                     
            }
            // 调用添加方法
            $data = $this -> Sql -> addVip($request,$orcl);
        }
        return json($data);
    }

    /**
     * 会员分析
     * @return [type] [description]
     */
    public function member_Analysis()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息  
        } 
        $cid = $this -> Token['CID'];   //公司ID  
        $storeid = $this -> Token['STOREID'];     //店铺ID

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

        $data = $this -> Sql -> get_Member_Analysis($cid,$storeid,$orcl);
        return json($data);

    }

    /**
     * 添加会员回访
     */
    public function addVipVisited()
    {
        /*报文定义开始*/
        header("Content-type:application/json;charset=utf-8");
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Request-Methods:POST,OPTIONS'); // 允许请求的类型 
        header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段        
        if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS'){exit;} 
        /*报文定义结束*/  
        if(!$this -> Token)  //验证token
        {
            return json($this -> verifyTokenJsonData);  //返回错误信息  
        } 
        $cid = $this -> Token['CID'];   //公司ID 
        $uesrid = $this -> Token['USERID'];   //公司ID 
        $vipid = input('post.vipid');  // 会员ID
        $calltype = input('post.calltype');  // 回访方式
        $calldoc = input('post.calldoc');  // 回访类型
        $callmemo = input('post.callmemo');  // 回访类型

        //获取数据库连接方式
        $orcl = Cache::get(input('post.token'))['ORCL'];

        $data = $this -> Sql -> addVisited($cid,$uesrid,$vipid,$calltype,$calldoc,$callmemo,$orcl);
        return json($data);

    }

	/*排序条件*/
    private function orderCondition ($order)
    {
    	switch ($order) {
    		case '0':  //按消费金额降序
    				$str = 'a.TOTLECOST desc nulls last';
    			break;
    		case '1':  //按消费次数降序
    				$str = 'a.TCNT desc nulls last';
    			break;
    		case '2':  //未消费天数升序
    				$str = 'NOT_CONSUMPTION_DAY asc nulls last';
    			break;
    		case '3':  //按回访时间降序
    				$str = 'VISITED desc nulls last';
    			break;
    		case '4':  //按开卡时间降序
    				$str = 'a.ADDDATE desc nulls last';
    			break;
    		
    		default:
    			# code...
    			break;
    	}

    }

    /**
     * 保存图片
     * 2019/04/20 17:20  李世杰  $image,$company,$moblie
     */
    static function uploadsImg($image,$company,$moblie,$token)
    {		
        $config = Cache::get("$token");
		$up_dir = $config['DIRVE']. ':' . DS . $config['FILENAME'] . DS . 'Server' . DS . $company . DS .'DEFVIP' ; //生成文件夹路径
					// return $up_dir;
		if (!file_exists($up_dir)) {
			  //检查是否有该文件夹，如果没有就创建，并给予最高权限
			  mkdir(iconv("UTF-8", "GBK", $up_dir),0777,true);
		}
        $base64_img = trim($image);
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
            $type = $result[2];
                // 'E:' . DS . 'eBoss_YUN' . DS . 'Server' . DS . $company . DS .'DEFVIP' . DS ,$filename
            if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
                $new_file = $up_dir . DS . $moblie . '.' . $type; //拼接文件名
                // dump($up_dir . DS . $moblie . '.' . $type);die;
                if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))){  //写入数据
                    // $img_path = str_replace('../../..', '', $new_file);
                    $image_path= DS . $company . DS . 'DEFVIP' . DS . basename($new_file);//拼接文件名     
                    return  $image_path;
                }else{
                    return '';  //图片上传失败
                }
            }else{
                //文件类型错误
                return '';  //图片上传类型错误
            }
        }
        
    }

}