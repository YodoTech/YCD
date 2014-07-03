<?php
return array(
	//'配置项'=>'配置值'
    'APP_GROUP_LIST'    => 'Home,Admin,Member',//分组
    'DEFAULT_GROUP'     =>'Home',//默认分组
    'DEFAULT_THEME'     =>'default',//使用的模板
	'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
	'LANG_SWITCH_ON'	=>true,//开启语言包
    'URL_MODEL'=>2, // 如果你的环境不支持PATHINFO 请设置为3,设置为2时配合放在项目入口文件一起的rewrite规则实现省略index.php/
	'URL_CASE_INSENSITIVE'=>true,//关闭大小写为true.忽略地址大小写
    'TMPL_CACHE_ON'    => false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_STRIP_SPACE'      => false,       // 是否去除模板文件里面的html空格与换行
	'APP_ROOT'=>str_replace(array('\\','Conf','config.php','//'), array('/','/','','/'), dirname(__FILE__)),//APP目录物理路径
	'WEB_ROOT'=>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(__FILE__)),0,-8)),//网站根目录物理路径
	'WEB_URL'=>"http://".$_SERVER['HTTP_HOST'],//网站域名
	'CUR_URI'=>$_SERVER['REQUEST_URI'],//当前地址
	'URL_HTML_SUFFIX'=>".html",//静态文件后缀
	'TMPL_ACTION_ERROR' =>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(__FILE__)),0,-8))."/Style/tip/tip.html",//操作错误提示
	'TMPL_ACTION_SUCCESS' =>str_replace("\\", '/', substr(str_replace('\\Conf\\', '/', dirname(__FILE__)),0,-8))."/Style/tip/tip.html",//操作正确提示
	'ERROR_PAGE'	=>'/Public/error.html',
	//cookie
	'COOKIE_PREFIX'	=>'ycd_',
	'COOKIE_PATH'	=>'/',
	'COOKIE_DOMAIN'	=>'.yodo.com',
	//cookie
	//数据库配置
	'DB_TYPE'           => 'mysql',
	'DB_HOST'           => 'localhost',
	'DB_NAME'           =>'ycd',				//数据库名称
	'DB_USER'           =>'root',				//数据库用户名
	'DB_PWD'            =>'123456',				//数据库密码
	'DB_PORT'           =>'3306',
	'DB_PREFIX'         =>'ycd_',
	//'DB_PARAMS'			=>array('persist'=>true),
	//数据库配置
	//子域名配置
	'URL_ROUTER_ON'		=>true,//开启路由规则
	'URL_ROUTE_RULES'	=>array(
		'/^shishicaiwu\/finanz.html/' => 'Home/tool/finanz',//实时财务
		'/^tuiguang\/index.html$/' => 'Home/help/tuiguang',//文章栏目页
		'/^service\/index.html$/' => 'Home/help/kf',//文章栏目页
		'/^borrow\/tool\/index.html$/' => 'Home/tool/index',//文章栏目页
		'/^borrow\/tool\/tool(\d+).html$/' => 'Home/tool/tool:1',//文章栏目页
		'/^borrow\/post\/([a-zA-z]+)\.html$/' => 'Home/borrow/post?type=:1',//文章栏目页
		
		'/^tools\/tool.html$/' => 'Home/tool/index',//文章栏目页
		'/^tools\/tool(\d+).html$/' => 'Home/tool/tool:1',//文章栏目页
		'/^invest\/index.html\?(.*)$/' => 'Home/invest/index?:1',//文章栏目页
		'/^invest\/(\d+).html$/' => 'Home/invest/detail?id=:1',//文章栏目页
		'/^invest\/(\d+).html\?(.*)$/' => 'Home/invest/detail?id=:1:2',//文章栏目页
		'/^([a-zA-z]+)\/([a-zA-z]+).html(.*)$/' => 'Home/help/index:3',//文章栏目页
		'/^([a-zA-z]+)\/(\d+).html$/' => 'Home/help/view?id=:2',//文章内容页
		'/^([a-zA-z]+)\/id\-(\d+).html$/' => 'Home/help/view?id=:2&type=subsite',//文章内容页
		'/^([a-zA-z]+)\/([a-zA-z]+)\/(\d+).html$/' => 'Home/help/view?id=:3',//文章内容页
		'/^aaa\/sss.html$/' => 'Admin/index/login/',//后台路径
	),
	'SYS_URL'	=>array('admin','borrow','member','invest','donate','tool','feedback','service','bid'),
	'EXC_URL'	=>array('invest/tool/index.html','borrow/tool/index.html','borrow/tool/tool2.html','borrow/tool/tool2.html'),
	//友情链接
    'FRIEND_LINK'=>array(
			1=>'首页',
			2=>'内页',
		),
	//友情链接
    'TYPE_SET'=>array(
			1=>'列表页',
			2=>'单页',
			3=>'跳转',
		),
	'XMEMBER_TYPE'=>array(
			1=>'普通借款者',
			2=>'优良借款者',
			3=>'风险借款者',
			4=>'黑名单',
	),
	//收费类型
    'MONEY_LOG'=>array(
			2=>'会员升级',
			3=>'会员充值',
			4=>'提现冻结',
			5=>'撤消提现',
			6=>'投标冻结',
			7=>'管理员操作',
			8=>'流标返还',
			9=>'会员还款',
			10=>'网站代还',
			11=>'偿还借款',
			12=>'提现失败',
			13=>'推广奖励',
			14=>'升级VIP',
			15=>'投标成功本金解冻',
			16=>'复审未通过返还',
			17=>'借款入帐',
			18=>'借款管理费',
			19=>'借款保证金',
			20=>'投标奖励',
			21=>'支付投标奖励',
			22=>'视频认证费用',
			23=>'利息管理费',
			24=>'还款完成解冻',
			25=>'实名认证费用',
			26=>'现场认证费用',
			27=>'充值审核',
			28=>'投标成功待收利息',
			29=>'提现成功',
			30=>'逾期罚息',
			31=>'催收费',
			32=>'线下充值奖励',
			33=>'续投奖励(预奖励)',
			34=>'续投奖励',
			35=>'续投奖励(取消)',
			36=>'提现通过，审核处理中',
		),
	'BANK_NAME'=>array(
			'招商银行'=>'招商银行',
			'中国银行'=>'中国银行',
			'中国工商银行'=>'中国工商银行',
			'中国建设银行'=>'中国建设银行',
			'中国农业银行'=>'中国农业银行',
			'中国邮政储蓄银行'=>'中国邮政储蓄银行',
			'交通银行'=>'交通银行',
			'上海浦东发展银行'=>'上海浦东发展银行',
			'深圳发展银行'=>'深圳发展银行',
			'中国民生银行'=>'中国民生银行',
			'兴业银行'=>'兴业银行',
			'平安银行'=>'平安银行',
			'北京银行'=>'北京银行',
			'天津银行'=>'天津银行',
			'上海银行'=>'上海银行',
			'华夏银行'=>'华夏银行',
			'光大银行'=>'光大银行',
			'广发银行'=>'广发银行',
			'中信银行'=>'中信银行',
			'上海农商银行'=>'上海农商银行',
		),
	
	'REPAYMENT_TYPE'=>array(
			'1'=>'每月还款',
			'2'=>'一次性还款'
		),
	
	'PAYLOG_TYPE'=>array(
			'0'=>'充值未完成',
			'1'=>'充值成功',
			'2'=>'签名不符',
			'3'=>'充值失败'
		),
	
	'WITHDRAW_STATUS'=>array(
			'0'=>'待审核',
			'1'=>'审核通过,处理中',
			'2'=>'已提现',
			'3'=>'审核未通过'
		),
	
	'FEEDBACK_TYPE'=>array(
			'1'=>'借入借出',
			'2'=>'资金账户',
			'3'=>'推广奖金',
			'4'=>'充值提现',
			'5'=>'注册登录',
			'6'=>'其他问题',
			'7'=>'快速借款通道'
		),
);
?>