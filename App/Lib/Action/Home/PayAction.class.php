<?php
// 本类由系统自动生成，仅供测试用途
class PayAction extends HCommonAction {
	var $paydetail = NULL;
	var $payConfig = NULL;
	var $locked = false;
	var $return_url = "";
	var $notice_url = "";
	var $member_url = "";
	
	public function _Myinit(){
		$this->return_url = "http://".$_SERVER['HTTP_HOST']."/Pay/payreturn";
		$this->notice_url = "http://".$_SERVER['HTTP_HOST']."/Pay/paynotice";
		$this->member_url = "http://".$_SERVER['HTTP_HOST']."/member";
		$this->payConfig = FS("Webconfig/payconfig");
	}
	
	public function offline(){
		$this->getPaydetail();
		$this->paydetail['money'] = floatval($_POST['money_off']);
		//本地要保存的信息
		$this->paydetail['fee'] = 0;
		$this->paydetail['nid'] = 'offline';
		$this->paydetail['way'] = 'off';
		$this->paydetail['tran_id'] = text($_POST['tran_id']);
		$this->paydetail['off_bank'] = text($_POST['off_bank']);
		$this->paydetail['off_way'] = text($_POST['off_way']);
		$newid = M('member_payonline')->add($this->paydetail);
		if($newid) $this->success("线下充值提交成功，请等待管理员审核",__APP__."/member/charge#fragment-2");
		else $this->success("线下充值提交失败，请重试");
	}
	
	//网银在线
	public function chinabank()
	{
		if($this->payConfig['chinabank']['enable']==0) exit("对不起，该支付方式被关闭，暂时不能使用!");
		$this->getPaydetail();
		$submitdata['v_mid'] = $this->payConfig['chinabank']['mid'];
		$submitdata['v_oid'] = "chinabank".time().rand(10000,99999);
		$submitdata['v_amount'] = $this->paydetail['money'];
		$submitdata['v_moneytype'] = 'CNY';
		$submitdata['v_url'] = $this->notice_url."?payid=chinabank";
		$submitdata['v_md5info'] = strtoupper($this->getSign('chinabank',$submitdata));
		
		//if($this->paydetail['bank']) $submitdata['bankCode'] = $this->paydetail['bank'];//银行直联必须
		//$submitdata['userType'] = 1;//银行直联,1个人,2企业
		
		//本地要保存的信息
		unset($this->paydetail['bank']);
		$this->paydetail['fee'] = getFloatValue($this->payConfig['chinabank']['feerate'] * $this->paydetail['money'] / 100,2);
		$this->paydetail['nid'] = $this->createnid('chinabank',$submitdata['v_oid']);
		$this->paydetail['way'] = 'chinabank';
		M('member_payonline')->add($this->paydetail);
		$this->create($submitdata,"https://Pay3.chinabank.com.cn/PayGate");
	}
	
	//财付通接口
	public function tenpay()
	{
		if ($this->payConfig['tenpay']['enable'] ==0)
		{
			$this->error( "对不起，该支付方式被关闭，暂时不能使用!" );
		}
		$this->getPaydetail();
		$submitdata['partner'] = $this->payConfig['tenpay']['partner'];
		$submitdata['out_trade_no'] = "tenpay".time().rand(10000, 99999);
		$submitdata['total_fee'] = $this->paydetail['money'] * 100;
		$submitdata['return_url'] = $this->return_url."?payid=tenpay";
		$submitdata['notify_url'] = $this->notice_url."?payid=tenpay";
		$submitdata['body'] = $this->glo['web_name']."帐户充值";
		$submitdata['bank_type'] = "DEFAULT";
		$submitdata['spbill_create_ip'] = get_client_ip();
		$submitdata['fee_type'] = 1;
		$submitdata['subject'] = $this->glo['web_name']."帐户充值";
		$submitdata['sign_type'] = "MD5";
		$submitdata['service_version'] = "1.0";
		$submitdata['input_charset'] = "UTF-8";
		$submitdata['sign_key_index'] = 1;
		$submitdata['trade_mode'] = 1;
		$submitdata['sign'] = $this->getSign("tenpay",$submitdata);
		unset( $this->paydetail['bank']);
		$this->paydetail['fee'] = 0;
		$this->paydetail['nid'] = $this->createnid("tenpay",$submitdata['out_trade_no']);
		$this->paydetail['way'] = "tenpay";
		M("payonline")->add( $this->paydetail);
		$this->create($submitdata, "https://gw.tenpay.com/gateway/pay.htm");
	}
	
	public function payreturn()
	{
		$payid = $_REQUEST['payid'];
		switch($payid){
			case 'chinabank':
				$v_pstatus = $_REQUEST['v_pstatus'];
				if($v_pstatus=="20"){//充值成功
					$signGet = strtoupper($this->getSign('chinabank_return',$_REQUEST));
					$nid = $this->createnid('chinabank',$_REQUEST['v_oid']);
					if($_REQUEST['v_md5str']==$signGet){//充值完成
						$this->success("充值完成",__APP__."/member/");
					}else{//签名不付
						$this->error("签名不付",__APP__."/member/");
					}
				}else{//充值失败
						$this->error("充值失败",__APP__."/member/");
				}
		break;
		case "tenpay" :
			$recode = $_REQUEST['trade_state'];
			if ($recode == "0" )
			{
				$signGet = $this->getSign( "tenpay", $this->getRequest( ) );
				$nid = $this->createnid( "tenpay", $_REQUEST['out_trade_no'] );
				if ( strtolower( $_REQUEST['sign'] ) == $signGet )
				{
					$this->success( "充值完成", __APP__."/member/" );
				}
				else
				{
					$this->error( "充值失败", __APP__."/member/" );
				}
			}
			else
			{
				$this->error( "充值失败", __APP__."/member/" );
				break;
			}
		}
	}
	
