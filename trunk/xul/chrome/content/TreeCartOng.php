<?php
	require_once ("../../../param/ParamPage.php");

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<?php echo '<?xul-overlay href="overlay/popupset.php?f=1"?>';?>
<?php echo '<?xul-overlay href="overlay/tree.php?box=singlebox1&ParamNom='.$objSite->scope['ParamNom'].'"?>';?>
<?php echo '<?xul-overlay href="overlay/iframe.php?ParamNom=TreeCartOng&amp&UrlNom=carto"?>';?>
<?php echo '<?xul-overlay href="overlay/tabbox.php?box=tabbox1"?>';?>


<?xml-stylesheet rel="stylesheet" href="xbl/editableTree/demo.css" type="text/css" title="css"?>

<window id="solacc-onto" title="Ontologie" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/interface.js"/>
    <script language="JavaScript" type="application/x-javascript" src="xbl/editableTree/functions.js" />
	<script language="JavaScript" type="application/x-javascript" src="js/ajax.js"/>

	<popupset id="popupset">
	</popupset>


	<hbox >
		<vbox id="singlebox1" style="height:400px;width:300px;" >
		</vbox>
		<vbox id="singlebox" style="height:400px;width:310px;" >
		</vbox>
		<vbox id="tabbox1" flex="1" style="height:400px;width:300px;" >
		</vbox>		
	</hbox >

</window>