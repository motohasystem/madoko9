<?php
	function stag($tag, $val) {
		return '<'.$tag.' '.$val.' />';
	}
	
	function dtag($tag, $val, $atr='') {
		return '<'.$tag.' '.$atr.'>'.$val.'</'.$tag.'>';
	}
	
	// Input
	function input($param) {
		$str = '';
		foreach ($param as $k => $v) {
			$str .= $k . '="' . $v . '" ';
		}
		return stag('input', $str);
	}

	// テーブル関連
	function table($val) {
		return dtag('table', $val);
	}

	function thead($val) {
		return dtag('thead', $val);
	}

	function tbody($val) {
		return dtag('tbody', $val);
	}
	
	function tr($val) {
		return dtag('tr', $val);
	}
	
	function th($val, $param = null) {
		if (!is_null($param)) {
			$str = '';
			foreach ($param as $k => $v) {
				$str .= $k . '="' . $v . '" ';
			}
			return dtag('th', $val, $str);
		} else {
			return dtag('th', $val);
		}
	}
	
	function td($val, $param = null) {
		if (!is_null($param)) {
			$str = '';
			foreach ($param as $k => $v) {
				$str .= $k . '="' . $v . '" ';
			}
			return dtag('td', $val, $str);
		} else {
			return dtag('td', $val);
		}
	}
	
