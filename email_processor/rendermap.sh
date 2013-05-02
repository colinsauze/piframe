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
                                                                
                                                                
                                                                
. ./config.sh
scp -P $port $scphost:$checkin_path/checkin.* .

for i in `ls checkin.* | sed 's/ /%20/g'` ; do
	#has the location changed?
	filename=`echo $i | sed 's/%20/ /g'`
	olddata=`cat "old.$filename"`
	data=`cat "$filename"`
	if [ "$olddata" != "$data" ] ; then
		cp "$filename" "old.$filename"
		name=`echo $i | awk -F. '{print $2}'`
		filename=`echo $i | tr ' ' '_'`
		lat=`echo $data | awk -F, '{print $1}'`
		lon=`echo $data | awk -F, '{print $2}'`
		if [ "$lat" != "" -a "$lon" != "" ] ; then
			url="http://ojw.dev.openstreetmap.org/StaticMap/?w=1280&h=650&lat=$lat&lon=$lon&z=8&mode=Export&mlat0=$lat&mlon0=$lon&fmt=jpg&show=1"
			echo $url
		        wget $url -O uploads/$filename.png
#			convert -quality 25 uploads/$name.png uploads/$name.jpg
			time=`date +"%I:%m %p on %A, %d %B"`
			text="$name\'s Location at $time"
			convert -quality 25 uploads/$filename.png  -pointsize 48 -fill white -undercolor black label:"$text" -background black  -gravity West -append uploads/$filename.jpg
			rm uploads/$filename.png
		fi
	fi
done


