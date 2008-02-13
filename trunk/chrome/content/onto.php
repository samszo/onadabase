<?php
	require_once ("param/ParamPage.php");

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<?php echo '<?xul-overlay href="overlay/popupset.php?f=1"?>';?>
<?php echo '<?xul-overlay href="overlay/tree.php?box=singlebox1&ParamNom='.$objSite->scope['ParamNom'].'"?>';?>
<?xml-stylesheet rel="stylesheet" href="xbl/editableTree/demo.css" type="text/css" title="css"?>

<window id="solacc-onto" title="Ontologie" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/Interface.js"/>
    <script type="text/javascript" src="xbl/editableTree/functions.js" />
	<script language="JavaScript" type="application/x-javascript" src="js/ajax.js"/>

	<popupset id="popupset">
	</popupset>


	<vbox >
		<label value="action : "/><label id="onto-message"  />
	</vbox>
	<vbox id="singlebox1" style="height:560px;width:1100px;" >
	</vbox>

</window>