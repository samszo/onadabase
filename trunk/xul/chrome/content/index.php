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
		var path = "<?php echo PathRoot."/param/synchroExport.xml"; ?>";

		//var win = window.open("chrome://myextension/content/about.xul", "aboutMyExtension", "chrome,centerscreen"); 
		var urlPopUp = "<?php echo "popup.php?"; ?>";

     </script>

	<popupset >
		<popup id="popterre" onpopupshowing="javascript:;">
		    <menu id="menu_terre_voir" label="Voir">
		      <menupopup id="popup_terre_voir">
				<menuitem label="Le(s) établissement(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Etablissements','etab','Etab');"/>
				<menuitem label="Le(s) tronçon(s) de voirie" oncommand="RefreshEcran(document.getElementById('idRub').value,'Voiries','voirie','Voirie');"/>
		      </menupopup>
		    </menu>
		    <menu id="menu_terre_ajouter" label="Ajouter">
		      <menupopup id="popup_terre_ajouter">
				<menuitem label="Un établissement" oncommand="AddNewGrille('Etab');"/>
				<menuitem label="Un tronçon de voirie" oncommand="AddNewGrille('Voirie');"/>
				<menuitem label="Un territoire" oncommand="AddNewRubrique(document.getElementById('idRub').value);"/>
		      </menupopup>
		    </menu>
		</popup>
		<popup id="popTerre" onpopupshowing="javascript:;">
		    <menu id="menu_Terre_voir" label="Voir">
		      <menupopup id="popup_Terre_voir">
				<menuitem label="Le(s) établissement(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Etablissements','etab','Etab');"/>
				<menuitem label="Le(s) tronçon(s) de voirie" oncommand="RefreshEcran(document.getElementById('idRub').value,'Voiries','voirie','Voirie');"/>
		      </menupopup>
		    </menu>
		    <menu id="menu_Terre_ajouter" label="Ajouter">
		      <menupopup id="popup_Terre_ajouter">
				<menuitem label="Un établissement" oncommand="AddNewGrille('Etab');"/>
				<menuitem label="Un tronçon de voirie" oncommand="AddNewGrille('Voirie');"/>
				<menuitem label="Un territoire" oncommand="AddNewRubrique(document.getElementById('idRub').value);"/>
		      </menupopup>
		    </menu>
		</popup>
		<popup id="popetab" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) problème(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="Générer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir le(s) bâtiment(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Bâtiments','bat','Bat');"/>
			<menuitem label="Ajouter un bâtiment" oncommand="AddNewGrille('Bat');"/>
			<menuitem label="Voir la(les) parcelle(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Parcelles','parcelle','Parcelle');"/>
			<menuitem label="Ajouter une parcelle" oncommand="AddNewGrille('Parcelle');"/>
		</popup>
		<popup id="popEtab" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un bâtiment" oncommand="AddNewGrille('Bat');"/>
			<menuitem label="Ajouter une parcelle" oncommand="AddNewGrille('Parcelle');"/>			
		</popup>
		<popup id="popvoirie" onpopupshowing="javascript:;">
			<menuitem label="Voir les éléments de voirie" oncommand="RefreshEcran(document.getElementById('idRub').value,'Eléments de voirie','elementvoirie','ElementVoirie');"/>
		</popup>
		<popup id="popespace" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces','espace','Espace');"/>
			<menuitem label="Ajouter un espace" oncommand="AddNewGrille('Espace');"/>
		</popup>
		<popup id="popbat" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) problème(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="Générer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir le(s) niveau(x)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Niveaux','niveau','Niveau');"/>
			<menuitem label="Ajouter un niveau" oncommand="AddNewGrille('Niveau');"/>
			<menuitem label="Voir la(les) cabine(s) d'ascenseur" oncommand="RefreshEcran(document.getElementById('idRub').value,'Cabines Ascenseurs','objetintbat','ObjetIntBat');"/>
			<menuitem label="Ajouter la(les) cabine(s) d'ascenseur" oncommand="AddNewGrille('ObjetIntBat');"/>
			<menuitem label="Copier le bâtiment" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popBat" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un niveau" oncommand="AddNewGrille('Niveau');"/>
			<menuitem label="Ajouter la(les) cabine(s) d'ascenseur" oncommand="AddNewGrille('ObjetIntBat');"/>
			
		</popup>
		<popup id="popniveau" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) problème(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="Générer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir le(s) espace(s) intérieur(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces intérieurs','espaceint','EspaceInt');"/>
			<menuitem label="Ajouter un espace intérieur" oncommand="AddNewGrille('EspaceInt');"/>
			<menuitem label="Voir les objets intérieurs" oncommand="RefreshEcran(document.getElementById('idRub').value,'Tous les objets','objetint','ObjetInt');"/>
			<menuitem label="Ajouter un objet intérieur" oncommand="AddNewGrille('ObjetInt');"/>
			<menuitem label="Copier le niveau" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popNiveau" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un espace intérieur" oncommand="AddNewGrille('EspaceInt');"/>
			<menuitem label="Ajouter un objet intérieur" oncommand="AddNewGrille('ObjetInt');"/>
		</popup>
		
		<popup id="popobjetintbat" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','objetgen','ObjetGen');"/>
			<menuitem label="Copier la cabine d'ascenseur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
			
		<popup id="popobjetint" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','objetgen','ObjetGen');"/>
			<menuitem label="Copier l'objet intérieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popespaceint" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','espacegen','EspaceGen');"/>
			<menuitem label="Copier l'espace intérieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popparcelle" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s) extérieur(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces extérieurs','espaceext','EspaceExt');"/>
			<menuitem label="Ajouter un espace extérieur" oncommand="AddNewGrille('EspaceExt');"/>
			<menuitem label="Voir les objets extérieurs" oncommand="RefreshEcran(document.getElementById('idRub').value,'Objets extérieurs','objetext','ObjetExt');"/>
			<menuitem label="Ajouter un objet extérieur" oncommand="AddNewGrille('ObjetExt');"/>
			<menuitem label="Copier la parcelle" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popParcelle" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un espace extérieur" oncommand="AddNewGrille('EspaceExt');"/>
			<menuitem label="Ajouter un objet extérieur" oncommand="AddNewGrille('ObjetExt');"/>
		</popup>
		<popup id="popespaceext" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) problème(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="Générer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','espaceextparamgen','EspaceExtParamGen');"/>
			<menuitem label="Copier l'espace extérieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popobjetext" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) problème(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="Générer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','objetgenext','ObjetGenExt');"/>
			<menuitem label="Copier l'objet extérieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popSyncSrc" onpopupshowing="javascript:;">
			<menuitem label="Ajouter les objets local au serveur" oncommand="SyncAjout(document.getElementById('idRub').value,'Paramètres généraux','espaceextparamgen','EspaceExtParamGen');"/>
		</popup>
		<popup id="popEspaceExtParamGen" onpopupshowing="javascript:;">
			<menuitem label="Voir les problèmes signalés" oncommand="RefreshEcran(document.getElementById('idRub').value,'Signalements problèmes','aucun','SignalementProbleme');"/>
		</popup>
		<popup id="popEspaceGen" onpopupshowing="javascript:;">
			<menuitem label="Voir les problèmes signalés" oncommand="RefreshEcran(document.getElementById('idRub').value,'Signalements problèmes','aucun','SignalementProbleme');"/>
		</popup>
		<popup id="popObjetGen" onpopupshowing="javascript:;">
			<menuitem label="Voir les problèmes signalés" oncommand="RefreshEcran(document.getElementById('idRub').value,'Signalements problèmes','aucun','SignalementProbleme');"/>
		</popup>
	</popupset>


	<vbox  flex="1" style="overflow:auto">
	
		<hbox class="menubar">
			<progressmeter id="progressMeter" value="0" mode="determined" style="margin: 4px;" hidden="true"/>
			<image src="images/logo.png" />
			<menubar id='choix_diagnostic'>
				<menu label="Gestion des bases">
					<menupopup >
						<menuitem accesskey="d" label="Déconnexion" oncommand="window.location.replace('exit.php');"/>
						<?php 
							if($_SERVER['REMOTE_ADDR']=="127.0.0.1")
								echo '<menuitem label="Synchroniser" oncommand="SynchroniserExportImport();"/>';
						?>
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
		<hbox >
			<label hidden="true" id="idAuteur" value="<?php echo $_SESSION['IdAuteur'];?>" />
			<label hidden="false" id="login" value="<?php echo $login; ?>" />
			<label hidden="false" value="sur" />
			<label hidden="false" value="<?php echo $objSite->infos["NOM"]; ?>" />
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
					<label id="idRub" value="-1" class="titreLiens" hidden="true"/>
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
				<hbox class="FormBox" id="FormSaisi" flex="1">
				
				
				</hbox>
				
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

<script type="application/x-javascript" >
   ChargeTreeFromAjax('idRub','treeRub','terre');
	//met à jour le choix du diagnostic
	SetChoixDiagnostic();
</script>

</window>

