<?php
require_once ("../../../param/ParamPage.php");
session_start();

extract($_SESSION,EXTR_OVERWRITE);
extract($_POST,EXTR_OVERWRITE);

if(!isset($_SESSION['loginSess'])) {
	$login=$_POST['login_uti'];
	$mdp=$_POST['mdp_uti'];
} else {
	$login=$_SESSION['loginSess'];
	$mdp=$_SESSION['mdpSess'];
	$idAuteur=$_SESSION['IdAuteur'];
}

/*if(!isset($_SESSION['type_controle']))
{*/
	//$login=$_POST['login_uti'];
	//$mdp=$_POST['mdp_uti'];
	$_SESSION['type_controle'] = array ($_POST['type_controle1'], $_POST['type_controle2']);
	$_SESSION['type_contexte'] = array ($_POST['type_contexte1'], $_POST['type_contexte2'], $_POST['type_contexte3'], $_POST['type_contexte4']);
	$_SESSION['version']= $_POST['version'];
//}

function ChercheAbo ($login, $mdp, $objSite)
	{
		// connexion serveur
		$link = mysql_connect($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"]) or die("Impossible de se connecter : " . mysql_error());	
		// S�lection de la base de donn�es
		//mysql_select_db("solacc", $link);	
		mysql_select_db($objSite->infos["SQL_DB"], $link);	
		
		$sql = "SELECT id_auteur, nom, login, email  FROM spip_auteurs WHERE login = '".$login."' AND pass = md5( CONCAT(alea_actuel,'$mdp'))";
		//echo $sql;
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			//echo $nbResultat."<br/>";
			
		mysql_close($link);
	  	$nbre_lignes = mysql_num_rows($req);
	  	//echo $nbre_lignes;
		if ($nbre_lignes == 1)
		{
			while($resultat = mysql_fetch_assoc($req))
				{	
					$_SESSION['IdAuteur'] = $resultat['id_auteur'];
					$_SESSION['NomSess'] = $resultat['nom'];
					$_SESSION['EmailSess'] = $resultat['email'];
					$_SESSION['loginSess'] = $resultat['login'];	
					$_SESSION['IpSess'] = $_SERVER['REMOTE_ADDR'];
					$_SESSION['mdpSess'] = $mdp;
				}
			
		}
		else
		{
			include("log.php");
			exit;
		}
	}

/*function Test ($syncSite)
	{
		// connexion serveur
		$link = mysql_connect($syncSite->infos["SQL_HOST"], $syncSite->infos["SQL_LOGIN"], $syncSite->infos["SQL_PWD"]) or die("Impossible de se connecter : " . mysql_error());	
		// S�lection de la base de donn�es
		//mysql_select_db("solacc", $link);	
		mysql_select_db($syncSite->infos["SQL_DB"], $link) or die("Impossible de se connecter a la base : " . mysql_error());	
	}

Test (	$objSiteSync);*/
ChercheAbo ($login, $mdp, $objSite);
//$idAuteur=$_SESSION['IdAuteur'];

header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: Saisi des diagnosics d'accessibilit�");
echo '<' . '?xml version="1.0" encoding="iso-8859-15" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="onada.css" type="text/css"?' . '>' . "\n");

//chargement du menu overlay
echo '<'.'?xul-overlay href="overlay/context.xul"?'.'>';

?>


<window
    id="wSaisiDiag"
    flex="1"
    title="Saisi des diagnosics d'accessibilit�"
    persist="screenX screenY width height"
    orient="horizontal"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"
>

