/**
 * chat 
 * 
 * @author zhang
 * @date   2016-12-10
 */

(function(){

	// define chat
	window.Chat = function(socket, chatID) {
		
		this._socket = socket;
		this._chatID = chatID;
		
	};
	
	// socket resource
	Chat.prototype._socket = null;
	
	// chat dom
	Chat.prototype._chatID = null;
	
	
	// open
	Chat.prototype.open = function(e) {
		
		json = {'cmd':'1000','data':{'uid':0, 'message':'connect.'}};
		json = JSON.stringify(json);
		this.send(json); 
	};
	
	// close
	Chat.prototype.close = function(e) { 
		this.show('[close]');
	};
	
	// message
	Chat.prototype.message = function(e) {
		
		var data = JSON.parse(e.data);
		var type = data.type;
		var name = 'system';
		
		console.log(data);
		
		if (data.status == 0) {
			Message.show(data.content);
			return false;
		}
		
		switch (type) {
		
			// login
			case 1001:
				this._login(data);
				break;
				
			// register
			case 1002:
				this._register(data);
				break;
				
			// _message
			case 2001:
				this._message(data);
				break;
				
			// _message
			case 2002:
				this._message(data);
				break;
			
			// _message
			case 4001:
				this._online(data);
				break;
				
			default:
				// _message(data)
				break;
		}
		
	};
	
	// error
	Chat.prototype.error = function(e) {
		alert(e);
		console.log(e);
		var error = e.data || 'unkown error.';
		Message.show(error);
		return;
	};
	
	// send message
	Chat.prototype.send = function(message) {
		this._socket.send(message);
	};
	
	// show message
	Chat.prototype.show = function(message) {
	    var pre = document.createElement("p"); 
	    pre.style.wordWrap = "break-word"; 
	    pre.innerHTML = message; 
	    
	    $('#' + this._chatID).append(message);
	    // document.getElementById(this._chatID).appendChild(message); 
	};

	
	
	/**********************************业务逻辑代码***********************************/
	
	// login
	Chat.prototype._login = function(data) {
		var uid = data.uid;
		var token = data.token;
		var username = data.user.username;
		
		$('#login_box').hide();
		$('#user_box').show();
		
		$('#current_username').text(username);
		$('#uid').val(uid);
		$('#token').val(token);
	};
	
	// register
	Chat.prototype._register = function(data) {
		var uid = data.uid;
		var token = data.token;
		var username = data.user.username;
		
		$('#login_box').hide();
		$('#user_box').show();
		
		$('#current_username').text(username);
		$('#uid').val(uid);
		$('#token').val(token);
		
		$('#layer').hide();
	};
	
	// talk
	Chat.prototype._message = function(data) {

		if (data.status == 0) {
			Message.show(data.content);
			return;
		} else {
			if (data.user) {
				var name = data.user.username ;
			} else {
				var name = '匿名者';
			}
		};
		
		console.log(data);
		
		var style = '';
		var me = $('#uid').val();
		if (me == data.uid) {
			style = ' msg_item_me';
		}
		
		if (me != data.uid) {
			$('.notice').show();
		}
		
		var message = '<div class="msg_item'+ style + '">';
			message += '<div class="head">' + name + ':</div>';
			message += '<div class="content">' + data.content + '</div>';
			message += '<div class="time">' + data.date + '</div>';
			message += '</div>';
					
		this.show(message);
	};
	
	Chat.prototype._online = function(data) {
		
		var html = '';
		if (data.content.list) {
			for (var i in data.content.list) {
				html += '<li><a href="javascript:;" class="user">' + data.content.list[i].username + '</a></li>';
			}
		}

		$('#online_users').html(html);
	}
	
	/***********************************业务逻辑代码**********************************/
	
})();
