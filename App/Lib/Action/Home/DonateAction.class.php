<?php
// 本类由系统自动生成，仅供测试用途
class DonateAction extends HCommonAction {
    public function index(){
		$per = C('DB_PREFIX');
	
		//网站公告
		$parmart['pagesize']=8;
		$parmart['type_id']=182;
		$listnotic = getArticleList($parmart);
		//媒体报道
		$media['pagesize']=8;
		$media['type_id']=303;
		$listmedia = getArticleList($media);
		//论坛精华
		$bbsbest['pagesize']=8;
		$bbsbest['type_id']=184;
		$listbbsbest = getArticleList($bbsbest);
		//最新投标
		$newinvest = M('borrow_investor i')->field("i.investor_capital,m.user_name")->join("{$per}members m ON m.id=i.investor_uid")->order('i.id DESC')->limit(20)->select();
        $this->assign("newinvest", $newinvest);
		//最新满标
		$newsucc = M('borrow_info b')->field("b.borrow_money,m.user_name")->join("{$per}members m ON m.id=b.borrow_uid")->where("b.borrow_status=3")->order('i.id DESC')->limit(20)->select();
        $this->assign("newsucc", $newsucc);
		//搜索筛选
		static $newpars;
		$curl = $_SERVER['REQUEST_URI'];
		$urlarr = parse_url($curl);
		parse_str($urlarr['query'],$surl);//array获取当前链接参数，2.
		
		$urlArr = array('use','area_id','age');
		
		foreach($urlArr as $v){
			$newpars = $surl;//用新变量避免后面的连接受影响
			unset($newpars[$v],$newpars['type']);//去掉公共参数，对掉当前参数
			foreach($newpars as $skey=>$sv){
				if($sv=="all") unset($newpars[$skey]);//去掉"全部"状态的参数,避免地址栏全满
			}
			
			$newurl = http_build_query($newpars);//生成此值的链接,生成必须是即时生成
			$searchUrl[$v]['url'] = $newurl;
			$searchUrl[$v]['cur'] = !isset($_GET[$v])?"all":text($_GET[$v]);
		}
		
		$area = C('DONATE_AREA');
		$rowArea=array();
		$rowArea['all'] = "全部";
		foreach($area as $key=>$v){
			$rowArea[$key] = $v;
		}
		
		$use = C('DONATE_TYPE');
		$rowUse=array();
		$rowUse['all'] = "全部";
		foreach($use as $key=>$v){
			$rowUse[$key] = $v;
		}
		
		$searchMap['use'] = $rowUse;
		$searchMap['area_id'] = $rowArea;
		$searchMap['age'] = array("all"=>"全部","0-6"=>"0-6岁","7-12"=>"7-12岁","13-18"=>"13-18岁","19-22"=>"19-22岁","23-59"=>"23-59岁","60-199"=>"60岁以上");

		$search = array();
		//搜索条件
		foreach($urlArr as $v){
			if($_GET[$v] && $_GET[$v]<>'all'){
				switch($v){
					case 'age':
						$barr = explode("-",text($_GET[$v]));
						$search["age"] = array("between",$barr);
					break;
					default:
						$barr = text($_GET[$v]);
						$search[$v] = $barr;
					break;
				}
			}
		}
		//捐助列表
		$donate_list = getDonateList($search,15);

		$this->assign("searchUrl",$searchUrl);
		$this->assign("searchMap",$searchMap);

        $this->assign("listnotic", $listnotic['list']);
        $this->assign("listmedia", $listmedia['list']);
        $this->assign("listbbsbest", $listbbsbest['list']);
        $this->assign("donatelist", $donate_list['list']);
        $this->assign("pagebar", $donate_list['page']);
		$this->display();
    }
	public function detail(){
		if($_GET['type']=='commentlist'){
			//评论
			$cmap['type'] = 2;
			$cmap['tid'] = intval($_GET['id']);
			$clist = getCommentList($cmap,10);
			$this->assign("commentlist",$clist['list']);
			$this->assign("commentpagebar",$clist['page']);
			$this->assign("commentcount",$clist['count']);
			$data['html'] = $this->fetch('commentlist');
			exit(json_encode($data));
		}
		$use = C('DONATE_TYPE');
		$vo = M('donate')->find(intval($_GET['id']));
		$vo['use'] = $use[$vo['use']];


		//评论
		$cmap['type'] = 2;
		$cmap['tid'] = intval($_GET['id']);
		$clist = getCommentList($cmap,10);
		$this->assign("commentlist",$clist['list']);
		$this->assign("commentpagebar",$clist['page']);
		$this->assign("commentcount",$clist['count']);

        $this->assign("vo", $vo);
        $this->assign("id", intval($_GET['id']));
		$this->display();
	}

	public function addcomment(){
		$data['comment'] = text($_POST['comment']);
		if(!$this->uid)  ajaxmsg("请先登陆",0);
		if(empty($data['comment']))  ajaxmsg("留言内容不能为空",0);
		$data['type'] = 2;
		$data['add_time'] = time();
		$data['uid'] = $this->uid;
		$data['uname'] = session("u_user_name");
		$data['tid'] = intval($_POST['tid']);
		$data['name'] = M('donate')->getFieldById($data['tid'],'title');
		$newid = M('comment')->add($data);
		if($newid) ajaxmsg();
		else ajaxmsg("留言失败，请重试",0);
	}
	public function donate(){
		$per = C('DB_PREFIX');
		//网站公告
		$parmart['pagesize']=8;
		$parmart['type_id']=182;
		$listnotic = getArticleList($parmart);
		//媒体报道
		$media['pagesize']=8;
		$media['type_id']=303;
		$listmedia = getArticleList($media);
		//论坛精华
		$bbsbest['pagesize']=8;
		$bbsbest['type_id']=184;
		$listbbsbest = getArticleList($bbsbest);
		//最新投标
		$newinvest = M('borrow_investor i')->field("i.investor_capital,m.user_name")->join("{$per}members m ON m.id=i.investor_uid")->order('i.id DESC')->limit(20)->select();
        $this->assign("newinvest", $newinvest);
		//最新满标
		$newsucc = M('borrow_info b')->field("b.borrow_money,m.user_name")->join("{$per}members m ON m.id=b.borrow_uid")->where("b.borrow_status=3")->order('i.id DESC')->limit(20)->select();
        $this->assign("newsucc", $newsucc);

        $this->assign("listnotic", $listnotic['list']);
        $this->assign("listmedia", $listmedia['list']);
        $this->assign("listbbsbest", $listbbsbest['list']);
		$this->display();
	}

}