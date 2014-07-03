<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `ycd_global`;");
E_C("CREATE TABLE `ycd_global` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `text` text NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT ' ',
  `tip` varchar(200) NOT NULL DEFAULT ' ',
  `order_sn` int(11) NOT NULL DEFAULT '0',
  `code` varchar(20) NOT NULL DEFAULT ' ',
  `is_sys` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=99 DEFAULT CHARSET=utf8");
E_D("replace into `ycd_global` values('20','textarea','圆创贷','网站关键词','在首页的keywords中显示','118','web_keywords','1');");
E_D("replace into `ycd_global` values('21','textarea','圆创贷','网站描述','在网站首页的描述中显示','117','web_descript','1');");
E_D("replace into `ycd_global` values('19','input','圆创贷','网站名称','出现在每个页面的title后面','120','web_name','1');");
E_D("replace into `ycd_global` values('27','input','圆创贷','首页title','首页标题','119','index_title','1');");
E_D("replace into `ycd_global` values('56','textarea','<p><a href=''/dfafa/index.html''>关于我们</a>  |  <a href=''/invest/index.html''>我要投资</a>  |  <a href=''/borrow/index.html''>我要贷款</a>  |  <a href=''/tools/tool.html''>网贷工具</a>  |  <a href=''/help/index.html''>咨询服务</a>  |  <a href=''/help/index.html''>帮助中心</a>  |   <a href=''/dfafa/lxwm.html''>联系我们</a></p>\r\n<p><a href=\"#\"><img src=\"/Style/H/images/chengxin.jpg\" /></a>  <a href=\"#\"><img src=\"/Style/H/images/kexin.jpg\" /></a>  <a href=\"#\"><img src=\"/Style/H/images/beian.jpg\" /></a>  <a href=\"#\"><img src=\"/Style/H/images/jingcha.jpg\" /></a></p>','网站底部','网站底部的版权和联系信息','116','bottom','1');");
E_D("replace into `ycd_global` values('71','input','100','VIP费用','VIP费用(每年)','0','fee_vip','1');");
E_D("replace into `ycd_global` values('72','input','3|8','逾期罚息','逾期后罚息的计算,(3|8)表示逾期3天开始算罚息,每天千分之8','110','fee_expired','1');");
E_D("replace into `ycd_global` values('73','input','30|2','催收费','逾期以后的催收费,(30|2)表示逾期30天以后开始算每天千分之2的催收费','111','fee_call','1');");
E_D("replace into `ycd_global` values('95','input','5000-10000|0|10000-30000|1.5|30000|2',' 线下充值奖励',' 填入5000-10000|1|10001-30000|1.5|30001|2 表示，线下充值金额在5000至10000以内的奖励千分之1，在10000至30000以内的奖励千分之1.5，大于30000时奖励千分之2','0','offline_reward','1');");
E_D("replace into `ycd_global` values('64','input','10-50|0-0|3-30|30|10','提现手续费','以10-50|0-0|3-30|10|20的形式填入，数字从左到右依次表示超出回款资金总额的每笔收取总额的千分之10作为手续费,最大手续费上限50元;提现在回款总金额内的每笔收费千分之0元，手续费上限0元;单日单笔提现上限3万,单日提现资金上限30万,每月免费提现次数为10次，超出免费次数后每次收取提现手续费20元','10','fee_tqtx','1');");
E_D("replace into `ycd_global` values('66','input','10|24','发标时的年化利率','以10|24的形式填入，表示年化利率最小10%,最大24%','1','rate_lixi','1');");
E_D("replace into `ycd_global` values('69','input','8','投资者成交管理费','10表示收取投资者所赚利息的10%','113','fee_invest_manage','1');");
E_D("replace into `ycd_global` values('70','input','1|24','借款期限(按天)','以1|24的形式填入，以天为单位，表示按天借款时最少借款时间为1天，最多24天','1','borrow_duration_day','1');");
E_D("replace into `ycd_global` values('67','input','1|24','借款期限','以1|24的形式填入，以月为单位，表示最小借款时间为1个月，最大24个月','1','borrow_duration','1');");
E_D("replace into `ycd_global` values('74','input','10','借款保证金','借款者借款成功后冻结的保证金,填10表示借款总金额的10%','114','money_deposit','1');");
E_D("replace into `ycd_global` values('75','input','10','视频认证费用','','0','fee_video','1');");
E_D("replace into `ycd_global` values('76','input','0','现场认证费用','','0','fee_face','1');");
E_D("replace into `ycd_global` values('77','input','0','实名认证费用','','0','fee_idcard','1');");
E_D("replace into `ycd_global` values('78','input','6','VIP默认额度','','0','limit_vip','1');");
E_D("replace into `ycd_global` values('79','input','0.1|1|0.2|4','借款者管理费','以0.1|1|0.2|4的形式填入，表示按天时每天收取借款总额0.1%的管理费，按月时在借款期限小于等于4个月时收取借款总额1%的管理费，借款期限大于4个月时(收获取借款总额1%的管理费+超过4个月的时间里每月收取借款总额0.2%的管理费)','115','fee_borrow_manage','1');");
E_D("replace into `ycd_global` values('90','input','2','客服提成','客服提成比例,填2表示千分之二','0','customer_rate','0');");
E_D("replace into `ycd_global` values('91','input','1|-3|-10|2|100','成功还款积分规则','填入1|-3|-10|2|100表示,正常还款|迟还逾期还款|提前还款|金额比率  不同状态下还款金额折合金额金额比率后*对应值','0','credit_borrow','0');");
E_D("replace into `ycd_global` values('92','input','100','投资成功积分规则','整数或者小数，表示比率，比如填入100，会员投资成功300元，则可得到300/100=3分','0','credit_investor','0');");
E_D("replace into `ycd_global` values('93','input','1','邀请下线奖励','填入整数，如2表示千分之二,即你所邀请的下线每投标成功一次，您可获得千分之二的奖励','2','award_invest','1');");
E_D("replace into `ycd_global` values('94','input','2013-01-04|2013-03-30','排行榜期限','某时间段内的月投标排名，前面时间为开始时间，后者为结束时间','2','rankDate','1');");
E_D("replace into `ycd_global` values('96','input','23:59:59',' 到期还款时钟设置',' 23:59:59 表示借款人必须在还款到期当天的23:59:59之前进行还款，否则该标变为逾期。填写请遵照hh:mm:ss格式','0','back_time','1');");
E_D("replace into `ycd_global` values('97','input','0',' 银行卡修改开关',' 1表示可以修改，0表示不可以修改','0','edit_bank','1');");
E_D("replace into `ycd_global` values('98','input','1|1.5|2',' 回款投标自动奖励',' 以1|1.5|2的形式填入，表示回款续投一月标奖励1‰回款续投二月标奖励1.5‰ 回款续投三月标及以上奖励2‰，如果投标金额大于回款资金池金额，有效续投奖励以回款金额资金池总额为标准，否则以投标金额为准','0','today_reward','1');");

require("../../inc/footer.php");
?>