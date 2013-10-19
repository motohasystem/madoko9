<?php
	require('./data.php');
	require('./dom.php');

	date_default_timezone_set('Asia/Tokyo');

	$conv = array(
		"ID"          => "ID",
		"RESTEL"      => "予約者電話",
		"TIME"        => "登録時刻",
		"CARNO"       => "配車番号",
		"SENTTIME"    => "配車状況",
		"START"       => "出発No",
		"FINISH"      => "到着No",
		"PICKUP_TIME" => "配車時刻",
		"PARTY"       => "人数"
	);
	$editable = array(
		"TIME",
		"CARNO",
		"SENTTIME",
		"START",
		"FINISH",
		"PICKUP_TIME",
		"PARTY"
	);
	$num_column = array(
		"ID",
		"CARNO",
		"START",
		"FINISH",
		"PARTY"
	);
	$to_date = array(
		"TIME",
		"SENTTIME"
	);
	$car_no = array(
		'---',
		'001',
		'002',
		'003',
		'004',
		'005'
	);

	$filename = "./reservation.json";
	
	function strtodate($from) {
		$to  = substr($from, 0, 4) . '/' . substr($from, 4, 2) . '/' . substr($from, 6, 2) . ' ';
		$to .= substr($from, 8, 2) . ':' . substr($from, 10, 2);
		return $to;
	}
	
	function datetostr($from) {
		return str_replace(array('/', ' ', ':'), '', $from);
	}

	function formattelnum($from) {
		$to = "----";
		if(strlen($from)>=5) {
			$to = "0".substr($from,3,2)."-****-".substr($from,-4,4);
		}
		return $to;
	}
	
	function drawData() {
		global $data, $edit_id, $conv, $editable, $num_column, $to_date, $car_no;
		
		$rdata = array_reverse($data);

		$crypt_td = getEncoder();
		
		if(isset($rdata) and !empty($rdata)) {
			$tbl = '';
			foreach ($rdata as $row) {
				$tbl .= '<tr>';
				foreach (array_keys($conv) as $key) {
					if (in_array($key, $to_date)) {
						if (isset($row[$key]) and strlen($row[$key]) > 0) {
							$row[$key] = strtodate($row[$key]);
						} else {
							$row[$key] = '-';
						}
					}
					if (isset($edit_id) and $edit_id === $row['ID']) {
						if (in_array($key, $num_column)) {
							$tbl .= '<td class="num">';
						} else {
							$tbl .= '<td>';
						}
						if (in_array($key, $editable)) {
							$tbl .= '<input type="text" id="'.$key.'" name="'.$key.'" value="'.$row[$key].'" />';
						} else {
							if ($key === 'RESTEL') {
								$tbl .= formattelnum($row[$key]) ;
							} else {
							$tbl .= $row[$key] . '<input type="hidden" id="'.$key.'" name="'.$key.'" value="'.$row[$key].'" />';
							}
						}
						$tbl .= '</td>';
					} else {
						if ($key === 'CARNO') {
							$tbl .= '<td><select id="carno' . $row['ID'] . '">';
							foreach ($car_no as $i => $c) {
								$tbl .= '<option id="no" value="' . $i . '"'.(((0+$row[$key])===(0+$i))?' selected ':'').'>' . $c . '</option>';
							}
							$tbl .= '</select></td>';
						} elseif ($key === 'RESTEL') {
							$tbl .= '<td class="telno' . $row['ID'] . '">' . formattelnum($row[$key]) . '<input type="hidden" id="C_'.$key.'" name="C_'.$key.'" value="'. base64_encode(mcrypt_generic($crypt_td, $row[$key])) . '" /></td>';
						} else {
							$tbl .= td($row[$key], array("id" => $key . $row['ID']));
						}
					}
				}
				if (isset($edit_id)) {
					if ($edit_id === $row['ID']) {
						$tbl .= td(input(array("type" => "submit", "id"=>"set".$row['ID'], "name" => "set", "value"=>"確定")));
					} else {
						$tbl .= td('');
					}
					$tbl .= td('<input type="button" id="sendsms' . $row['ID'] . '" class="sendsms" value="配車" disabled="disabled" />');
				} else {
					$tbl .= td('<input type="button" id="edit' . $row['ID'] . '" class="edit" value="変更">');
					$tbl .= td('<input type="button" id="sendsms' . $row['ID'] . '" class="sendsms" value="配車" />');
				}
				$tbl .= '</tr>';
			}
			print $tbl;
		} else {
			print '<p>データがありません</p>';
		}
		closeEncoder($crypt_td);
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

	$data = readData($filename);
	if (isset($_POST["set"])) {
		unset($_POST["set"]);
//		$data[$_POST['ID']] = $_POST;

		$i = $_POST['ID'];
		$row = $_POST;
//		foreach ($data as $i => $row) {
			foreach ($row as $k => $v) {
			  if (in_array($k, $editable)) {
				if (in_array($k, $to_date)) {
					$data[$i][$k] = datetostr($v);
				} else {
					$data[$i][$k] = $v;
				}
			  }
			}
//		}
		setData($filename, $data);
	} elseif (isset($_GET["act"]) and $_GET["act"] === 'edit') {
		$edit_id = $_GET["id"];
	} elseif (isset($_GET["act"]) and $_GET["act"] === 'sent') {
		$data[$_GET['id']]['SENTTIME'] = date("YmdHis", time());
		$data[$_GET['id']]['CARNO'] = $_GET['carno'];
		setData($filename, $data);
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />

		<title>まど子さんによる配車予約サービス</title>
		<link rel="stylesheet" href="./css/index.css" type="text/css" /> 
		<script type="text/javascript" src="./js/lib/jquery-1.10.2.min.js" ></script>
		<script type="text/javascript" src="./js/index.js?3" ></script>
	</head>
	<body>
		<img src="./img/map.png" alt="地図" />
		<form id="main" action="./" method="post">
			<input type="submit" id="upd" name="upd" value="更新" />
			<hr />
			<table>
				<thead>
					<tr>
						<th class="num">ID</th>
						<th>予約者電話</th>
						<th>登録時刻</th>
						<th>配車番号</th>
						<th>配車状況</th>
						<th class="num">出発No</th>
						<th class="num">到着No</th>
						<th>配車時刻</th>
						<th class="num">人数</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php drawData($data) ?>
				</tbody>
			</table>
			<br/>
			<!--
			<input type="submit" id="set" name="set" value="設定" />
			-->
		</form>
	</body>
</html>