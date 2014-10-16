<?php
// 本类由系统自动生成，仅供测试用途
class BorrowAction extends HCommonAction {
	public function index() {
		$per = C('DB_PREFIX');
		if($this->uid){
			$this->assign("mstatus", M('members_status')->field(true)->find($this->uid));
			$this->assign("mdata", getMemberInfoDone($this->uid));
			$this->assign("minfo", getMinfo($this->uid,true));
			$this->assign("capitalinfo", getMemberBorrowScan($this->uid));
		}
        $this->assign("pagebar", $donate_list['page']);
		$this->display();
    }
	
	public function post(){
		if(!$this->uid) $this->error("请先登陆",__APP__."/member/common/login");
		$_xoc = M('borrow_info')->where("borrow_uid={$this->uid} AND borrow_status in(0,2,4)")->count('id');
		if($_xoc>0)  $this->error("您有一个借款中的标，请等待审核",__APP__."/member/borrowin#fragment-1");
		
		$vminfo = M('members')->field("user_leve,time_limit")->find($this->uid);
		if(!($vminfo['user_leve']>0 && $vminfo['time_limit']>time())) $this->error("请先通过VIP审核再发标",__APP__."/member/vip");
		
		$gtype = text($_GET['type']);
		$vkey = md5(time().$gtype);
		switch($gtype){
			case "normal"://普通标
				$borrow_type=1;
			break;
			case "vouch"://担保标
				$borrow_type=2;
			break;
			case "second"://秒还标
				$this->assign("miao",'yes');
				$borrow_type=3;
			break;
			case "net"://净值标
				$borrow_type=4;
			break;
			case "mortgage"://抵押标
				$borrow_type=5;
			break;
		}


		cookie($vkey,$borrow_type,3600);
		$borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
		$day = range($borrow_duration_day[0],$borrow_duration_day[1]);
		$day_time=array();
		foreach($day as $v){
			$day_time[$v] = $v."天";
		}

		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$month = range($borrow_duration[0],$borrow_duration[1]);
		$month_time=array();
		foreach($month as $v){
			$month_time[$v] = $v."个月";
		}
		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		$borrow_config = require C("APP_ROOT")."Conf/borrow_config.php";
		$this->assign("borrow_use",$borrow_config['BORROW_USE']);
		$this->assign("borrow_min",$borrow_config['BORROW_MIN']);
		$this->assign("borrow_max",$borrow_config['BORROW_MAX']);
		$this->assign("borrow_time",$borrow_config['BORROW_TIME']);
		$this->assign("BORROW_TYPE",$borrow_config['BORROW_TYPE']);
		$this->assign("borrow_type",$borrow_type);
		$this->assign("borrow_day_time",$day_time);
		$this->assign("borrow_month_time",$month_time);
		$this->assign("repayment_type",$borrow_config['REPAYMENT_TYPE']);
		$this->assign("vkey",$vkey);
		$this->assign("rate_lixt",$rate_lixt);
		
		$this->display();
	}
	
