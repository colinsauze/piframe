<!--
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
-->                                                                
                                                                
                                                                
<!DOCTYPE HTML>
<html>
<body>

<form id="dataform" action="savelocation.php" method="post">
<input type="hidden" id="lat" name="lat">
<input type="hidden" id="lon" name="lon">
<label for="name">Your Name</label>
<input type="text" id="name" name="name">
<input type="submit" id="submit" value="Check In">
</form>


<script type="text/javascript">

window.onload = send_pos();

function send_pos() {
  var startPos;

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      startPos = position;
      document.getElementById("lat").value = startPos.coords.latitude;
      document.getElementById("lon").value = startPos.coords.longitude;
      /*document.getElementById("dataform").submit();*/
    }, function(error) {
       
       switch(error.code)
       {
		case 1:
			alert("Error occurred. Permission Denied");
			break;
		case 2:
			alert("Error: Position is unavailable, check you have location services enabled");
			break;
		case 3:
			alert("Error: Timeout getting location");
			break;
		default:
			alert("Error: Unknown Error");
			break;
      }
      
      // error.code can be:
      //   0: unknown error
      //   1: permission denied
      //   2: position unavailable (error response from locaton provider)
      //   3: timed out
    });
  }
}

</script>

</body>

</html>
