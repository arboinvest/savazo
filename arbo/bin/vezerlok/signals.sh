#!/bin/env bash
while [ true ]; do
    sleep 4
    if [ -f "/home/pi/arbo/reset/rebootP1" ]; then
		rm /home/pi/arbo/reset/rebootP1
		touch /home/pi/arbo/reset/p1
		echo 'Send reboot signal to P1'
		sshpass -p 'r2' ssh -t pi@192.168.5.152 'sudo killall python2.7 ; sudo reboot' &
    elif [ -f "/home/pi/arbo/reset/rebootP2" ]; then
		rm /home/pi/arbo/reset/rebootP2
		touch /home/pi/arbo/reset/p2
		echo 'Send reboot signal to P2'
		sshpass -p 'r2' ssh -t pi@192.168.5.151 'sudo killall python2.7 ; sudo reboot' &
    fi
done

