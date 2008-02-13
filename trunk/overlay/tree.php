<?php
require_once ("../param/ParamPage.php");
	//adresse de la datasource
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']";
	$ds = $objSite->XmlParam->GetElements($Xpath);
	//echo $ds[0]["datasource"];
	//print_r($Desc);
	//param des colonnes
	$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/col";
	$Cols = $objSite->XmlParam->GetElements($Xpath);	
	//print_r($Rdfs);

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>

<overlay id="oTree"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

<script type="text/javascript" src="../xbl/editableTree/functions.js" />
    <popupset>
        <tooltip id="tipBadValue" onclick="this.hidePopup( );">
            <vbox>
                <label value="Valeur incorrecte !!"/>
            </vbox>
        </tooltip>
    </popupset>
	<box id="<?php echo $objSite->scope["box"]; ?>"  class="editableTree" >
		<tree id="tree<?php echo $type;?>"
			width="300" height="400"
			context="clipmenu"			
			enableColumnDrag="true"
			fctStart="startEditable"
			fctSave="saveEditable"
			fctInsert="startInsert"
			fctDelete="startDelete"
			fctSelect="startSelect"
			typesource="<?php echo $type;?>"	
			idTree="tree<?php echo $type;?>"
		 >
				<treecols>
				<?php
					//le conteneur doit avoir comme id id pour editableTree
					echo('<treecol id="id" primary="true" flex="1" cycler="true" />');
					echo('<splitter class="tree-splitter"/>');
					$i=0;
					foreach($Cols as $Col)
					{
						//la première colonne est le bouton pour déplier
						if($i!=0){
							echo('<treecol id="treecol_'.$Col["tag"].'" label="'.$Col["tag"].'" persist="width ordinal hidden" />');
							echo('<splitter class="tree-splitter"/>');
						}
						$i++;
					}
				?>
				</treecols>
				<?php
					echo $objSite->GetTreeChildren($type, $Cols);
				?>
			</tree>
	</box>
</overlay>
