#!/bin/bash

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
                                                                
                                                                
                                                                

. ./config.sh
path=$scphost:$upload_path

#get the server's copy of the checksum list
rm photos.sum.server
rm *.enc
scp -P $port $path/photos.sum photos.sum.server

changed=0
copylist=""
for i in `ls -rt uploads/*.jpg | tail -15` ; do
    sum=`sha1sum $i | awk '{print $1}'`
    gpg --always-trust -e -r $recipient < $i > tmpfile
    mv tmpfile $sum.enc

    grep $sum photos.sum.server > /dev/null
    if [ "$?" != "0" ] ; then
	echo "file $sum.enc is not on server, copying"
	#file is not in the server's checksum list
	copylist=$copylist" "$sum.enc
	changed=1
    else
	echo "file $sum is already on server"
    fi
done

ls *.enc > photos.sum

if [ "$changed" = "1" ] ; then

    scp -P $port photos.sum $copylist $path
    rm email_downloads/*
fi

#find files which are on server, but not stored locally. These are old files which need to be deleted.
if [ -f "photos.sum.server" ] ; then

for i in `cat photos.sum.server` ; do
    grep $i photos.sum > /dev/null
    if [ "$?" != "0" ] ; then
	echo "file $i is no longer on server, deleting"
	username=`echo $path | awk -F: '{print $1}'`
	dir=`echo $path | awk -F: '{print $2}'`
	cmd="rm $dir/$i"
	ssh $username -p $port $cmd
	scp -P $port photos.sum $path
    fi
    
done

fi