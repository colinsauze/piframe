<?php
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
