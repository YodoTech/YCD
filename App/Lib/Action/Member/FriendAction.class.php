<?php
// 本类由系统自动生成，仅供测试用途
class FriendAction extends MCommonAction {

    public function index(){
		$this->display();
    }

	public function friendlist(){
		$map['f.uid']  = $this->uid;
		$map['f.apply_status'] = 1;
		$list = getFriendList($map,15);
		$this->assign("list",$list);
		//$this->display("Public:_footer");
		
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	public function friendapply(){
		$map['f.friend_id'] = $this->uid;
		$map['f.apply_status'] = 0;
		$list = getFriendList($map,15);
		$this->assign("list",$list);
		
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	public function friendmsg(){
		$parm['map']['to_uid'] = $this->uid;
		$parm['map']['to_del'] = 0;
		$parm['pagesize'] = 15;
		$list = getMsgList($parm);
		$this->assign("list",$list);
		
		$parm['map']['is_read'] = 0;
		$this->assign("unread",M('member_msg')->where($parm['map'])->count('id'));
		$data['html'] = $this->fetch();
		
		exit(json_encode($data));
	}

	public function msghistory(){
		$parm['map']['from_uid'] = $this->uid;
		$parm['map']['from_del'] = 0;
		$parm['pagesize'] = 15;
		$list = getMsgList($parm);
		$this->assign("list",$list);
		$data['html'] = $this->fetch();
		
		exit(json_encode($data));
	}

	public function delmsg(){
		$idarr = text($_POST['idarr']);
		$type = text($_POST['type']);;
		
		if($type=='to') $map['to_uid'] = $this->uid;
		else $map['from_uid'] = $this->uid;
		$map['id'] = array("in",$idarr);
		
		if($type=='to') $save['to_del'] = 1;
		else $save['from_del'] = 1;
		$newid = M('member_msg')->where($map)->save($save);
		if($newid){
			$data['data'] = $idarr;
			ajaxmsg($data);
		}
		else ajaxmsg("删除失败，请重试",0);
	}

	public function viewmsg(){
		$id = intval($_GET['id']);
		$where['to_uid'] = $this->uid;
		$where['from_uid'] = $this->uid;
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['id'] = $id;
		$vo = M('member_msg')->field(true)->where($map)->find();
		if(!is_array($vo)) ajaxmsg("数据有误",0);
		
		M('member_msg')->where($map)->setField('is_read',1);
		$this->assign("vo",$vo);
		$data['content'] = $this->fetch();
		exit(json_encode($data));
	}

	public function blockfriend(){
		$map['f.uid'] = $this->uid;
		$map['f.apply_status'] = 3;
		$list = getFriendList($map,15);
		$this->assign("list",$list);
		
		$data['html'] = $this->fetch();
		exit(json_encode($data));
	}

	public function dofriend(){
		$fuid = intval($_POST['uid']);
		$type = intval($_POST['type']);
		if(empty($type)||empty($fuid)) ajaxmsg('',0);
		$map['uid'] = $fuid;
		$map['friend_id'] = $this->uid;
		if($type==1) $newid = M("member_friend")->where($map)->setField('apply_status',$type);
		else $newid = M("member_friend")->where($map)->delete();
		if($newid){
			if($type==1||$type==3){
				$xmap['uid'] = $this->uid;
				$xmap['friend_id'] = $fuid;
				$xmap['apply_status'] = $type;
				$cu = M("member_friend")->where($xmap)->count('id');
				
				if($type==1){
					$xmap['apply_status'] = 3;
					M("member_friend")->where($xmap)->delete();
				}else{
					$xmap['apply_status'] = 1;
					M("member_friend")->where($xmap)->delete();
				}
				if($cu==0){
					$save['uid'] = $this->uid;
					$save['friend_id'] = $fuid;
					$save['apply_status'] = $type;
					$save['add_time'] = time();
					$newxid = M('member_friend')->add($save);
				}else{
					$newxid=1;
				}
				
				if($newxid) ajaxmsg();
				else ajaxmsg('',0);
			}else{
				ajaxmsg();
			}
			//$this->display("Public:_footer");
		}
		else ajaxmsg('',0);
	}


	public function dofriendm(){
		$fuid = intval($_POST['uid']);
		$type = intval($_POST['type']);
		if(empty($type)||empty($fuid)) ajaxmsg('',0);
		$map['uid'] = $this->uid;
		$map['friend_id'] = $fuid;
		if($type==3) $newid = M("member_friend")->where($map)->setField('apply_status',$type);
		else $newid = M("member_friend")->where($map)->delete();
		if($newid) ajaxmsg();
		else ajaxmsg('',0);
	}


	public function removeblock(){
		$fuid = intval($_POST['uid']);
		if(empty($fuid)) ajaxmsg('',0);
		$map['uid'] = $this->uid;
		$map['friend_id'] = $fuid;
		$newid = M("member_friend")->where($map)->delete();
		if($newid) ajaxmsg();
		else ajaxmsg('',0);
	}

	
	public function innermsg(){
		if(!$this->uid) ajaxmsg("请先登陆",0);
		$this->assign("touid",intval($_GET['uid']));
		$data['content'] = $this->fetch("Public:innermsg");
		ajaxmsg($data);
	}
	public function doinnermsg(){
		$touid = intval($_POST['to']);
		$msg = text($_POST['msg']);	
		$title = text($_POST['title']);	
		$newid = addMsg($this->uid,$touid,$title,$msg);
		if($newid) ajaxmsg();
		else ajaxmsg("发送失败",0);
		
	}

}