<?php
require_once(dirname(__FILE__)."/config.inc.php");
header("Content-Type: image/jpeg");

$file = $_GET["file"];
$extension = strrchr($file, '.');//Should Verify is a valid DATA on No
if(strtolower($extension)==".jpg")
{
	$chacheFolder = cacheDir;

 		if(file_exists("$chacheFolder/$file")) {
        	$fp = fopen("$chacheFolder/$file","rb");
        	$tmpStr = fread($fp,filesize("$chacheFolder/$file"));
        	echo $tmpStr;
   		exit;
 		}

 	/* assumed to get the filename with full path though GET method. */
 	require_once(dirname(__FILE__)."/library/phpExifRW/exifReader.inc");
 	$er =phpExifReader::getInstance($file);
 	$er->processFile();
 	echo $er->getThumbnail();
}else 
	echo "";
?>