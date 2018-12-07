<?php
session_start();
ini_set("display_errors", 0);
Require("rsa.php");

//-------------------- 服务器信息 --------------------
$server = '192.168.200.131';			//数据库地址
$dbuser = 'game';						//数据库用户名
$dbpass = 'uu5!^%jg';					//数据库密码

//--------------------- 游戏名称 ---------------------
$gamename = '爱老牛DNF社区服4';

//--------------------- 功能开关 ---------------------
$maint = 'open';		//游戏运行状态 --open表示正常 --close表示维护
$regst = 'open';		//注册帐号开关*
$modfy = 'open';		//修改密码开关*
$backe = 'open';		//密码找回开关*
$cdkex = 'open';		//CDK 兑换开关
//open为打开相应功能，close为关闭相应功能，带*项表示为其他参数时不打开功能界面而跳转到指定网页

//-------------------- 自定义链接 --------------------
$gamehome = 'http://bbs.ilaoniu.cn';	//游戏官网
$recharge = 'http://www.baidu.com';		//充值地址
$serviceq = '718053767';				//在线客服QQ
$supportq = '718053767';				//技术支持QQ
$regstUrl = 'http://www.baidu.com';		//游戏注册地址
$modfyUrl = 'http://www.baidu.com';		//修改密码地址
$backeUrl = 'http://www.baidu.com';		//密码找回地址

//--------------------- 注册更新 ---------------------
$inituid = '1';		//注册起始uid，不超过8位数
$regdb = '0';		//注册赠送D币数量
$regdd = '200';		//注册赠送D点数量
$pvfId = '4D4E811F0917E5A8302511916AE66A26C64D0BA0';	//PVF效验值（SHA1算法）
$pvfUrl = 'http://pan.baidu.com/s/1skLrp7Z';			//PVF更新地址

$msgkey = 'insure';				//登录器通讯密钥

//连接数据库
$dbcon = Mysql_connect($server, $dbuser, $dbpass);
if (!$dbcon){
	echo "Mysql Connect Error";
	exit;
}

if(substr($_POST['Data'],0,11)=='GET_TempKey')
{
	$_SESSION[Temp_Key]=GET_Temp_Key();
	exit(strtoupper(strToHex(rc4($_SESSION[Temp_Key],$msgkey))));
}

$temps = str_decode($_POST['Data'],$msgkey,$_SESSION[Temp_Key]);
$temp = explode('|',$temps);
$rand = $temp[0];

if(count($temp)<3){
	exit('Live Or Die!');
}

//验证是否连接服务器
if($temp[1]=='isconnect'){
	$isconnect = $temp[2];
	if($isconnect = 'isconnect'){
		echo str_encode("connect success",$msgkey,$rand);
		exit;
	}else{
		echo str_encode("connect fail",$msgkey,$rand);
		exit;
	}
}

//获取游戏名称
if($temp[1]=='getgame'){
     echo str_encode($gamename,$msgkey,$rand);
     exit;
}

//获取注册地址
if($temp[1]=='getregurl'){
     echo str_encode($regstUrl,$msgkey,$rand);
     exit;
}

//获取修改密码地址
if($temp[1]=='getmodurl'){
     echo str_encode($modfyUrl,$msgkey,$rand);
     exit;
}

//获取找回密码地址
if($temp[1]=='getbacurl'){
     echo str_encode($backeUrl,$msgkey,$rand);
     exit;
}

//获取在线客服QQ
if($temp[1]=='getservice'){
     echo str_encode($serviceq,$msgkey,$rand);
     exit;
}

//获取技术支持QQ
if($temp[1]=='getsupport'){
     echo str_encode($supportq,$msgkey,$rand);
     exit;
}

//获取充值地址
if($temp[1]=='getrecharge'){
     echo str_encode($recharge,$msgkey,$rand);
     exit;
}

//获取游戏地址
if($temp[1]=='gethost'){
     echo str_encode($server,$msgkey,$rand);
     exit;
}

//获取数据库用户
if($temp[1]=='getduser'){
     echo str_encode($dbuser,$msgkey,$rand);
     exit;
}

//获取数据库密码
if($temp[1]=='getdpswd'){
     echo str_encode($dbpass,$msgkey,$rand);
     exit;
}

//获取游戏官网
if($temp[1]=='gethome'){
     echo str_encode($gamehome,$msgkey,$rand);
     exit;
}

//获取pvf值
if($temp[1]=='getpvf'){
     echo str_encode($pvfId,$msgkey,$rand);
     exit;
}

