#!/bin/sh
APN="m2m.aql.net"
USER="user"
PASSWORD="pass"
    sudo mknod /dev/ppp c 108 0 #on squashfs this doesn't seem to exist even though we've put it in the filesystem and it being missing makes ppp fail
    
    sudo /home/pi/sakis3g status

    if [ "$?" = "0" ] ; then
        connected=true
    else
	connected=false
    fi


while [ "$connected" != "true" ] ; do

    sudo /home/pi/sakis3g connect USBINTERFACE="0" APN="CUSTOM_APN" CUSTOM_APN="$APN" APN_USER="$USER" APN_PASS="$PASSWORD"
    
    sleep 1

    sudo /home/pi/sakis3g status

    if [ "$?" = "0" ] ; then
        connected=true
    else
	connected=false
    fi

    

done

