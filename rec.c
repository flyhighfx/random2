#include <stdio.h>
int main(){
	int a=0,c=0,b=0;
	do{
		a = system("sox -r 41000 -t alsa default recording.flac silence 1 0.1 1% 1 1.5 1%");
		if(a==0){
			b = system("wget -q -U 'rate=41000' -O - 'http://www.google.com/speech-api/v1/recognize?lang=en-IN&client=Mozilla/5.0' --post-file recording.flac --header='Content-Type: audio/x-flac; rate=41000'");
		}
	}while(1!=1);
	return 0;
}
