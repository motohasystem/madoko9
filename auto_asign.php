<?php
/*
	Event Rickshaw API（デモ用自動配車）

▼呼び出しは下記の形態となることを想定する。
 http://xxxx/auto_asign.php?id=XX

▼パラメータ一覧
	id	ID	IDentifier	レコードID（自動配車対象レコード）
*/

require('./data.php');

date_default_timezone_set('Asia/Tokyo');

define("FILE_DIRNAME",  ".\\");	// データファイルディレクトリパス
define("FILENAME",  "reservation.json");	// データファイル名
define("HOSTNAME", "eventrickshaw.azurewebsites.net");
define("API_NAME", "send_message.php");

// Basic Auth
if(!isset($_SERVER["PHP_AUTH_USER"])) {
    header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
    header("HTTP/1.0 401 Unauthorized");
    //キャンセル時の表示
    echo "Authorization Required";
    exit;
}
else {
    if(!AuthenticateUser($_SERVER["PHP_AUTH_USER"],$_SERVER["PHP_AUTH_PW"])){
        //認証エラーの処理
        header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
        header("HTTP/1.0 401 Unauthorized");
        //キャンセル時の表示
        echo "Authorization Required";
        exit;
	}
}

// ひとまずGETのみ
if( !empty($_GET['rt']) ) {
	$method = "GET";
	$PARAMS = explode('&', $_SERVER['QUERY_STRING']);
}else{
	//  テスト用
	$PARAMS = explode('&', "rt=090121262756305&tm=20130831170000&cn=001&se=20130831160000&st=1&fi=5&pt=30&pa=3");
//	array_push($PARAMS, 'id=5');
//	http://xxxxx/erapi.php?id=5&rt=090121262756305&tm=20130831170000&cn=001&se=20130831160000&st=1&fi=5&pt=30&pa=3");
//	http://xxxxx/erapi.php?id=5&rt=090121262756305&tm=20130831170000&cn=001&se=20130831160000&st=1&fi=5&pt=30&pa=3");
}

if(empty($_GET['id'])) {
	print "no id error";
	exit;
}

$id = $_GET['id'];

$records = load(FILE_DIRNAME . FILENAME);
//-----------------------------------


$today = new DateTime();
$records->$id->{'SENTTIME'} = $today->format('YmdHis');
$car_no = sprintf("%03d",rand(1,5));
$records->$id->{'CARNO'} = $car_no;

$result["SENDRESULT"] = sendMessage($records->$id->{'RESTEL'}, $records->$id->{'CARNO'}, $records->$id->{'START'}, $records->$id->{'PICKUP_TIME'});


//-----------------------------------
if( save($records) != -1) {
	$result["RESULT"] =  "OK";
}
else{
		$result["RESULT"] =  "ERROR";
}

$ret = json_encode( $result );
echo $ret;

exit;

// JSONをファイルに保存
function save($records){
	$serialized = json_encode($records);
	$result = array();
	
	$filename = FILE_DIRNAME . FILENAME;
	if( file_put_contents($filename, $serialized) ){
		return 1;
	}
	else{
		return -1;
	}
}

// JSONファイルを読み込み
function load($filename){
	$records = (object)array();
	
	if( file_exists($filename) ){
		$data = file_get_contents($filename);
		$records = json_decode($data);
	}
	return $records;
}
// メッセージ送信
function getMessage($no,$pos,$wait){
	$msg = "迎えの車は、". $no . "番が" . $pos . "番地点に迎えに行くよう手配したけん、" . $wait . "分ほど待っとってな。\n※デモのため実際には配車されません！";
	return $msg;
}

function sendMessage($tel_num,$no,$pos,$wait){

	$msg = urlencode(getMessage($no,$pos,$wait));

	$crypt_td = getEncoder();
	$c_tel_num = base64_encode(mcrypt_generic($crypt_td, $tel_num));
	closeEncoder($crypt_td);

	$params = array(
		'To=' . $c_tel_num,
		'Message=' . $msg
	);
		
	$get_param = join("&", $params);
	$request = "https://". getenv("OMADOKO_OPE_USER") . ":" . getenv("OMADOKO_OPE_PWD") . "@" . HOSTNAME . "/" . API_NAME . "?" . $get_param;


	$result = file_get_contents($request);

	return $result;
}

function getEncoder() {
	//暗号化＆復号化キー
	$crypt_key = md5(getenv("TWILIO_AUTH_TOKEN"));

	//暗号化モジュール使用開始
	$crypt_td  = mcrypt_module_open('des', '', 'ecb', '');
	$crypt_key = substr($crypt_key, 0, mcrypt_enc_get_key_size($crypt_td));
	$crypt_iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($crypt_td), MCRYPT_RAND);

	//暗号化モジュール初期化
	if (mcrypt_generic_init($crypt_td, $crypt_key, $crypt_iv) < 0) {
	  exit('error.');
	}
	return $crypt_td;
}
function closeEncoder($td) {
	//暗号化モジュール使用終了
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
}
function AuthenticateUser($user,$pwd){
    //環境変数を使ってユーザーチェック
    $authuser = getenv("OMADOKO_OPE_USER");
    $authpwd = getenv("OMADOKO_OPE_PWD");
    if(($user==$authuser) && ($pwd==$authpwd)) return true;
    return false;
}
?>