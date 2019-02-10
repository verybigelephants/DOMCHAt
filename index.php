<?php session_start(); ?><!doctype html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>DOMCHAt</title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900&amp;subset=latin-ext" rel="stylesheet">
	<link href="gfx/chat.css" media="screen" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.12.0.min.js"></script>
	<script type="text/javascript" src="js/howler.2.0.15.js"></script>
	<script type="text/javascript">
		var chat_meta = {
			'nickname':'<?php
				if(isset($_SESSION["nick"])){
					echo($_SESSION["nick"]);
				}else{
					$current = intval(file_get_contents("db/visitor_name_current.db"));
					$names = file("db/visitor_names.db");
						$names = array_values(array_filter($names, "trim"));
					
					$n=trim($names[$current]);
					if(strlen($n)>0){
						if((mt_rand() / mt_getrandmax())>0.7){
							$extra_numbers=[666,69,13,98,420];
							$n .= $extra_numbers[array_rand($extra_numbers)];
						}
					}else{
						$n='visitor';//whatever
					}
					
					$_SESSION["nick"]=$n;
					echo($n);
					
					$current++;
					if($current>=count($names)){
						$current=0;
					}
					file_put_contents("db/visitor_name_current.db", $current);
				}
			?>'
		};
		var playsound=true;
	</script>
	<script type="text/javascript" src="js/chat.js"></script>
</head>
<body>
	<div class="chat">
		<h2>
			<span id="chat_name_actual">Chat</span>
			<input type="text" id="chat_name_changer" /> <input type="submit" value="Change" data-nick-maxlength="<?php require_once('chat.php'); global $nick_maxlength; echo($nick_maxlength); ?>" id="chat_name_changer_confirm" />
		</h2>
		<div class="discussionwrap" id="chatwrap">
			<ul class="discussion" id="chatarea" data-frontend-maxmessages="<?php require_once('chat.php'); global $cutmessages; echo($cutmessages); ?>" >
			</ul>
		</div>
		<div class="underdiscussion">
			<input type="text" id="chatsender" data-frontend-maxlength="<?php require_once('chat.php'); global $maxlength; echo($maxlength); ?>" />
			<span id="alternativesender" class="sender"></span>
		</div>
	</div>
</body>
</html>