//获取PVF更新地址
if($temp[1]=='pvfurl'){
     echo str_encode($pvfUrl,$msgkey,$rand);
     exit;
}

//游戏维护开关
if($temp[1]=='maint'){
    if($maint=='open'){
		echo str_encode("in service",$msgkey,$rand);
		exit;
	}else{
		echo str_encode("system maint",$msgkey,$rand);
		exit;
   }
}

//账号重复性检测
if($temp[1]=='nameCheck'){
	$username = $temp[2];
	$sql = Mysql_query("select * from d_taiwan.accounts where accountname='$username'");
	if(Mysql_num_rows($sql)==0){
		echo str_encode("check success",$msgkey,$rand);
		exit;
	}else{
		echo str_encode("check fail",$msgkey,$rand);
		exit;		
	}
	
}

//注册账号开关检测
if($temp[1]=='checkreg'){
	if($regst == 'open'){
		echo str_encode("register open",$msgkey,$rand);
		exit;
	}else if($regst == 'close'){
		echo str_encode("register close",$msgkey,$rand);
		exit;		
	}else{
		echo str_encode("register url",$msgkey,$rand);
		exit;
	}
}

//修改密码开关检测
if($temp[1]=='checkmod'){
	if($modfy == 'open'){
		echo str_encode("modify open",$msgkey,$rand);
		exit;
	}else if($modfy == 'close'){
		echo str_encode("modify close",$msgkey,$rand);
		exit;
	}else{
		echo str_encode("modify url",$msgkey,$rand);
		exit;
	}
}

//找回密码开关检测
if($temp[1]=='checkbac'){
	if($backe == 'open'){
		echo str_encode("back open",$msgkey,$rand);
		exit;
	}else if($backe == 'close'){
		echo str_encode("back close",$msgkey,$rand);
		exit;		
	}else{
		echo str_encode("back url",$msgkey,$rand);
		exit;
	}
}

//CDK兑换开关检测
if($temp[1]=='checkcdk'){
	if($cdkex == 'open'){
		echo str_encode("cdk open",$msgkey,$rand);
		exit;
	}else{
		echo str_encode("cdk close",$msgkey,$rand);
		exit;		
	}
}

//注册帐号
if($temp[1]=='register'){
	$username = $temp[2];
	$password = $temp[3];
	$qq = $temp[4];
	$sql = Mysql_query("select * from d_taiwan.accounts where accountname='$username'");
		
	if(Mysql_num_rows($sql)==0){
		$sql = Mysql_query("select * from d_taiwan.accounts order by UID desc limit 1");
		if(Mysql_num_rows($sql)==0){
			$uid = $inituid;
		}else{
			$str = Mysql_fetch_array($sql);
			$uid = $str['UID'] + 1;
		}
		$time = date("y-m-d h:i:s",time());
		if($_COOKIE['ip'] == $_SERVER['REMOTE_ADDR']){
			echo str_encode("register not",$msgkey,$rand);
			exit;
		}else{
			if(Mysql_query("insert into d_taiwan.accounts (UID,accountname,password,qq) VALUES ('$uid','$username','$password','$qq')")){
				Mysql_query("insert into d_taiwan.limit_create_character (m_id) VALUES ('$uid')");
				Mysql_query("insert into d_taiwan.member_info (m_id,user_id) VALUES ('$uid','$uid')");
				Mysql_query("insert into d_taiwan.member_join_info (m_id) VALUES ('$uid')");
				Mysql_query("insert into d_taiwan.member_miles (m_id) VALUES ('$uid')");
				Mysql_query("insert into d_taiwan.member_white_account (m_id) VALUES ('$uid')");
				Mysql_query("insert into taiwan_login.member_login (m_id) VALUES ('$uid')");
				Mysql_query("insert into taiwan_billing.cash_cera (account,cera,mod_date,reg_date) VALUES ('$uid','$regdb','$time','$time')");
				Mysql_query("insert into taiwan_billing.cash_cera_point (account,cera_point,reg_date,mod_date) VALUES ('$uid','$regdd','$time','$time')");
				Mysql_query("insert into taiwan_cain_2nd.member_avatar_coin (m_id) VALUES ('$uid')");
				Mysql_query("insert into d_gmaster.login (uid,reg_time) VALUES ('$uid','$time')");
				setcookie('ip',$_SERVER['REMOTE_ADDR'],time()+60*60);
				setcookie('count',1,time()+60*60);
				echo str_encode("register success",$msgkey,$rand);
				exit;
			}else{
				echo str_encode("register fail",$msgkey,$rand);
				exit;
			}
		}
	}else{
		echo str_encode("register repeat",$msgkey,$rand);
		exit;
	}
}

