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



killall fbi

fbi -vt 1 --noverbose /home/pi/piframe/pi/connecting.png

connected=false

/home/pi/piframe/pi/connect3g.sh

ntpdate 0.debian.pool.ntp.org

#sleep 5

killall fbi

fbi -vt 1 --noverbose /home/pi/piframe/pi/gettingpics.png

path="http://mydomain.com/piframe/"

cd /tmp

mkdir photos

while [ "0" = "0" ] ; do

    wget $path/photos.sum?time=`date +%s` -O photos.sum.new
    
    touch photos.sum
    
    changed=0

    #test if we have each of the new photos
    for i in `cat photos.sum.new` ; do
	sum=`echo $i | awk -F. '{print $1}'`
	grep $sum photos.sum > /dev/null
	if [ "$?" != "0" ] ; then
	    #current file is not in the list, get it
	    wget $path/$sum.enc?time=`date +%s` -O $sum.enc
            #gpg --home /root/.gnupg/
            gpg --homedir /home/pi/.gnupg -d --batch --output photos/$sum.jpg $sum.enc 
            changed=1
	fi
    done
    
    cp photos.sum.new photos.sum
    
    #now we should really delete anything which no longer exists on the server
    for i in `ls *.enc` ; do
	grep $i photos.sum > /dev/null
	if [ "$?" != "0" ] ; then
	    echo "$i exists locally, but not on server, deleting"
	    rm $i
	fi
    done

    for i in `ls photos/*.jpg` ; do
	sum=`echo $i | awk -F/ '{print $2}' | awk -F. '{print $1}'`
	grep $sum photos.sum > /dev/null
	if [ "$?" != "0" ] ; then
	    echo "$i exists locally, but not on server, deleting"
	    rm $i
	fi
    done

    if [ "$changed" = "1" ] ; then
        killall fbi
        fbi -vt 1 --noverbose -t 15 /tmp/photos/*.jpg &
    fi

    hour=`date +%H`
    if [ "$hour" -ge "8" ] ; then
	sleep 60
    else
	sleep 1h
    fi

    if [ "$hour" = "04" ] ; then
	reboot
    fi

done