	public function save(){
		if(!$this->uid) $this->error("请先登陆",__APP__."/member/common/login");
		$pre = C('DB_PREFIX');
		//相关的判断参数
		$rate_lixt = explode("|",$this->glo['rate_lixi']);
		$borrow_duration = explode("|",$this->glo['borrow_duration']);
		$borrow_duration_day = explode("|",$this->glo['borrow_duration_day']);
		$fee_borrow_manage = explode("|",$this->glo['fee_borrow_manage']);
		$vminfo = M('members m')->join("{$pre}member_info mf ON m.id=mf.uid")->field("m.user_leve,m.time_limit,mf.province_now,mf.city_now,mf.area_now")->where("m.id={$this->uid}")->find();
		//相关的判断参数
		$borrow['borrow_type'] = intval(cookie(text($_POST['vkey'])));
		
		if(floatval($_POST['borrow_interest_rate'])>$rate_lixt[1] || floatval($_POST['borrow_interest_rate'])<$rate_lixt[0]) $this->error("提交的借款利率有误，请重试",0);
		$borrow['borrow_money'] = intval($_POST['borrow_money']);


		$_minfo = getMinfo($this->uid,true);
		$_capitalinfo = getMemberBorrowScan($this->uid);
		///////////////////////////////////////////////////////
		$borrowNum=M('borrow_info')->field("borrow_type,count(id) as num,sum(borrow_money) as money,sum(repayment_money) as repayment_money")->where("borrow_uid = {$this->uid} AND borrow_status=6 ")->group("borrow_type")->select();
		$borrowDe = array();
		foreach ($borrowNum as $k => $v) {
			$borrowDe[$v['borrow_type']] = $v['money'] - $v['repayment_money'];
		}
		///////////////////////////////////////////////////
		switch($borrow['borrow_type']){
			case 1://普通标
				if($_minfo['credit_cuse']<$borrow['borrow_money']) $this->error("您的可用信用额度为{$_minfo['credit_cuse']}元，小于您准备借款的金额，不能发标");
			break;
			case 2://担保标
				if($_minfo['borrow_vouch_cuse']<$borrow['borrow_money']) $this->error("您的可用信用担保借款额度为{$_minfo['borrow_vouch_cuse']}元，小于您准备借款的金额，不能发标");
			break;
			case 3://秒还标

			break;
			case 4://净值标
				//$_netMoney = getFloatValue($minfo['money_collect'],2);//getNet($_minfo,$_capitalinfo);
				//if($_netMoney<$borrow['borrow_money']) $this->error("您的净资产为{$_netMoney}元，小于您准备借款的金额，不能发标");
				$_netMoney = getFloatValue(0.9*$_minfo['money_collect']-$borrowDe[4],2);
				if($_netMoney<$borrow['borrow_money']) $this->error("您的净值额度{$_netMoney}元，小于您准备借款的金额，不能发标");
			break;
			case 5://抵押标
				//$borrow_type=5;
			break;
		}

		
		if($borrow['borrow_type']==2){//担保标				
			$borrow['reward_vouch_rate'] = floatval($_POST['vouch_rate']);
			$borrow['reward_vouch_money'] = getFloatValue($borrow['borrow_money']*$borrow['reward_vouch_rate']/100,2);
			$borrow['vouch_member'] = text($_POST['vouch_member']);
		}
		
		$borrow['borrow_uid'] = $this->uid;
		$borrow['borrow_name'] = text($_POST['borrow_name']);
		$borrow['borrow_duration'] = ($borrow['borrow_type']==3)?1:intval($_POST['borrow_duration']);//秒标固定为一月
		$borrow['borrow_interest_rate'] = floatval($_POST['borrow_interest_rate']);
		if(strtolower($_POST['is_day'])=='yes') $borrow['repayment_type'] = 1;
		elseif($borrow['borrow_type']==3) $borrow['repayment_type'] = 2;//秒标按月还
		else $borrow['repayment_type'] = intval($_POST['repayment_type']);
		
		
		if($_POST['show_tbzj']==1) $borrow['is_show_invest'] = 1;//共几期(分几次还)
		
		$borrow['total'] = ($borrow['repayment_type']==1)?1:$borrow['borrow_duration'];//共几期(分几次还)
		$borrow['borrow_status'] = 0;
		$borrow['borrow_use'] = intval($_POST['borrow_use']);
		$borrow['add_time'] = time();
		$borrow['collect_day'] = intval($_POST['borrow_time']);
		$borrow['add_ip'] = get_client_ip();
		$borrow['borrow_info'] = text($_POST['borrow_info']);
		$borrow['reward_type'] = intval($_POST['reward_type']);
		$borrow['reward_num'] = floatval($_POST["reward_type_{$borrow['reward_type']}_value"]);
		$borrow['borrow_min'] = intval($_POST['borrow_min']);
		$borrow['borrow_max'] = intval($_POST['borrow_max']);
		$borrow['province'] = $vminfo['province_now'];
		$borrow['city'] = $vminfo['city_now'];
		$borrow['area'] = $vminfo['area_now'];
		if($_POST['is_pass']&&intval($_POST['is_pass'])==1) $borrow['password'] = md5($_POST['password']);
		
		//借款费和利息
		$borrow['borrow_interest'] = getBorrowInterest($borrow['repayment_type'],$borrow['borrow_money'],$borrow['borrow_duration'],$borrow['borrow_interest_rate']);
		
		
		if($borrow['repayment_type'] == 1){//按天还
			$fee_rate = (is_numeric($fee_borrow_manage[0]))?($fee_borrow_manage[0]/100):0.001;
			$borrow['borrow_fee'] = getFloatValue($fee_rate*$borrow['borrow_money']*$borrow['borrow_duration'],2);
		}else{
			$fee_rate_1=(is_numeric($fee_borrow_manage[1]))?($fee_borrow_manage[1]/100):0.02;
			$fee_rate_2=(is_numeric($fee_borrow_manage[2]))?($fee_borrow_manage[2]/100):0.002;
			if($borrow['borrow_duration']>$fee_borrow_manage[3]&&is_numeric($fee_borrow_manage[3])){
				$borrow['borrow_fee'] = getFloatValue($fee_rate_1*$borrow['borrow_money'],2);
				$borrow['borrow_fee'] += getFloatValue($fee_rate_2*$borrow['borrow_money']*($borrow['borrow_duration']-$fee_borrow_manage[3]),2);
			}else{
				$borrow['borrow_fee'] = getFloatValue($fee_rate_1*$borrow['borrow_money'],2);
			}
		}
		
		if($borrow['borrow_type']==3){//秒还标
			if($borrow['reward_type']>0){
				if($borrow['reward_type']==1) $_reward_money = getFloatValue($borrow['borrow_money']*$borrow['reward_num']/100,2);
				elseif($borrow['reward_type']==2) $_reward_money = getFloatValue($borrow['reward_num'],2);
			}
			$_reward_money =floatval($_reward_money);
			if(($_minfo['account_money']+$_minfo['back_money'])<($borrow['borrow_fee']+$_reward_money)) $this->error("发布此标您最少需保证您的帐户余额大于等于".($borrow['borrow_fee']+$_reward_money)."元，以确保可以支付借款管理费和投标奖励费用");
		}
		
		//投标上传图片资料（暂隐）
		foreach($_POST['swfimglist'] as $key=>$v){
			if($key>10) break;
			$row[$key]['img'] = substr($v,1);
			$row[$key]['info'] = $_POST['picinfo'][$key];
		}
		$borrow['updata']=serialize($row);
		$newid = M("borrow_info")->add($borrow);

		if($newid) $this->success("借款发布成功，网站会尽快初审",__APP__."/member/borrowin#fragment-1");
		else $this->error("发布失败，请先检查是否完成了个人详细资料然后重试");
		
	}

