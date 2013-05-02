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
                                                                

include 'add_caption.php';
include 'config.php';

function is_base64_encoded($data)
    {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
            return TRUE;
        } else {
            return FALSE;
        }
    };

$connection = imap_open($email_server,$email_address,$email_password);
$count = imap_num_msg($connection);
$dir="email_downloads";
mkdir($dir);
//chdir($dir);

for($i = 1; $i <= $count; $i++) {
	$imgCount = 0;
	$imageList = array();
	$captionFile = "";
	$imageFile = "";
	$header = imap_headerinfo($connection, $i);
	
	$from = $header->fromaddress;
	
	if(strstr($from,"<"))
	{
	    $parts = explode("<",$from);
	    $from=$parts[1];
	    
	}
	
	//$name=str_replace("<","",$name);
	$from=str_replace(">","",$from);
	echo "from: ".$from."\n";
	
	$name = $dir."/".$from."-".$header->udate;
	
	$name=str_replace(" ","_",$name);
	
	
	$raw_body = imap_body($connection, $i);
	$structure = imap_fetchstructure($connection,$i,0);

	//echo $header->fromaddress;
	$from=$names[$from];
	$attachments = array();
	echo "number of items ".count($structure->parts);
	if(isset($structure->parts) && count($structure->parts)) {
	
		for($j = 0; $j < count($structure->parts); $j++) {
	
			$attachments[$j] = array(
				'is_attachment' => false,
				'filename' => '',
				'name' => '',
				'attachment' => ''
			);
			
			if($structure->parts[$j]->ifdparameters) {
				foreach($structure->parts[$j]->dparameters as $object) {
					if(strtolower($object->attribute) == 'filename') {
						$attachments[$j]['is_attachment'] = true;
						$attachments[$j]['filename'] = $object->value;
					}
				}
			}
			       
			if($structure->parts[$j]->ifparameters) {
				foreach($structure->parts[$j]->parameters as $object) {
					if(strtolower($object->attribute) == 'name') {
						$attachments[$j]['is_attachment'] = true;
						$attachments[$j]['name'] = $object->value;
					}
				}
			}
			
			if($attachments[$j]['is_attachment']) {
				$attachments[$j]['attachment'] = imap_fetchbody($connection, $i, $j+1);
				if($structure->parts[$j]->encoding == 3) { // 3 = BASE64
					$attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
					
					$pieces = explode(".",$attachments[$j]['filename']);
					
					$fp = fopen($name.".".$j.".".$pieces[1],"w");
					fwrite($fp,$attachments[$j]['attachment']);
					fclose($fp);
					$imageFile=$name.".".$j.".".$pieces[1];
					$imageList[$imgCount]=$imageFile;
					$imgCount++;
					echo "Saved image ".$imageFile."\n";
				}
				elseif($structure->parts[$j]->encoding == 4) { // 4 = QUOTED-PRINTABLE
					$attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
					echo $attachments[$j]['attachment'];
				}
			}
			else
			{
				$caption=imap_fetchbody($connection,$i,$j+1.1);
				if($caption=="")
				{
				    $caption=imap_fetchbody($connection, $i, $j+1);
				}
				
				if(is_base64_encoded($caption)||(strlen($caption)>10&&strchr($caption," ")==false))
				{
				    echo "base 64 encoded";
		                    $caption=base64_decode($caption);
            			}
            			else
            			{
            			    echo "not base 64 encoded";
            			}
            			
            			   $header = imap_fetchheader($connection, $i);
            			   
            			   
            			   $has_boundary = preg_match("/boundary=[\"]{0,1}[a-zA-Z0-9-_]*[\"]{0,1}/",$header,$parts);
            			   
            			   if($has_boundary)
            			   {
            			    $boundary = str_replace("boundary=","",$parts[0]);
            			    $parts = preg_split("/".$boundary."/",$caption);
            			    $real_caption = $parts[0];
            			    
            			     
            			   } else {
            			     $real_caption=$caption;
            			   }   
            			
            			echo "Full caption ".$real_caption;
            			
		                $real_caption=preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $real_caption);
            			$real_caption=preg_replace("/Sent from my [A-Za-z ]*/","",$real_caption);
		                $real_caption="From: ".$from."\n".$real_caption;
            	            	
                
				$fp = fopen($name.".msg","w");
				fwrite($fp,$real_caption);
				fclose($fp);
				$captionFile=$name.".msg";
			}
		}
	}
	if(count($imageList)!=0&&$captionFile!="")
	{
	    foreach($imageList as $imageFile)
	    {
		buildImage($imageFile,$captionFile);
	    }	
	}
	imap_delete($connection,$i);
}
imap_expunge($connection);
imap_close($connection);
system("./upload_photos.sh");
?>
