<?php
session_start();
require_once ("../../../param/ParamPage.php");

$_SESSION['type_controle'] = array ($_POST['type_controle1'], $_POST['type_controle2']);
$_SESSION['type_contexte'] = array ($_POST['type_contexte1'], $_POST['type_contexte2'], $_POST['type_contexte3'], $_POST['type_contexte4']);
$_SESSION['version']= $_POST['version'];

if(TRACE)
	echo "index:login=$login, $mdp<br/>";
ChercheAbo ($login, $mdp, $objSite);
if(TRACE)
	echo "index:login=$login, $mdp<br/>";

header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: Saisi des diagnosics d'accessibilité");
echo '<' . '?xml version="1.0" encoding="iso-8859-15" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="onada.css" type="text/css"?' . '>' . "\n");

//chargement du menu overlay
//echo '<'.'?xul-overlay href="overlay/context.xul"?'.'>';
//echo '<'.'?xul-overlay href="overlay/choix_diagnostic.xul" ?'.'>';
echo '<'.'?xul-overlay href="overlay/PopupMenuSet.xul"?'.'>';
echo '<'.'?xul-overlay href="overlay/mnuSynchro.xul"?'.'>';
echo '<'.'?xul-overlay href="overlay/EtatDiag.xul"?'.'>';

?>


<window
    id="wSaisiDiag"
    flex="1"
    title="Saisi des diagnosics d'accessibilité"
    persist="screenX screenY width height"
    orient="horizontal"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"
>

