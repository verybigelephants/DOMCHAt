<?php
	$function = isset($_POST['function']) ? $_POST['function'] : null;
	$db = 'db/chat.db';
	
	$nick_maxlength = 40;
	global $maxlength;
		$maxlength = 500;
	global $cutmessages;
		$cutmessages = 300;
	
	if($function!==null){
		$log = array();
		switch($function) {
			case('update'):
				if(!isset($_POST['state']) || $_POST['state']==null || $_POST['state']==false){ $_POST['state']="0,0"; }
				$state = explode(",", $_POST['state']);
					$state_lines=intval($state[0]);
					$state_fsize=intval($state[1]);
				
				$lines = "";
				$count = 0;
				$fsize = 0;
				if(file_exists($db)){
					$lines = file($db);
					$count = count($lines);
					$fsize = filesize($db);
				}
				
				if($state_lines == $count && $state_fsize == $fsize){
					 $log['state'] = $_POST['state'];
					 $log['text'] = false;
				}else{
					$text= array();
					$log['state'] = implode(",", array($count,$fsize));
					if($state_lines==0){
						foreach ($lines as $line_num => $line){
							if($line_num >= ($count-$cutmessages-1) ){
								$text[] =  $line = str_replace("\n", "", $line);	
							}
						}
					}else{
						foreach ($lines as $line_num => $line){
							if($line_num >= $state_lines){
								$text[] =  $line = str_replace("\n", "", $line);
							}
						}
					}
					$log['text'] = $text; 
				}
				
				break;
			 
			case('send'):
				$nickname = trim(htmlentities(strip_tags($_POST['meta']['nickname']), ENT_COMPAT, 'UTF-8'));
				$message = trim(htmlentities(strip_tags($_POST['message']), ENT_COMPAT, 'UTF-8'));
				if(strlen($message)>$maxlength){
					$message=substr($message, 0, $maxlength);
				}
				if(strlen($nickname)>$nick_maxlength){
					$nickname=substr($nickname, 0, $nick_maxlength);
				}
				
				if(($message) != "\n" && strlen($message)>0 ){
				    /*
					$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
					if(preg_match($reg_exUrl, $message, $url)) {
						$message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
					}
					*/ 
					
					fwrite(fopen($db, 'a'), "<li><span data-time='".date('Y-m-d H:i:s')."' class='nick'>".$nickname."</span><span class='message'>".$message=str_replace("\n", " ", $message)."</span></li>\n"); 
				}
				break;
			
		}
		
		header('Content-Type: application/json');
		echo json_encode($log);

	}
?>