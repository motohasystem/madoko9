<?php

	function readData($fn) {
		$h = fopen($fn, "r");
		$d = fread($h, filesize($fn));
		fclose($h);
		return json_decode($d, true);
	}
	
	function setData($fn, $data) {
		$h = fopen($fn, "w");
		$d = fwrite($h, json_encode($data));
		fclose($h);
	}
