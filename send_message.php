<?php
// Get the PHP helper library from twilio.com/docs/php/install
require_once('/Services/Twilio.php'); // Loads the library
 
    $To = $_REQUEST['To'];
    $Message = $_REQUEST['Message'];

    function AuthenticateUser($user,$pwd){
        //環境変数を使ってユーザーチェック
        $authuser = getenv("OMADOKO_OPE_USER");
        $authpwd = getenv("OMADOKO_OPE_PWD");
        if(($user==$authuser) && ($pwd==$authpwd)) return true;
        return false;
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
    if(!isset($_SERVER["PHP_AUTH_USER"])) {
        header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
        header("HTTP/1.0 401 Unauthorized");
        //キャンセル時の表示
        echo "Authorization Required";
        exit;
    }
    else {
        if(AuthenticateUser($_SERVER["PHP_AUTH_USER"],$_SERVER["PHP_AUTH_PW"])){
            //認証成功後の処理
// Your Account Sid and Auth Token from twilio.com/user/account
$sid = "ACb6a2a22711d50f770902b427997bfbb9"; 
$token = getenv("TWILIO_AUTH_TOKEN"); 
$client = new Services_Twilio($sid, $token);

$td = getEncoder();
$To = mdecrypt_generic($td, base64_decode($To));
closeEncoder($td);

 
$message = $client->account->sms_messages->create("+12077473988", $To, $Message, array());
echo $message->sid;
        }
        else {
            //認証エラーの処理
	        header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
	        header("HTTP/1.0 401 Unauthorized");
	        //キャンセル時の表示
	        echo "Authorization Required";
	        exit;
        }
    }


?>