	//资料上传页面
	public function apply() {
		$array = $this->_apply_setting();
		$this->assign('applySetting', $array);
		$this->display();
	}

	private function _apply_setting() {
		$array = array(
			'maxSize' => 1024*1024*3,//3M
			'chunkSize' => 1024*1024*1,//1M
			'extensions' => array('jpg', 'gif', 'png', 'jpeg')
		);
		return $array;
	}

	private function _apply_error($data) {
		ajaxmsg($data, 0);
	}

	//上传文件校验
	private function _apply_check($file) {
		$applySetting = $this->_apply_setting();
		$data = array(
			'jsonrpc' => '2.0',
			'id' => 'id'
		);
		if($file['error']!== 0) {
            //文件上传失败
            //捕获错误代码
            switch($file['error']) {
	            case 1:$error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';break;
	            case 2:$error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';break;
	            case 3:$error = '文件只有部分被上传';break;
	            case 4:$error = '没有文件被上传';break;
	            case 6:$error = '找不到临时文件夹';break;
	            case 7:$error = '文件写入失败';break;
	            default:$error = '未知上传错误！';
	        }
	        $data['error'] = array('code' => $file['error'], 'message' => $error);
            $this->_apply_error($data);
        }
        //文件上传成功，进行自定义规则检查
        //检查文件大小
        if(($file['size'] > $applySetting['maxSize']) && ($applySetting['maxSize'] > 0)) {
            $data['error'] = array('code' => 201, 'message' => '上传文件大小不符！');
            $this->_apply_error($data);
        }

        //检查文件类型
        $pathinfo = pathinfo($file['name']);
        if(!in_array(strtolower($pathinfo['extension']),$applySetting['extensions'],true)) {
            $data['error'] = array('code' => 202, 'message' => '上传文件类型不允许');
            $this->_apply_error($data);
        }

        //检查是否合法上传
        if(!is_uploaded_file($file['tmp_name'])) {
        	$data['error'] = array('code' => 203, 'message' => '非法上传文件！');
            $this->_apply_error($data);
        }
        return true;
	}