//修改密码
if($temp[1]=='modify'){
	$username = $temp[2];
	$password = $temp[3];
	$newpassword = $temp[4];
	$sql = Mysql_query("select * from d_taiwan.accounts where accountname='$username' and password='$password'");

	if(Mysql_num_rows($sql)==0){
		echo str_encode("username or password error",$msgkey,$rand);
		exit;
	}
	if(Mysql_query("update d_taiwan.accounts set password='$newpassword' where accountname='$username'")){
		echo str_encode("modify success",$msgkey,$rand);
		exit;
	}else{
		echo str_encode("modify fail",$msgkey,$rand);
		exit;
	}
}

//找回密码
if($temp[1]=='back'){
	$username = $temp[2];
	$password = $temp[3];
	$qq = $temp[4];
	$sql = Mysql_query("select * from d_taiwan.accounts where accountname='$username' and qq='$qq'");

	if(Mysql_num_rows($sql)==0){
		echo str_encode("username or qq error",$msgkey,$rand);
		exit;
	}
	if(Mysql_query("update d_taiwan.accounts set password='$password' where accountname='$username'")){
		echo str_encode("back success",$msgkey,$rand);
		exit;
	}else{
		echo str_encode("back fail",$msgkey,$rand);
		exit;
	}
}

//获取角色信息
if($temp[1]=='getChar'){
	if($cdkex=='open'){
		$username = $temp[2];
		$password = $temp[3];
		$sql = Mysql_query("select * from d_taiwan.accounts where accountname='$username' and password='$password'");

		if(Mysql_num_rows($sql)==0){
			echo str_encode("getchar fail",$msgkey,$rand);
			exit;
		}else{
			echo str_encode("getchar success",$msgkey,$rand);
			exit;
		}
	}else{
		echo str_encode("cdk close",$msgkey,$rand);
		exit;
	}
}

//CDK兑换
if($temp[1]=='cdkey'){
	if($cdkex=='open'){
		$char_no = $temp[2];
		$cdks = $temp[3];
		$uid = $temp[4];
		$sql = Mysql_query("select * from d_gmaster.cdk where cdk='$cdks' and status='0'");
		
		if(Mysql_num_rows($sql)==0){
			echo str_encode("cdk error",$msgkey,$rand);
			exit;
		}

		$str = Mysql_fetch_array($sql);
		$code = $str['code'];
		$num = $str['num'];
		$gold = $str['gold'];
		$cera = $str['cera_point'];

		if(Mysql_query("update d_gmaster.cdk set status='1' where cdk='$cdks'")){
			postal($code,$num,$gold,$char_no);
			$sql_cera = Mysql_query("select * from taiwan_billing.cash_cera_point where account='$uid'");
			$str_cera = Mysql_fetch_array($sql_cera);
			$cera_point = $str_cera['cera_point'] + $cera;
			Mysql_query("update taiwan_billing.cash_cera_point set cera_point='$cera_point' where account='$uid'");
			echo str_encode("cdk success",$msgkey,$rand);
			exit;
		}else{
			echo str_encode("cdk fail",$msgkey,$rand);
			exit;
		}
	}else{
		echo str_encode("cdk close",$msgkey,$rand);
		exit;
    }
}

//CDK兑换邮件系统
function postal($code,$num,$gold,$char_no){
	$sql = Mysql_query("select * from taiwan_cain_2nd.postal order by letter_id desc limit 1");
	if(Mysql_num_rows($sql)==0){
		$letter_id = 1;
	}else{
		$str = Mysql_fetch_array($sql);
		$letter_id = $str['letter_id'] + 1;
	}
	$time = date("y-m-d h:i:s",time());
	Mysql_query("insert into taiwan_cain_2nd.postal (occ_time,send_charac_name,receive_charac_no,item_id,add_info,gold,letter_id) VALUES ('$time','DNF admin','$char_no','$code','$num','$gold','$letter_id')");
}

