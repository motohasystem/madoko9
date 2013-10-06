var getMessage = function(no) {
	var msg = "迎えの車、";
	msg += no + "番で手配したけんちょっと待っとってな。";
	return msg;
}

var getAPICode = function() {
	$.ajax({
		type: 'POST',
		url: './ajax_getApiCode.php',
		data: null,
		success: function(code) {
			alert(code);
		},
		error: function() {
		},
		timeout: 6000
	});
}

var getLocation = function(location) {
	var temp = location.split("?");
	return temp[0];
}

var sendsms = function(self) {
	var id = self.attr("id").replace("sendsms", "");
	var targ = $("select#carno" + id + " option:selected");
	if (targ.val() == '0') {
		alert('配車番号を選択してください。');
	} else {
		var tel_no = $("td.telno" + id + " :hidden").val(),
		    tel_no_txt = $("td.telno" + id).text();
		alert(tel_no_txt + 'にSMSメッセージを送信します。');
		$.ajax({
			type: 'POST',
			url: './send_message.php',
			data: {
				To:tel_no,
				Message:getMessage(targ.text())
			},
			success: function( json ) {
				alert('送信しました。');
				location.href = getLocation(location.href) + "?act=sent&id=" + id + "&carno=" + targ.text();
			},
			error: function() {
			},
			timeout : 120000
		});
	}
};

$(document).ready(function(){
	$("input.edit").click(function(){
		var id = $(this).attr("id").replace("edit", "");
		location.href = getLocation(location.href) + "?act=edit&id=" + id;
	});
	$("input.sendsms").click(function(){
		sendsms($(this));
	});
});