	public function paynotice()
	{
		$payid = $_REQUEST['payid'];
		switch($payid){
			case 'chinabank':
				$v_pstatus = $_REQUEST['v_pstatus'];
				if($v_pstatus=="20"){//充值成功
					$signGet = strtoupper($this->getSign('chinabank_return',$_REQUEST));
					$nid = $this->createnid('chinabank',$_REQUEST['v_oid']);
					$money = $_REQUEST['v_amount'];
					if($_REQUEST['v_md5str']==$signGet){//充值完成
						$done = $this->payDone(1,$nid,$_REQUEST['v_oid']);
					}else{//签名不付
						$done = $this->payDone(2,$nid,$_REQUEST['v_oid']);
						echo "签名不正确";
					}
				}else{//充值失败
					$done = $this->payDone(3,$nid);
				}
				if($done===true) echo "ok";
				else echo "error";
			break;
			case "tenpay":
				$recode = $_REQUEST['trade_state'];
				if ($recode == "0"){
					$signGet = $this->getSign("tenpay", $_REQUEST);
					$nid = $this->createnid( "tenpay", $_REQUEST['out_trade_no'] );
					if ( strtolower( $_REQUEST['sign']) == $signGet ){
						$done = $this->payDone(1,$nid,$_REQUEST['transaction_id']);
					}else{
						$done = $this->payDone(2,$nid,$_REQUEST['transaction_id']);
					}
				}else{
					$done = $this->payDone(3,$nid);
				}
				if($done === true){
					echo "success";
				}else{
					echo "fail";
				}
			break;
		}
	}
	
	private function payDone($status,$nid,$oid){
		$done = false;
		$Moneylog = D('member_payonline');
		if($this->locked) return false;
		$this->locked = true;
		switch($status){
			case 1:
				$updata['status'] = $status;
				$updata['tran_id'] = text($oid);
				$vo = M('member_payonline')->field('uid,money,fee,status')->where("nid='{$nid}'")->find();
				if($vo['status']!=0 || !is_array($vo)) return;
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
				
				$tmoney = floatval($vo['money'] - $vo['fee']);
				if($xid) $newid = memberMoneyLog($vo['uid'],3,$tmoney,"充值订单号:".$oid,0,'@网站管理员@');//更新成功才充值,避免重复充值 
				//if(!$newid){
				//	$updata['status'] = 0;
				//	$Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
				//	return false;
				//}
				$vx = M("members")->field("user_phone,user_name")->find($vo['uid']);
				SMStip("payonline",$vx['user_phone'],array("#USERANEM#","#MONEY#"),array($vx['user_name'],$vo['money']));
			break;
			case 2:
				$updata['status'] = $status;
				$updata['tran_id'] = text($oid);
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
			break;
			case 3:
				$updata['status'] = $status;
				$xid = $Moneylog->where("uid={$vo['uid']} AND nid='{$nid}'")->save($updata);
			break;
		}
		
		if($status>0){
			if($xid) $done = true;
		}
		$this->locked = false;
		return $done;
	}
	
	private function createnid($type,$static){
			return md5("XXXXX@@#$%".$type.$static);
	}
	
	private function getPaydetail(){
		if(!$this->uid) exit;
		$this->paydetail['money'] = getFloatValue($_GET['t_money'],2);
		$this->paydetail['fee'] = 0;
		$this->paydetail['add_time'] = time();
		$this->paydetail['add_ip'] = get_client_ip();
		$this->paydetail['status'] = 0;
		$this->paydetail['uid'] = $this->uid;
		$this->paydetail['bank'] = strtoupper($_GET['bankCode']);
	}
	
	private function getSign($type,$data){
		$md5str="";
		switch($type) {
			case "chinabank":
				$signarray=array(
					"v_amount",
					"v_moneytype",
					"v_oid",
					"v_mid",
					"v_url"
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= "$data[$v]";
				}
				$md5str.=$this->payConfig['chinabank']['mkey'];
				$md5str = md5($md5str);
				return $md5str;
			break;
			case "chinabank_return":
				$signarray=array(
					"v_oid",
					"v_pstatus",
					"v_amount",
					"v_moneytype"
				);
				foreach($signarray as $v){
					if(!isset($data[$v])) $md5str .= "";
					else $md5str .= "$data[$v]";
				}
				$md5str.=$this->payConfig['chinabank']['mkey'];
				$md5str = md5($md5str);
				return $md5str;
			break;
			case "tenpay" :
				$signPars = "";
				ksort($data);
				foreach ( $data as $k => $v )
				{
					if ("" != $v && "sign" != $k )
					{
						$signPars .= $k."=".$v."&";
					}
				}
				$signPars .= "key=".$this->payConfig['tenpay']['key'];
				$md5str = strtoupper(md5($signPars));
				return $md5str;
			break;
		}
	}
	
	private function create($data,$submitUrl){
		$inputstr = "";
		foreach($data as $key=>$v){
			$inputstr .= '
		<input type="hidden"  id="'.$key.'" name="'.$key.'" value="'.$v.'"/>
		';
		}
		
		$form = '
		<form action="'.$submitUrl.'" name="pay" id="pay" method="POST">
';
		$form.=	$inputstr;
		$form.=	'
</form>
		';
		
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>请不要关闭页面,支付跳转中.....</title>
        </head>
<body>
        ';
        $html.=	$form;
        $html.=	'
        <script type="text/javascript">
					document.getElementById("pay").submit();
				 </script>
        ';
        $html.= '
        </body>
</html>
		';
				 
		Mheader('utf-8');
		echo $html;
		exit;
	}
}