//登录游戏
if($temp[1]=='login'){
	$username = $temp[2];
	$password = $temp[3];
	$mac = $temp[4];
	$ip = $_SERVER['REMOTE_ADDR'];
	$sql = Mysql_query("select * from d_taiwan.accounts where accountname='$username' and password='$password'");

    if(Mysql_num_rows($sql)==0){
		echo str_encode("Username or password Error",$msgkey,$rand);
		exit;
	}else{
		$sql_mac = Mysql_query("select * from d_gmaster.blacklist_mac where mac='$mac'");
		$sql_ip = Mysql_query("select * from d_gmaster.blacklist_ip where ip='$ip'");
		if(Mysql_num_rows($sql_mac) || Mysql_num_rows($sql_ip)){ 
			echo str_encode("login error",$msgkey,$rand);
			exit;
		}
		
		$str = Mysql_fetch_array($sql);
		$uid = $str['UID'];
		
		$sql_not = Mysql_query("select * from d_taiwan.member_punish_info where m_id='$uid'");
		if(Mysql_num_rows($sql_not)){ 
			echo str_encode("login not",$msgkey,$rand);
			exit;
		}
		
		$sql_login = Mysql_query("select * from d_gmaster.login where uid='$uid'");
		$time = date("y-m-d h:i:s",time());
		
		if(Mysql_num_rows($sql_login)==0){
			Mysql_query("insert into d_gmaster.login (uid,reg_time,login_time,mac,ip) VALUES ('$uid','$time','$time','$mac','$ip')");
		}else{
			$str_login = Mysql_fetch_array($sql_login);
			$login_num = $str_login['login_num'] + 1;
			Mysql_query("update d_gmaster.login set login_time='$time',login_num='$login_num',mac='$mac',ip='$ip' where uid='$uid'");
		}
		
		$data = sprintf("%08x010101010101010101010101010101010101010101010101010101010101010155914510010403030101",$uid);
		$data = hex2tobin($data);
		$encrypted = "";
		$pi_key =  openssl_pkey_get_private($private_key);
		openssl_private_encrypt($data,$encrypted,$pi_key);
		$encrypted = base64_encode($encrypted);
		echo str_encode($encrypted,$msgkey,$rand);
	    Mysql_query("update d_taiwan.limit_create_character set count=0 where m_id='$uid'");//取消角色创建限制
		exit;
	}
}

function rc4($data,$pwd){
	$key[] ="";
	$box[] ="";
	$pwd_length = strlen($pwd);
	$data_length = strlen($data);
	for ($i = 0; $i < 256; $i++)
	{
		$key[$i] = ord($pwd[$i % $pwd_length]);
		$box[$i] = $i;
	}
	for ($j = $i = 0; $i < 256; $i++)
	{
		$j = ($j + $box[$i] + $key[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for ($a = $j = $i = 0; $i < $data_length; $i++)
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;

		$k = $box[(($box[$a] + $box[$j]) % 256)];
		$cipher .= chr(ord($data[$i]) ^ $k);
	}
	return $cipher;
}

function HexTostr($s){
	$r = "";
	for ( $i = 0; $i<strlen($s); $i += 2)
	{
		$x1 = ord($s{$i});
		$x1 = ($x1>=48 && $x1<58) ? $x1-48 : $x1-97+10;
		$x2 = ord($s{$i+1});
		$x2 = ($x2>=48 && $x2<58) ? $x2-48 : $x2-97+10;
		$r .= chr((($x1 << 4) & 0xf0) | ($x2 & 0x0f));
	}
	return $r;
}

function strToHex($s){
	$r = "";
	$hexes = array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
	for ($i=0; $i<strlen($s); $i++) {$r .= ($hexes [(ord($s{$i}) >> 4)] . $hexes [(ord($s{$i}) & 0xf)]);}
	return $r;
}

function str_decode($str,$key,$key_rand){
	return (string)rc4(HexTostr((string)rc4(HexTostr($str),(string)$key_rand)),$key);
}

function str_encode($str,$key,$key_rand){
	$key_temp = strToHex(rc4($str,(string)$key_rand));
	$key_temp = strtoupper(strToHex(rc4($key_temp,$key)));
	return $key_temp;
}

function GET_Temp_Key(){
	if(function_exists('com_create_guid')){
		return com_create_guid();
	}else{
		mt_srand((double)microtime()*10000);
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = chr(123)// "{"
		.substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12)
		.chr(125);// "}"
		return $uuid;
	}
}

function hex2tobin( $str ) {
	$sbin = "";
	$len = strlen( $str );
	for ( $i = 0; $i < $len; $i += 2 ) {
		$sbin .= pack( "H*", substr( $str, $i, 2 ) );
	}
	return $sbin;
}

?>
