var now_updating = false;
var now_posting = false;
var state = null;

var beepsound=null;
var skipped_beep_times=0;

var max_messages=null;

$(function(){
	max_messages = Number($("#chatarea").attr('data-frontend-maxmessages'));
	
	chatInteraction();
	
	chatUpdate(false, true);
	setInterval( function(){
		chatUpdate(false, false)
	}, 500 );
});

function chatInteraction(){
	$("#chatsender").keydown(function(event) {
		var key = event.which;  
		if (key >= 33) {
			var maxLength = $(this).attr("data-frontend-maxlength");  
			var length = this.value.length;  
			
			// don't allow new content if length is maxed out
			if (length >= maxLength) {  
				event.preventDefault();  
			}  
		}  
	});
	
	// watch textarea for release of key press
	$('#chatsender').keyup(function(e) {
		if (e.keyCode == 13) {
			sendProcessor();
		}
	});
	
	$("#alternativesender").click(function(){
		sendProcessor();
	});
}

function sendProcessor(){	
	var text = $.trim($('#chatsender').val());
	var maxLength = $('#chatsender').attr("data-frontend-maxlength");  
	var length = text.length; 
	
	if(length>0){
		// send 
		if (length <= maxLength + 1) { 
			chatSend(text, name);  
		} else {
			$('#chatsender').val(text.substring(0, maxLength));
		}
	}  
}

function chatSend(message, nickname){
	if(!now_posting){
		$("#chatsender").prop('disabled', true);
		
		$.ajax({
			type: "POST",
			url: "chat.php",
			data: {'function':'send', 'message':message, 'meta':chat_meta},
			dataType: "json",
			success: function(data){
				chatUpdate(true, true);
				$('#chatsender').val("");
			},
			error: function(){
				//$("#chatsender").prop('disabled', false).focus();
			},
			complete: function(){
				now_posting=false;
				//disabled prop disables itself only after we update the chat inside chatUpdate
				//not anymore 
				$("#chatsender").prop('disabled', false).focus();
			}
		});
	}
}

function chatUpdate(forceUpdate, forceScrollDown) {
	if((!now_posting && !now_updating) || forceUpdate){
		now_updating = true;
		$.ajax({
			type: "POST",
			url: "chat.php",
			data: {'function':'update', 'state':state},
			dataType: "json",
			success: function(data) {
				if(data.text){
					var max = (document.getElementById('chatarea').scrollHeight - document.getElementById('chatwrap').clientHeight);
					var current = document.getElementById('chatwrap').scrollTop;
					
					for (var i = 0; i < data.text.length; i++) {
						$('#chatarea').append($(data.text[i]));
					}
					
					if(max_messages !== null){
						var messages = $("#chatarea li");
						var i=1;
						messages.each(function(){
							if(i < messages.length-max_messages){
								$(this).remove();
							} 
							i++;
						});
					}
					
					var scroll_condition = (current > max-50);
					if( forceScrollDown || scroll_condition ){
						document.getElementById('chatwrap').scrollTop = document.getElementById('chatarea').scrollHeight;
					}
					
					if(typeof playsound != "undefined" && playsound==true){
						if(skipped_beep_times<1){
							skipped_beep_times++;
						}else{
							if(typeof playsound != "undefined" && playsound==true){
								beepsound = new Howl({ src: 'gfx/beep.mp3' });
							}  	
							beepsound.play();
						}
					}  	
				}
				state = data.state;
			},
			complete: function(){
				now_updating = false;
			}
		});
	}
}                                              