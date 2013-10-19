var getMessage = function(no,pos,wait) {
	var msg = "迎えの車は、";
	msg += no + "番が" + pos + "番地点に迎えに行くよう手配したけん、" + wait + "分ほど待っとってな。\n※デモのため実際には配車されません！";
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
	var s_pos = $("td#START" + id ).text(),
	    w_tim = $("td#PICKUP_TIME" + id ).text();
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
				Message:getMessage(targ.text(),s_pos,w_tim)
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
