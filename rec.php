<?php
wait();

function wait(){
	while(1==1){

		$a = exec("sox -r 41000 -t alsa default recording.flac silence 1 0.1 1% 1 1.5 1%");
/*		echo "recording.flac:: ".filesize("recording.flac")."\n";
		if(filesize("recording.flac")>1024*150) { echo "too long or noise";continue;}*/

		$b = exec("wget -q -U 'rate=41000' -O - 'http://www.google.com/speech-api/v1/recognize?lang=en-IN&client=Mozilla/5.0' --post-file recording.flac --header='Content-Type: audio/x-flac; rate=41000'");

		$b = json_decode($b);

		if(isset($b->hypotheses[0])){
			echo "SLEEP MODE:".$b->hypotheses[0]->utterance;

			$f = $b->hypotheses[0]->utterance;
			if(preg_match("/system/i", $f, $matches)){
				play("yessir");
				ask_commands();
			}
		}
	}
}

function ask_commands(){


	while(1==1){

		$a = exec("sox -r 41000 -t alsa default recording.flac silence 1 0.1 1% 1 1.5 1%");

		$b = exec("wget -q -U 'rate=41000' -O - 'http://www.google.com/speech-api/v1/recognize?lang=en-IN&client=Mozilla/5.0' --post-file recording.flac --header='Content-Type: audio/x-flac; rate=41000'");

		$b = json_decode($b);

		if(isset($b->hypotheses[0])){
			echo "ACTIVE MODE:".$b->hypotheses[0]->utterance;

			$f = $b->hypotheses[0]->utterance;
			if(preg_match("/listening/i", $f, $matches)){
				play("listening stopped");
				return;
			}
			if(preg_match("/shut[ ]?down in (\d+) minutes?/i", $f, $matches)){
				play("shutting down in $matches[1] minutes");
				forking("shutdown -h $matches[1]");
			}
			if($f == "cancel shutdown"){
				exec("shutdown -c");
				play("shutdown_cancelled");
			}
			if(preg_match("/play music/i", $f, $matches)){
				forking("rhythmbox-client --play");
			}
			if(preg_match("/pause/i", $f, $matches)){
				forking("rhythmbox-client --pause");
			}
			if(preg_match("/previous/i", $f, $matches)){
				forking("rhythmbox-client --previous");
				forking("rhythmbox-client --previous");
			}
			if(preg_match("/next/i", $f, $matches)){
				forking("rhythmbox-client --next");
			}
		}
		else play("sorry");
	}

}

function play($t){
	$t=str_replace(" ", "+", $t);
	if(!file_exists($t.".mp3")){
		echo "string\n\n\n";
		exec('wget -q -U Mozilla -O '.$t.'.mp3 "http://translate.google.com/translate_tts?tl=en-IN&q='.$t.'"');
		exec("lame --decode ".$t.".mp3 - | play -");
	}
	else
		exec("lame --decode ".$t.".mp3 - | play -");
	
}

function say($t){
	$t = str_replace(" ", "+", $t);
	exec('wget -q -U Mozilla -O output.mp3 "http://translate.google.com/translate_tts?tl=en-IN&q='.$t.'"');
	exec("lame --decode output.mp3 - | play -");
}

function forking($a){
	if($a=="") return 0;

	$pid = pcntl_fork();
	if ($pid == -1) {
		die('could not fork');
	} else if ($pid) {
		return;
		pcntl_wait($status); 
	} else {
     // we are the child
		$c = exec($a);
		die();
	}

}
function user($a){
		echo "string";
	if($a=="") return 0;

	$pid = pcntl_fork();
	if ($pid == -1) {
		die('could not fork');
	} else if ($pid) {
		pcntl_wait($status);
		return;
	} else {
     // we are the child
		exec("sudo su master");
		$c = exec($a);
		die();
	}

}


?>