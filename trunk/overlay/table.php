<?php
require_once ("../param/ParamPage.php");
	//adresse de la datasource
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']";
	$ds = $objSite->XmlParam->GetElements($Xpath);
	//echo $ds[0]["datasource"];
	//param de la description
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdfDesc";
	$Desc = $objSite->XmlParam->GetElements($Xpath);
	//print_r($Desc);
	//param des lignes rdf
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/rdf";
	$Rdfs = $objSite->XmlParam->GetElements($Xpath);	
	//print_r($Desc[0]["urn"]);
	//print_r($Rdfs);

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<overlay id="tabletrad" >
	<box id="<?php echo $objSite->scope["box"]; ?>" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" >
		<listbox >
			
			<listhead >
				<listheader label="id_ieml"></listheader>
				<listheader label="id_10eF"></listheader>
			</listhead>
			<listcols>
				<listcol flex="1"></listcol>
				<listcol flex="1"></listcol>
			</listcols>
		
		<?php
			$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='ieml-10eF']";
			$Q = $objSite->XmlParam->GetElements($Xpath);
			$sql = $Q[0]->select.$Q[0]->from.$Q[0]->where;
			//echo $Xpath."<br/>"; 

			$db = new mysql ($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$req = $db->query($sql);
			$db->close();
			$nb = mysql_num_rows($req);

			while($r = mysql_fetch_assoc($req))
			{
				echo('<listitem >');
				echo('<listcell idTradIeml="'.$r["ieml_id"].'" label="'.$r["ieml_desc"].'"/>');
				echo('<listcell idTrad10ef="'.$r["10ef_id"].'" label="'.$r["10ef_desc"].'"/>');
				echo ('</listitem>');
			}		    			    
		?>
		</listbox>
	</box>
</overlay>