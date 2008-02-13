<?php
	require_once ("../param/ParamPage.php");

	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/menu";
	$Menus = $objSite->XmlParam->GetElements($Xpath);
	print_r($Desc);

    header('Content-type: application/vnd.mozilla.xul+xml');
	print '<?xml version="1.0" encoding="ISO-8859-1" ?>';
?>
<overlay id="menubar"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

	<box id="menubar" >
		<menubar id="menu-principal">
		<?php
			foreach($Menus as $Menu)
			{
				echo('<menu id="menu-onto" label="'.$Menu["nom"].'" style="font-size: 16px;">');
				echo('<menupopup id="onto-popup">');
				foreach($Menu->urlDesc as $url)
				{
					echo("<menuitem label=\"".$url["nom"]."\" oncommand=\"ChargeBrower('BrowerGlobal','".$objSite->XmlParam->XML_entities($url["src"])."')\"/>");
					echo('<menuseparator/>');
				}
				echo('</menupopup>');
				echo('</menu>');
				echo('<spacer width="20"/>');
			}
		?>
		</menubar>
	</box>
</overlay>