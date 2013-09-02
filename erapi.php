<?php
/*
	Event Rickshaw API

▼呼び出しは下記の形態となることを想定する。
 http://xxxx/erapi.php?m=reg&....

▼パラメータ一覧
	id	ID	IDentifier	レコードID（通し番号、修正・完了のときに使用）
	rt	RESTEL	Reservation telephone	予約者電話番号
	tm	TIME	Time	予約依頼時刻
	cn	CARNO	Car Number	配車番号（管理者）
	se	SENTTIME	SEnt time	配車依頼時刻（管理者）
	st	START	STart	出発地点番号
	fi	FINISH	FInish	到着地点番号
	pt	PICKUP_TIME	Pickup Time	ピックアップ予定時刻
	pa	PARTY	PArty Count	申し込み人数
*/

define("FILE_DIRNAME",  ".\\");	// データファイルディレクトリパス
define("FILENAME",  "reservation.json");	// データファイル名


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
$new_record = _params_to_record( $PARAMS );
//var_dump($new_record);
$records = load(FILE_DIRNAME . FILENAME);
if(empty($new_record['ID'])){
	$id = _get_new_id($records);
	$new_record['ID'] = (string)$id;
}
else{
	$id = $new_record['ID'];
}

$records->$id = $new_record;
if( save($records) != -1) {
	$result["RESULT"] =  "OK";
}
else{
		$result["RESULT"] =  "ERROR";
}
$ret = json_encode( $result );
echo $ret;

exit;

// 新規追加するIDを取得
function _get_new_id($records){
	$max = 0;
	foreach($records as $key => $value) {
		$max = $max < intval($key) ? intval($key) : $max;
	}
	return $max + 1;
}

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

// GETパラメータ（ex. m=edt）の配列をひとつのレコードに変換する
function _params_to_record($params){
	$record = array();
	foreach($params as $p){
		$set  = explode("=", $p);
		switch($set[0]){
			case "id":
				$record["ID"] = $set[1];
				break;
			/*
			case "m":
				$record[""] = $set[1];
				break;
			*/
			case "rt":
				$record["RESTEL"] = $set[1];	// 予約者電話番号
				break;
			case "tm":
				$record["TIME"] = $set[1];	// 予約受付時刻
				break;
			case "cn":
				$record["CARNO"] = $set[1];	// 配車済み車体番号
				break;
			case "se":
				$record["SENTTIME"] = $set[1];	// 配車依頼済み時刻
				break;
			case "st":
				$record["START"] = $set[1];	// 乗車地点番号
				break;
			case "fi":
				$record["FINISH"] = $set[1];	// 降車地点番号
				break;
			case "pt":
				$record["PICKUP_TIME"] = $set[1];	// ピックアップ予定時刻
				break;
			case "pa":
				$record["PARTY"] = $set[1];	// 同乗者の人数
				break;
		}
	}
	return $record;
}
?>
