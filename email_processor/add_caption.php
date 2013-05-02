<?php

function buildImage($imageFile,$captionFile)
{

	$im = new Imagick();
	//$im2 = new Imagick();
	$im->setSize(1280,620);
	//$im2->setSize(1280,620);
	
	mkdir("uploads");
	$dest = "uploads/".basename($imageFile);
	
	$dest = strtolower($dest);
	$dest = str_replace("jpeg","jpg",$dest);
	$im->readImage($imageFile);
	echo "read file\n";
	$im->scaleImage(1280,620,true);
	echo "rescaled image \n";

	//$im->compositeImage($im2,imagick::COMPOSITE_COPYOPACITY,0,0);


    $exif = exif_read_data($imageFile);

    $orientation=0;
    if( isset($exif['Orientation']) )
        $orientation = $exif['Orientation'];
    elseif( isset($exif['IFD0']['Orientation']) )
        $orientation = $exif['IFD0']['Orientation'];

    switch($orientation) {
        case 3: // rotate 180 degrees
            $im->rotateimage("#FFF", 180);
        break;

        case 6: // rotate 90 degrees CW
            $im->rotateimage("#FFF", 90);
        break;

        case 8: // rotate 90 degrees CCW
            $im->rotateimage("#FFF", -90);
        break;
    }



	$geo = $im->getImageGeometry();
	echo "width = ".$geo['width'];
	echo "height = ".$geo['height'];

	  $color=new ImagickPixel();
	  $color->setColor("rgb(0,0,0)");
	$im->borderImage($color,(1280-$geo['width'])/2,100);
	$im->cropImage(1280,720,0,100);

	/* Set the font for the object */
	$draw = new ImagickDraw();
	$draw->setFontSize(36);
	$draw->setFont("/usr/share/fonts/truetype/freefont/FreeSansBold.ttf");
	$draw->setFillColor('#ffffff');
	$draw->setTextUnderColor('#00000088');

	/* Create new caption */
	echo "caption file = ".$captionFile."\n";
	$fp=fopen($captionFile,"r");
	$caption=fread($fp,filesize($captionFile));
	fclose($fp);
	
	$caption=preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $caption);
		
	$im->annotateImage($draw,10, 760, 0,$caption);
	echo "set caption to ".$caption."\n";
	/* Do something with the image */

	   $im->setImageCompression(Imagick::COMPRESSION_JPEG);
	   $im->setImageCompressionQuality(70);
	   $im->stripImage();
	   echo "stripped image\n";
	   $im->writeImage($dest); 
	echo "wrote image\n";
	$im->destroy();
	
}
?>