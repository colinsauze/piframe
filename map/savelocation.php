<?php
/*
An internet enabled TV photo frame for the Raspberry Pi
Copyright (C) 2013 Colin Sauze
      
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.
                        
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
                         
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Foundation,
Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
*/
$name = $_POST['name'];
$lat = $_POST['lat'];
$lon = $_POST['lon'];

$fptr = fopen("~/map/checkin.".$name,"w");
fwrite($fptr,$lat.",".$lon."\n");
fclose($fptr);
echo "<html><body>";
echo "<h1>Location Saved</h1>";
echo "<p><a href=\"http://www.openstreetmap.org/?lat=".$lat."&lon=".$lon."&zoom=7&layers=M\">Verify Location</a></p>";
echo "</body></html>";
?>
