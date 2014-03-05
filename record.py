import subprocess

subprocess.call("sox -r 16000 -t alsa default recording.flac silence 1 0.1 1% 1 1.5 1%")
