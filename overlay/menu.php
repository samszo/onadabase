<?php
	require_once ("../param/ParamPage.php");

	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/menu";
	$Menus = $objSite->XmlParam->GetElements($Xpath);
	//print_r($Desc);

	$sem = New Sem($objSite, PathRoot."/param/EvalActiSem.xml", "loto web");

    //header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<overlay id="menu"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

	<toolbox flex="1"  id='<?php echo $objSite->scope['box']; ?>' >
	
		<menubar id="sample-menubar">
		<?php echo $sem->GetChoixNavig(); ?>		
		</menubar>
		
	</toolbox>

</overlay>