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