<script type="application/x-javascript" src="js/interface.js" />
<script type="application/x-javascript" src="js/ajax.js"/>
<script type="application/x-javascript" src="js/tree.js"/>
<script type="application/x-javascript"  src="xbl/editableTree/functions.js" />
     <script>
		//initialise le param�trage du site
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
			<menuitem label="Voir le(s) �tablissement(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Etablissements','etab','Etab');"/>
			<menuitem label="Ajouter un �tablissement" oncommand="AddNewGrille('Etab');"/>
			<menuitem label="Voir le(s) tron�on(s) de voirie" oncommand="RefreshEcran(document.getElementById('idRub').value,'Voiries','voirie','Voirie');"/>
			<menuitem label="Ajouter un tron�on de voirie" oncommand="AddNewGrille('Voirie');"/>
			<menuitem label="Ajouter un territoire" oncommand="AddNewRubrique(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popTerre" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un �tablissement" oncommand="AddNewGrille('Etab');"/>
			<menuitem label="Ajouter un tron�on de voirie" oncommand="AddNewGrille('Voirie');"/>
			<menuitem label="Ajouter un territoire" oncommand="AddNewRubrique(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popetab" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) probl�me(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="G�n�rer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir le(s) b�timent(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'B�timents','bat','Bat');"/>
			<menuitem label="Ajouter un b�timent" oncommand="AddNewGrille('Bat');"/>
			<menuitem label="Voir la(les) parcelle(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Parcelles','parcelle','Parcelle');"/>
			<menuitem label="Ajouter une parcelle" oncommand="AddNewGrille('Parcelle');"/>
		</popup>
		<popup id="popEtab" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un b�timent" oncommand="AddNewGrille('Bat');"/>
			<menuitem label="Ajouter une parcelle" oncommand="AddNewGrille('Parcelle');"/>
		</popup>
		<popup id="popvoirie" onpopupshowing="javascript:;">
			<menuitem label="Voir les �l�ments de voirie" oncommand="RefreshEcran(document.getElementById('idRub').value,'El�ments de voirie','elementvoirie','ElementVoirie');"/>
		</popup>
		<popup id="popespace" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces','espace','Espace');"/>
			<menuitem label="Ajouter un espace" oncommand="AddNewGrille('Espace');"/>
		</popup>
		<popup id="popbat" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) probl�me(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="G�n�rer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir le(s) niveau(x)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Niveaux','niveau','Niveau');"/>
			<menuitem label="Ajouter un niveau" oncommand="AddNewGrille('Niveau');"/>
			<menuitem label="Voir la(les) cabine(s) d'ascenseur" oncommand="RefreshEcran(document.getElementById('idRub').value,'Cabines Ascenseurs','objetintbat','ObjetIntBat');"/>
			<menuitem label="Ajouter la(les) cabine(s) d'ascenseur" oncommand="AddNewGrille('ObjetIntBat');"/>
			<menuitem label="Copier le b�timent" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popBat" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un niveau" oncommand="AddNewGrille('Niveau');"/>
			<menuitem label="Ajouter la(les) cabine(s) d'ascenseur" oncommand="AddNewGrille('ObjetIntBat');"/>
			
		</popup>
		<popup id="popniveau" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) probl�me(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="G�n�rer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir le(s) espace(s) int�rieur(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces int�rieurs','espaceint','EspaceInt');"/>
			<menuitem label="Ajouter un espace int�rieur" oncommand="AddNewGrille('EspaceInt');"/>
			<menuitem label="Voir les objets int�rieurs" oncommand="RefreshEcran(document.getElementById('idRub').value,'Tous les objets','objetint','ObjetInt');"/>
			<menuitem label="Ajouter un objet int�rieur" oncommand="AddNewGrille('ObjetInt');"/>
			<menuitem label="Copier le niveau" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popNiveau" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un espace int�rieur" oncommand="AddNewGrille('EspaceInt');"/>
			<menuitem label="Ajouter un objet int�rieur" oncommand="AddNewGrille('ObjetInt');"/>
		</popup>
		
		<popup id="popobjetintbat" onpopupshowing="javascript:;">
			<menuitem label="Voir les param�tres de contr�le" oncommand="RefreshEcran(document.getElementById('idRub').value,'Param�tres de cont�le','objetgen','ObjetGen');"/>
			<menuitem label="Copier la cabine d'ascenseur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
			
		<popup id="popobjetint" onpopupshowing="javascript:;">
			<menuitem label="Voir les param�tres de contr�le" oncommand="RefreshEcran(document.getElementById('idRub').value,'Param�tres de cont�le','objetgen','ObjetGen');"/>
			<menuitem label="Copier l'objet int�rieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popespaceint" onpopupshowing="javascript:;">
			<menuitem label="Voir les param�tres de contr�le" oncommand="RefreshEcran(document.getElementById('idRub').value,'Param�tres de cont�le','espacegen','EspaceGen');"/>
			<menuitem label="Copier l'espace int�rieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popparcelle" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s) ext�rieur(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces ext�rieurs','espaceext','EspaceExt');"/>
			<menuitem label="Ajouter un espace ext�rieur" oncommand="AddNewGrille('EspaceExt');"/>
			<menuitem label="Voir les objets ext�rieurs" oncommand="RefreshEcran(document.getElementById('idRub').value,'Objets ext�rieurs','objetext','ObjetExt');"/>
			<menuitem label="Ajouter un objet ext�rieur" oncommand="AddNewGrille('ObjetExt');"/>
			<menuitem label="Copier la parcelle" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popParcelle" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un espace ext�rieur" oncommand="AddNewGrille('EspaceExt');"/>
			<menuitem label="Ajouter un objet ext�rieur" oncommand="AddNewGrille('ObjetExt');"/>
		</popup>
		<popup id="popespaceext" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) probl�me(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="G�n�rer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir les param�tres de contr�le" oncommand="RefreshEcran(document.getElementById('idRub').value,'Param�tres de cont�le','espaceextparamgen','EspaceExtParamGen');"/>
			<menuitem label="Copier l'espace ext�rieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popobjetext" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) probl�me(s)" oncommand="ChargeTreeProb('idRub','FormSaisi');"/>
			<menuitem label="Voir la(les) observation(s)" oncommand="ChargeTreeObs('idRub','FormSaisi');"/>
			<menuitem label="G�n�rer csv" oncommand="ChargeTreeCsv('idRub','FormSaisi');"/>
			<menuitem label="Voir les param�tres de contr�le" oncommand="RefreshEcran(document.getElementById('idRub').value,'Param�tres de cont�le','objetgenext','ObjetGenExt');"/>
			<menuitem label="Copier l'objet ext�rieur" oncommand="CopyRub(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popSyncSrc" onpopupshowing="javascript:;">
			<menuitem label="Ajouter les objets local au serveur" oncommand="SyncAjout(document.getElementById('idRub').value,'Param�tres g�n�raux','espaceextparamgen','EspaceExtParamGen');"/>
		</popup>
		<popup id="popEspaceExtParamGen" onpopupshowing="javascript:;">
			<menuitem label="Voir les probl�mes signal�s" oncommand="RefreshEcran(document.getElementById('idRub').value,'Signalements probl�mes','aucun','SignalementProbleme');"/>
		</popup>
		<popup id="popEspaceGen" onpopupshowing="javascript:;">
			<menuitem label="Voir les probl�mes signal�s" oncommand="RefreshEcran(document.getElementById('idRub').value,'Signalements probl�mes','aucun','SignalementProbleme');"/>
		</popup>
		<popup id="popObjetGen" onpopupshowing="javascript:;">
			<menuitem label="Voir les probl�mes signal�s" oncommand="RefreshEcran(document.getElementById('idRub').value,'Signalements probl�mes','aucun','SignalementProbleme');"/>
		</popup>
	</popupset>


	<vbox  flex="1" style="overflow:auto">
	
		<hbox class="menubar">
			<image src="images/logo.png" />
			<label id="idAuteur" value="<?php echo $_SESSION['IdAuteur'];?>" class="menubartext"/>
			<script type="text/javascript">document.getElementById('idAuteur').style.visibility="hidden";</script>
			<label value="Auteur du diagnostic : " class="menubartext"/>
			<label id="login" value="<?php echo $login; ?>" class="menubartext" onclick="window.location.replace('exit.php') ; "/>
			<button id="btnSync" label="Synchroniser" onclick="SynchroniserExportImport()"/>
			<progressmeter id="progressMeter" value="0" mode="determined" style="margin: 4px;"/>
			<label id="infiDiag" value="
			<?php echo 'Version : '.$_SESSION['version'];
				 echo ' ++ Type de crit�re :';
				 if ($_SESSION['type_controle']== null) echo 'Aucun'; 
				 else foreach($_SESSION['type_controle'] as $controle) {
				 	if ($controle=='multiple_1_1') echo ' R�glementaire -'; 
				 	if ($controle=='multiple_1_2') echo ' Souhaitable -'; 
				 }
				 echo ' ++ Contexte r�glementaire :'; 
				 if ($_SESSION['type_contexte']== null) echo 'Aucun'; 
				 else foreach($_SESSION['type_contexte'] as $contexte) {
				 	if($contexte == 'multiple_2_1' ) echo ' Travail -';
				 	if($contexte == 'multiple_2_2' ) echo ' ERP/IOP -';  
				 	if($contexte == 'multiple_2_3' ) echo ' Logement -';  
				 	if($contexte == 'multiple_2_4' ) echo ' Voirie -';    
				 }
				 ?>" class="menubartext"/>
			<script type="text/javascript">
				document.getElementById('progressMeter').style.visibility="hidden";
				if ("<?php echo $_SERVER['REMOTE_ADDR']?>"!="127.0.0.1") {
					document.getElementById('btnSync').style.visibility="hidden";
				}
			</script>
		</hbox>	
		
		<hbox id="nav-toolbar" >
			<label id="tbbAccueil" value="Accueil" class="text-link" />
			<label id="tbbterre" value="Territoires" class="text-link" onclick="RefreshEcran(1942,'Territoires','terre','terre');"/>
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
				 <label value="S�lectionnez un �tablissement dans" id="TitreFormSaisi" class="titre" />
				 <label id="libRub" value="Le d�partement du Nord" class="titre" />
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
				 <label value="S�lectionnez un �tablissement dans" id="syncTitreFormSaisi" class="titre" />
				 <label id="synclibRub" value="Le d�partement du Nord" class="titre" />
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
</script>

</window>