	//Plupload 文件上传
	public function apply_upload() {
		$data = array(
			'jsonrpc' => '2.0',
			'id' => 'id'
		);
		// HTTP headers for no cache etc
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Settings
		$targetDir = C('HOME_UPLOAD_DIR').'Borrow';

		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds

		// 5 minutes execution time
		@set_time_limit(5 * 60);

		// Uncomment this one to fake upload time
		// usleep(5000);

		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir.'/'.$fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir.'/'.$fileName_a.'_'.$count.$fileName_b))
				$count++;

			$fileName = $fileName_a.'_'.$count.$fileName_b;
		}

		$filePath = $targetDir.'/'.$fileName;

		// Create target dir
		if (!file_exists($targetDir)) @mkdir($targetDir);

		// Remove old temp files	
		if ($cleanupTargetDir) {
			if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
				while (($file = readdir($dir)) !== false) {
					$tmpfilePath = $targetDir.'/'.$file;

					// Remove temp file if it is older than the max age and is not the current file
					if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
						@unlink($tmpfilePath);
					}
				}
				closedir($dir);
			} else {//Failed to open temp directory
				$data['error'] = array('code' => 100, 'message' => '系统出错');
				$this->_apply_error($data);
			}
		}

		if ($this->_apply_check($_FILES['upload'])) {
			// Open temp file
			$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = @fopen($_FILES['upload']['tmp_name'], "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else {//Failed to open input stream.
					$data['error'] = array('code' => 101, 'message' => '系统出错');
					$this->_apply_error($data);
				}
				@fclose($in);
				@fclose($out);
				@unlink($_FILES['upload']['tmp_name']);
			} else {//Failed to open output stream.
				$data['error'] = array('code' => 102, 'message' => '系统出错');
				$this->_apply_error($data);
			}
		} else {//Failed to move uploaded file.
			$data['error'] = array('code' => 103, 'message' => '系统出错');
			$this->_apply_error($data);
		}

		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off 
			rename("{$filePath}.part", $filePath);

			//保存数据
			$infos = array(
				'uid' 		=> $this->uid,
				'add_ip' 	=> get_client_ip(),
				'add_time' 	=> time()
			);
			$infos['data_name'] = $_FILES['upload']['name'];
			$infos['data_url'] 	= $filePath;
			$pathinfo = pathinfo($filePath);
			$infos['ext'] 		= $pathinfo['extension'];
			$infos['size'] 		= $_FILES['upload']['size'];
			if (!(M("borrow_apply_file")->add($infos))) {
				@unlink($filePath);
				$data['error'] = array('code' => 104, 'message' => '上传失败，请重试');
				$this->_apply_error($data);
			}
		}

		$data['result'] = null;
		ajaxmsg($data, 1);
	}

	//资料提交
	/* post data
		borrow_name:xxx
		borrow_info:xxxx
		uploader_0_tmpname:p193se6qjbjor16qhplu4ldapj4.jpg
		uploader_0_name:yanzhengma.jpg
		uploader_0_status:done
		uploader_count:1
	*/
	public function apply_submit() {
		$pre = c("DB_PREFIX");
		$status = true;
		$borrowApply = d('borrow_apply');
    	$borrowApply->startTrans();
    	//信息添加
    	$iid = M('borrow_apply')->add(array(
    		'uid' 			=> $this->uid,
    		'borrow_name' 	=> text($_POST['borrow_name']),
    		'borrow_info' 	=> text($_POST['borrow_info']),
    		'add_group' 	=> strtolower(GROUP_NAME),
    		'add_user' 		=> $this->uid,
    		'add_ip' 		=> get_client_ip(),
    		'add_time' 		=> time()
    	));
    	if ($iid) {
			$uploader_count = intval($_POST['uploader_count']);
			for ($i=0; $i < $uploader_count; $i++) {
				if (!(m()->execute("update `{$pre}borrow_apply_file` set `iid`={$iid} WHERE `data_name`='".text($_POST['uploader_'.$i.'_name'])."'"))) {
					$status = false;
					break;
				}
			}
    	} else {
    		$status = false;
    	}
    	if ($status) {
    		$borrowApply->commit();
    		$data['message'] = '申请成功，请耐心等待审核';
			ajaxmsg($data, 1);
    	} else {
    		$borrowApply->rollback();
    		$data['message'] = '申请失败，请重试';
			$this->_apply_error($data);
    	}
	}
	
	//swf上传图片
	public function swfUpload(){
		if($_POST['picpath']){
			$imgpath = substr($_POST['picpath'],1);
			if(in_array($imgpath,$_SESSION['imgfiles'])){
					 unlink(C("WEB_ROOT").$imgpath);
					 $thumb = get_thumb_pic($imgpath);
				$res = unlink(C("WEB_ROOT").$thumb);
				if($res) $this->success("删除成功","",$_POST['oid']);
				else $this->error("删除失败","",$_POST['oid']);
			}else{
				$this->error("图片不存在","",$_POST['oid']);
			}
		}else{
			$this->savePathNew = C('HOME_UPLOAD_DIR').'Product/';
			$this->thumbMaxWidth = C('PRODUCT_UPLOAD_W');
			$this->thumbMaxHeight = C('PRODUCT_UPLOAD_H');
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if(!isset($_SESSION['count_file'])) $_SESSION['count_file']=1;
			else $_SESSION['count_file']++;
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];//返回给前台显示缩略图
		}
	}

	//工薪贷
	public function prodwork() {
		//...
		$this->display();
	}
	//生意贷
	public function prodbiz() {
		//...
		$this->display();
	}
	//网商贷
	public function prodecomm() {
		//...
		$this->display();
	}

}