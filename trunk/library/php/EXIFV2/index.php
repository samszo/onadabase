<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
	  require_once(dirname(__FILE__)."/config.inc.php");
	  require_once(dirname(__FILE__)."/library/zip/zipextract.lib.php");
?>
<title><?php echo siteTitle;?></title>
<script type="text/javascript" src="./library/JQuery/jquery.1.3.js"></script>
<script type="text/javascript" src="./public/scripts/index.js"></script>
<link href="./public/styles/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

$action=@$_GET['action'];
if($action=="start")
{
?>
<a href="index.php">Go Back</a>
<?php
	if( !empty($_POST)	&&	($_FILES['fileToUpload']['error'] != UPLOAD_ERR_INI_SIZE) )
	{
		$tmp_file = $_FILES['fileToUpload']['tmp_name'];
	
		if( !is_uploaded_file($tmp_file) )
  		  {
      		  die("Cannot find the file");
   		  }
   		  $name_file = $_FILES['fileToUpload']['name'];
   		  if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $name_file) )
			{
				//We can upload for example file named: "hack.php\0.jpg"
 			 	die("You can't use the Null character when uploading file to the server :)");
			}
			else 
			{
    			$extensions = array('.jpg','.jpeg','.zip');
				$extension = strrchr($_FILES['fileToUpload'.$i]['name'], '.');
				if(in_array(strtolower($extension), $extensions)) //Si l'extension 	est pas dans le tableau
				{
					try{
						move_uploaded_file($tmp_file, dirname(__FILE__)."/pics/". $name_file);
					}catch(Exception $ex)
					{
						die("Cannot move File to the directory, please check the permission");
					}	
					//echo  "Exte:".$extension;
					if($extension==".zip")
					{
						$zip = new ZipExtract();
						if( $zip->OpenZipFile( dirname(__FILE__)."/pics/". $name_file ) )
						{
							    // Parametrage de la classe
							    $zip->SetOutputDestination(dirname(__FILE__)."/pics/");
							    $zip->SetIsChangeNameFile(false);
							    //Extraction des fichiers
							    $zip->Extract();
							    $zip->DeleteZipFile();
							    //On parcours maintenant le fichier Zip
							    //On n'autorise qu'un seul fichier image
							    $nom_fichier_zip=$zip->ListFiles();
							    
   									$extension = strrchr($nom_fichier_zip[0],'.');
   									if($extension==".jpg")
   									{
   										$name_file=$nom_fichier_zip[0];
   									}else 
   									{
   										@unlink(dirname(__FILE__)."/pics/".$nom_fichier_zip[0]);
   										die('Must contain just one jpg file (.jpg)');
   									}
   								}
						}
						 $filename = dirname(__FILE__)."/pics/". $name_file;//File Name 
						 require_once(dirname(__FILE__)."/library/phpExifRW/exifReader.inc");						
						 $er = phpExifReader::getInstance($filename);
						 $er->setCacheDir(cacheDir);//This variable indicate the Path of The cachDir to thumbnail
						 $er->setDebugging(false);
						 $er->Read();
						
		 				if($er->getThumbnailSize() > 0) {
		        			echo "<br><img src='".$er->showThumbnail()."' alt='Thumbnail Image'>";
		 				}
		 				?>
				 				<br/>Show Details:<a href="" id='link' class="link" onclick="switchDiv();return false;">+</a><br/>
								<div id="details" class="hidden">
								<?php
									echo "<pre>";
								    print_r($er->getImageInfo());
									echo "</pre>";
								?>
								</div>
		 				<?php
				}else 
				{
						die("You don't have permission to upload file that have $extension extension");
				}
			}
		
	}elseif($_FILES['fileToUpload']['error'] == UPLOAD_ERR_INI_SIZE)
	{
		die('The file size exceeds the size allocated');
	}
		
?>

<?php
}else 
{
?>

<!-- This is a FORM -->
<fieldset>
<legend>Please select a Zip file or an image file</legend>
	<form name="myForm" id="myForm" action="index.php?action=start" enctype="multipart/form-data" method="POST">
		<input type="file" id="fileToUpload" name="fileToUpload" class="inputFile">
		<input type="hidden" name="MAX_FILE_SIZE" value="200000">
		<input id="submitButton" type="submit" value="Extract"/>
		<div id="loading" class="loading">
			Veuillez patientez ....		
		</div>
	</form>
</fieldset>
<?php } ?>
<body>
</html>