<script type="application/x-javascript" src="js/interface.js" />
<script type="application/x-javascript" src="js/ajax.js"/>
<script type="application/x-javascript" src="js/tree.js"/>
<script type="application/x-javascript"  src="xbl/editableTree/functions.js" />
     <script>
		//initialise le paramètrage du site
		var lienAdminSpip = "<?php echo $objSite->infos["lienAdminSpip"]; ?>";
		var urlExeAjax = "<?php echo $objSite->infos["urlExeAjax"]; ?>";
		var urlExeAjax2 = "<?php echo $objSiteSync->infos["urlExeAjax"]; ?>";
		var xmlParam = GetXmlUrlToDoc("<?php echo $objSite->infos["jsXulParam"]; ?>");
		var synclienAdminSpip = "<?php echo $objSiteSync->infos["lienAdminSpip"]; ?>";
		var syncurlExeAjax = "<?php echo $objSiteSync->infos["urlExeAjax"]; ?>";
		var syncxmlParam = GetXmlUrlToDoc("<?php echo $objSiteSync->infos["jsXulParam"]; ?>");
		var urlSite = "<?php echo $objSite->infos["urlSite"]; ?>";
		var urlCarto = "<?php echo $objSite->infos["urlCarto"]; ?>";
		var path = "<?php echo PathRoot."/param/synchroExport.xml"; ?>";

		//var win = window.open("chrome://myextension/content/about.xul", "aboutMyExtension", "chrome,centerscreen"); 
		var urlPopUp = "<?php echo "popup.php?"; ?>";

     </script>



	<vbox  flex="1" style="overflow:auto">
	
		<hbox class="menubar">
			<image src="images/logo.png" />
			<menubar id='choix_diagnostic'>
				<menu label="Gestion des bases">
					<menupopup >
						<menuitem accesskey="d" label="Déconnexion" oncommand="window.location.replace('exit.php');"/>
					    <menu label="Synchronisation">
					      <menupopup id='mnuBarSynchro' >
						    <menu label="serveur->local">
						      <menupopup >
								<menuitem hidden="true" accesskey="s" label="Vérifier les paramètres" oncommand="SynchroniserMajParam();"/>
								<menuitem accesskey="v" label="Vérifier les contrôles" oncommand="CompareRubSrcDst('CompareServeurLocal',80);"/>
								<menuitem label="Vérifier l'élément en cours" oncommand="CompareRubSrcDst('CompareServeurLocal',document.getElementById('idRub').value);"/>
						      </menupopup>
						    </menu>
						    <menu label="local->serveur">
						      <menupopup >
								<menuitem hidden="true" accesskey="s" label="Vérifier les paramètres" oncommand="SynchroniserMajParam();"/>
								<menuitem label="Vérifier l'élément en cours" oncommand="CompareRubSrcDst('CompareLocalServeur',document.getElementById('idRub').value);"/>
						      </menupopup>
						    </menu>
					      </menupopup>
					    </menu>
					    <menu label="Bases disponibles">
					      <menupopup id='mnuSite' >
							<?php 
								foreach($SITES as $k => $s){
									if($site == $k)
										$check = "true";
									else
										$check = "false";
									echo "<menuitem id='site' checked='".$check."' type='radio' label=\"".$s["NOM"]."\" value='".$k."' oncommand=\"ChangeBase('".$k."');\"/>";
								}
							?>
					      </menupopup>
					    </menu>
					</menupopup>
				</menu>
				<menu label="Version" >
					<menupopup id="mnuVersion" onpopupshowing="javascript:;">
						<menuitem id="version" checked="<?php if($_SESSION['version']=="V1") echo "true"; ?>" type="radio" label="V1" value='V1' oncommand="SetChoixDiagnostic();"/>
						<menuitem id="version" checked="<?php if($_SESSION['version']=="V2") echo "true"; ?>" type="radio" label="V2" value='V2' oncommand="SetChoixDiagnostic();"/>
					</menupopup>
				</menu>
				<menu label="Type de critère" >
					<menupopup id="mnuTypeCrit" onpopupshowing="javascript:;">
						<menuitem id="type_controle1" type="checkbox" checked="<?php if($_SESSION['type_controle'][1]=="multiple_1_1") echo "true"; ?>" label="Réglementaire" value='multiple_1_1' oncommand="SetChoixDiagnostic();" />
						<menuitem id="type_controle2" type="checkbox" checked="<?php if($_SESSION['type_controle'][0]=="multiple_1_2") echo "true"; ?>" label="Souhaitable" value='multiple_1_2' oncommand="SetChoixDiagnostic();" />
					</menupopup>
				</menu>
				<menu label="Contexte réglementaire" onpopupshowing="javascript:;">
					<menupopup id="mnuContReg" >
						<menuitem id="type_contexte1" type="checkbox" checked="<?php if($_SESSION['type_contexte'][0]=="multiple_2_1") echo "true"; ?>" label="Travail" value='multiple_2_1' oncommand="SetChoixDiagnostic();"/>
						<menuitem id="type_contexte2" type="checkbox" checked="<?php if($_SESSION['type_contexte'][1]=="multiple_2_2") echo "true"; ?>" label="ERP/IOP" value='multiple_2_2' oncommand="SetChoixDiagnostic();"/>
						<menuitem id="type_contexte3" type="checkbox" checked="<?php if($_SESSION['type_contexte'][3]=="multiple_2_3") echo "true"; ?>" label="Logement" value='multiple_2_3' oncommand="SetChoixDiagnostic();"/>
						<menuitem id="type_contexte4" type="checkbox" checked="<?php if($_SESSION['type_contexte'][2]=="multiple_2_4") echo "true"; ?>" label="Voirie" value='multiple_2_4' oncommand="SetChoixDiagnostic();"/>
					</menupopup>
				</menu>
			</menubar>
		</hbox>
		<progressmeter id="progressMeter" value="0" mode="determined" style="margin: 4px;" hidden="true"/>	
		<hbox >
			<label hidden="true" id="idAuteur" value="<?php echo $_SESSION['IdAuteur'];?>" />
			<label hidden="false" id="login" value="<?php echo $login; ?>" />
			<label hidden="true" id="typeSrc" value="terre" />
			<label hidden="true" id="typeDst" value="Terre" />
			<label hidden="true" value="sur" />
			<label hidden="true" value="<?php echo $objSite->infos["NOM"]; ?>" />
			<label id="ChoixDiagnostic" value="" />
		</hbox>

		<hbox id="nav-toolbar" >
			<label id="tbbAccueil" value="Accueil" class="text-link" />
			<label id="tbbterre" value="Territoires" class="text-link" onclick="RefreshEcran(1942,'Territoires','terre','Terre');"/>
		</hbox>
		<hbox id="tbFilAriane" />
		
		<hbox class="global" id="global" flex="1">
		
			<vbox class="BoiteV" flex="0" width="300px">
				<hbox id="RefId" >
				 	<label id="titreRub" value="Selectionner un territoire" class="titre" />
					<label id="idRub" value="<?php echo $objSite->infos["RUB_TERRE"]; ?>" class="titreLiens" hidden="true"/>
				</hbox>
				<hbox id='treeRub' class="BoiteV" context="popterre" ></hbox>
			</vbox>
				<splitter collapse="before" resizeafter="farthest">
					<grippy/>
				</splitter>
	
			<vbox class="BoiteV" flex="1" >
				<hbox flex="1" hidden="true" >
				 <label value="Sélectionnez un établissement dans" id="TitreFormSaisi" class="titre" />
				 <label id="libRub" value="Le département du Nord" class="titre" />
				</hbox>
				<hbox id="EtatDiag" hidden="true" flex="1" />
				<hbox class="FormBox" id="FormSaisi" flex="1" />		
			</vbox>

			<vbox class="BoiteV" id="syncV1" flex="1" hidden="true">
				<hbox id="syncRefId" >
				 <label value="Selectionner un territoire" class="titre" />
				</hbox>
				<label id="syncidRub" value="-1"/>
				<hbox id='synctreeRub' class="BoiteV" context="popterre" ></hbox>
			</vbox>
				<splitter id="syncSplit" hidden="true" collapse="before" resizeafter="farthest">
					<grippy/>
				</splitter>
	
			<vbox class="BoiteV" id="syncV2" flex="1" hidden="true">
				<hbox flex="1" hidden="true" >
				 <label value="Sélectionnez un établissement dans" id="syncTitreFormSaisi" class="titre" />
				 <label id="synclibRub" value="Le département du Nord" class="titre" />
				</hbox>
				<hbox class="FormBox" id="syncFormSaisi" flex="1">
				</hbox>
				
			</vbox>
		
		</hbox>	

		<hbox class="footer" >
			<label control="middle" value="Version 1.0" dir="reverse"/>
		</hbox>	
		
	</vbox>
	<splitter collapse="before" resizeafter="farthest">
		<grippy/>
	</splitter>
	<!-- 
	<vbox flex="1" style="overflow:auto">
		<iframe flex="1"  src='<?php echo $objSite->infos["urlCarto"];?>'  id='BrowerGlobal' />
	</vbox>
 	-->
 
<script type="application/x-javascript" >
	//met à jour le choix du diagnostic
	SetChoixDiagnostic();
   ChargeTreeFromAjax('idRub','treeRub','terre');
</script>

</window>

