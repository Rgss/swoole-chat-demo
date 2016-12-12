/**
 * common
 * 
 * $id zhang
 * 
 */


var Z = {
	
	redirect: function(url, delay) {
		delay = delay || 10;
		setTimeout('Z._go("'+ url +'")', delay);
	},

	delayRedirect: function (url, delay) {
		delay = delay || 1000;
		setTimeout('Z.redirect("'+ url +'")', delay);
	},
	
	_go: function(url) {
		window.location.href = url;
	},
}

// http tools
var Http = {
		
	// call
	get: function(url, params, callback) {
		$.get(url, params, function(result) {
			callback && callback(result);
		}, 'json');
	},
	
	// call
	call: function(url, params, callback) {
		$.post(url, params, function(result) {
			callback && callback(result);
		}, 'json');
	},
	
	// api jsonp
	api: function (url, params, callback) {
		$.ajax({
			async: false,
			url: url,
			type: "GET",
			dataType: 'jsonp',
			jsonp: 'callback',
			data: params,
			timeout: 5000,
			success: function(res){
				callback && callback(res);
			}
        });
	},
	
	// alert
	alert: function(url, params, msg, callback) {
		$.post(url, params, function(result) {
			msg = msg || result.data.msg;
			
			// show message
			Message.show(msg);
			
			// callback
			callback && callback(result);
		}, 'json');
	},
	
	go: function() {
		alert('go');
	},
};


// message
var Message = {
		
	// show
	show: function(msg, url, delay) {
		if ($('#system_message_layer').length > 0) {
			alert('操作太快了.');
			return;
		}
		
		var html = '<div id="system_message_layer" class="system_message_layer">';
			html += '<div id="system_message_bg" class="system_message_bg"></div>';
			html += '<div id="system_message_content" class="system_message_content">' + msg;
	    	html += '</div></div>';
	    $("body").append(html);
	    
	    var delay = delay || 2000;
	    var url = url || '';
	    setTimeout('Message.close("'+ url +'")', delay);
	},
	
	// 关闭
	close: function(url) {
		$('#system_message_layer').fadeOut("slow");
		$('#system_message_layer').remove();
		
		if (url) {
	    	location.href = url;
	    }
	},
		
};

