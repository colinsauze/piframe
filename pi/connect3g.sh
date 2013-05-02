#!/bin/sh

#An internet enabled TV photo frame for the Raspberry Pi
#Copyright (C) 2013 Colin Sauze
      
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 3 of the License, or
#(at your option) any later version.
                  
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
 
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software Foundation,
#Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
                                       


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

