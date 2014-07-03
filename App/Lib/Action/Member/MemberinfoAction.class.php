<?php
// 本类由系统自动生成，仅供测试用途
class MemberinfoAction extends MCommonAction {

    public function index(){
		$this->display();
    }
	
	public function editmemberinfo(){
		$model=M('member_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
		unset($savedata['real_name'],$savedata['idcard'],$savedata['card_img'],$savedata['card_credits']);
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}
	
	public function editcontact(){
		$model=M('member_contact_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}

	
	public function editdepartment(){
		$model=M('member_department_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}

	
	public function edithouse(){
		$model=M('member_house_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}
	
	public function editfinancial(){
		$model=M('member_financial_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			$json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}


	
	public function editensure(){
		$model=M('member_ensure_info');
		if(!$_POST){
			$vo = $model->find($this->uid);
			if(!is_array($vo)) $model->add(array('uid'=>$this->uid));
			else $this->assign('vo',$vo);
			$json['content'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$savedata = textPost($_POST);
		$savedata['uid'] = $this->uid;
		
		
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->save()) {
			if($nid) $json['message'] = "修改成功";
			else $json['message'] = "修改成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			if($nid) $json['message'] = "修改失败或者资料没有改动";
			else  $json['message'] = "修改失败或者资料没有改动";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}

	
	public function editdata(){
		$Bconfig = require C("APP_ROOT")."Conf/borrow_config.php";
		$model=M('member_data_info');
		if(!$_FILES){
			import("ORG.Util.Page");
			$count = $model->where("uid={$this->uid}")->count('id');
			$p = new Page($count, 15);
			$page = $p->show();
			$Lsql = "{$p->firstRow},{$p->listRows}";
			$list = $model->field('id,data_url,data_name,add_time,status,type,ext,size,deal_info,deal_credits')->where("uid={$this->uid}")->order("type DESC")->limit($Lsql)->select();
			
			$this->assign('Bconfig',$Bconfig);
			$this->assign('list',$list);
			$this->assign('page',$page);
			$json['html'] = $this->fetch();
			exit(json_encode($json));
		}
		
		$this->savePathNew = C('MEMBER_UPLOAD_DIR').'MemberData/'.$this->uid.'/' ;
		$this->saveRule = date("YmdHis",time()).rand(0,1000);
		$info = $this->CUpload();

		$savedata['data_url'] = $info[0]['savepath'].$info[0]['savename'];
		$savedata['size'] = $info[0]['size'];
		$savedata['ext'] = $info[0]['extension'];
		$savedata['data_name'] = text(urldecode($_GET['name']));
		$savedata['type'] = intval($_GET['data_type']);
		$savedata['uid'] = $this->uid;
		$savedata['add_time'] = time();
		$savedata['status'] = 0;
		
        if (false === $model->create($savedata)) {
            $this->error($model->getError());
        }elseif ($result = $model->add()) {
			$json['message'] = "文件上传成功";
			$json['status'] = 1;
			exit(json_encode($json));
        } else {
			$json['message'] = "文件上传失败";
			$json['status'] = 0;
			exit(json_encode($json));
        }
	}

	public function delfile(){
		$id = intval($_POST['id']);
		
		$model=M('member_data_info');
		$vo = $model->field("uid,status")->where("id={$id}")->find();
		if(!is_array($vo)) ajaxmsg("提交数据有误",0);
		else if($vo['uid']!=$this->uid) ajaxmsg("不是你的资料",0);
		else if($vo['status']==1) ajaxmsg("审核通过的资料不能删除",0);
		else{
			$newid = $model->where("id={$id}")->delete();
		}
		if($newid) ajaxmsg();
		else ajaxmsg('删除失败，请重试',0);
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
			$this->savePathNew = C('MEMBER_UPLOAD_DIR').'MemberData/' ;
			$this->thumbMaxWidth = "1000,1000";
			$this->thumbMaxHeight = "1000,1000";
			$this->saveRule = date("YmdHis",time()).rand(0,1000);
			$info = $this->CUpload();
			$data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
			if(!isset($_SESSION['count_file'])) $_SESSION['count_file']=1;
			else $_SESSION['count_file']++;
			$_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
			echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];//返回给前台显示缩略图
		}
	}

	
	public function getarea(){
		$rid = intval($_GET['rid']);
		if(empty($rid)){
			$data['NoCity'] = 1;
			exit(json_encode($data));
		}
		$map['reid'] = $rid;
		$alist = M('area')->field('id,name')->order('sort_order DESC')->where($map)->select();

		if(count($alist)===0){
			$str="<option value=''>--该地区下无下级地区--</option>\r\n";
		}else{
			if($rid==1) $str.="<option value='0'>请选择省份</option>\r\n";
			foreach($alist as $v){
				$str.="<option value='{$v['id']}'>{$v['name']}</option>\r\n";
			}
		}
		$data['option'] = $str;
		$res = json_encode($data);
		echo $res;
	}	
	

}