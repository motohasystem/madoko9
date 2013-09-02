<?php
define("HOSTNAME", "http://eventrickshaw.azurewebsites.net/");
define("API_NAME", "erapi.php");
$WAIT_TIME = array(0, 15, 30, 60);

    header('Content-type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
 
    echo '<Response>';
 
    $CallSid = $_REQUEST['CallSid'];
    $CallerNumber = $_REQUEST['From'];
    # @start snippet
    $user_pushed = (int) $_REQUEST['Digits'];

/*
// テストコード
	$CallSid = "abcdefghijijefok";
	$CallerNumber = "00000000000";
	$user_pushed  = 1;
// テストコード/
*/

    # @end snippet
    $file = fopen( sprintf("%s.txt",$CallSid), "r" );
    $input_data = fgets($file);
    fclose( $file );
    $data_array = split(",",$input_data);

    if ($user_pushed == 1)
    {
	// 出発地点
	$start = $data_array[0];
	// 到着地点
	$finish = $data_array[1];
	// 配車までの待ち時間
	$wait_time = $WAIT_TIME[ $data_array[2] ];
	// 同乗人数
	$party_count = $data_array[3];
	// 登録時刻
	$today = new DateTime();
	$register_time = $today->format('YmdHis');
	
	$params = array(
		'rt=' . $CallerNumber,
		'st=' . $start,
		'fi=' . $finish,
		'pt=' . $wait_time,
		'pa=' . $party_count,
		'tm=' . $register_time
	);
		
	$get_param = join("&", $params);
	$request = HOSTNAME . API_NAME . "?" . $get_param;
	$result = file_get_contents($request);

        echo '<Say language="ja-JP">ありがとうございます、</Say>';
        echo '<Say language="ja-JP">ご希望の予約内容を登録しました。</Say>';
        echo '<Say language="ja-JP">車両の手配が出来た時点でこちらからご連絡さしあげます。</Say>';
        echo '<Say language="ja-JP">電話を切ってお待ちください</Say>';
        echo '<Hangup/>';
    }
    else if ($user_pushed == 2){
        // 出発地点の選択にリダイレクト
        echo '<Redirect method="GET">say_confirmation.php</Redirect>';
    }
    else if ($user_pushed == 3){
        // 出発地点の選択にリダイレクト
        echo '<Redirect method="GET">select_start_point.xml</Redirect>';
    } 
    echo '</Response>';
?>