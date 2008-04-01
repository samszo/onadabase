<?php
require_once ("../../../param/ParamPage.php");
session_start();
extract($_SESSION,EXTR_OVERWRITE);
extract($_POST,EXTR_OVERWRITE);

if(isset($_SESSION['loginSess']))
{
	$login=$_SESSION['loginSess'];
	$mdp=$_SESSION['mdpSess'];
	$idAuteur=$_SESSION['IdAuteur'];
	
}
else
{
	$login=$_POST['login_uti'];
	$mdp=$_POST['mdp_uti'];
}

function ChercheAbo ($login, $mdp, $objSite)
	{
		// connexion serveur
	   //$link = mysql_connect("mysql5-5", "mundilogcai", "CnVjzGxb")  or die("Impossible de se connecter : " . mysql_error());	
		// Sélection de la base de données
		//mysql_select_db("mundilogcai", $link);	
	
		$link = mysql_connect($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"]) or die("Impossible de se connecter : " . mysql_error());	
		// Sélection de la base de données
		//mysql_select_db("solacc", $link);	
		mysql_select_db($objSite->infos["SQL_DB"], $link);	
		
		$sql = "SELECT id_auteur, nom, login, email  FROM spip_auteurs WHERE login = '".$login."' AND pass = md5( CONCAT(alea_actuel,'$mdp'))";
		//echo $sql;
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			//echo $nbResultat."<br/>";
	  	$nbre_lignes = mysql_num_rows($req);
	  	//echo $nbre_lignes;
		if ($nbre_lignes == 1)
		{
			while($resultat = mysql_fetch_assoc($req))
				{	
					$_SESSION['IdAuteur'] = $resultat->id_auteur;
					$_SESSION['NomSess'] = $resultat->nom;
					$_SESSION['EmailSess'] = $resultat->email;
					$_SESSION['LoginSess'] = $resultat->login;	
					$_SESSION['IpSess'] = $_SERVER['REMOTE_ADDR'];
				}
		}
		else
		{
			include("log.php");
			exit;
		}
	}

ChercheAbo ($login, $mdp, $objSite);

header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: Saisi des diagnosics d'accessibilité");
echo '<' . '?xml version="1.0" encoding="iso-8859-15" ?' . '>';
echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
echo ('<' . '?xml-stylesheet href="onada.css" type="text/css"?' . '>' . "\n");
?>


<window
    id="wSaisiDiag"
    title="Saisi des diagnosics d'accessibilité"
    persist="screenX screenY width height"
    orient="horizontal"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul"
    onload="ChargeTreeFromAjax('idRub','treeRub','terre');"
>

<script type="application/x-javascript" src="js/interface.js" />
<script type="application/x-javascript" src="js/ajax.js"/>
<script type="application/x-javascript" src="js/tree.js"/>
<script type="application/x-javascript"  src="xbl/editableTree/functions.js" />
     <script>
		//initialise le paramètrage du site
		var lienAdminSpip = "<?php echo $objSite->infos["lienAdminSpip"]; ?>";
		var urlExeAjax = "<?php echo $objSite->infos["urlExeAjax"]; ?>";
		var xmlParam = GetXmlUrlToDoc("<?php echo $objSite->infos["jsXulParam"]; ?>");
		var synclienAdminSpip = "<?php echo $objSiteSync->infos["lienAdminSpip"]; ?>";
		var syncurlExeAjax = "<?php echo $objSiteSync->infos["urlExeAjax"]; ?>";
		var syncxmlParam = GetXmlUrlToDoc("<?php echo $objSiteSync->infos["jsXulParam"]; ?>");
     </script>

	<popupset >
		<popup id="popterre" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) établissement(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Etablissements','etab','Etab');"/>
			<menuitem label="Ajouter un établissement" oncommand="AddNewGrille('Etab');"/>
			<menuitem label="Voir le(s) tronçon(s) de voirie" oncommand="RefreshEcran(document.getElementById('idRub').value,'Voiries','voirie','Voirie');"/>
			<menuitem label="Ajouter un tronçon de voirie" oncommand="AddNewGrille('Voirie');"/>
			<menuitem label="Ajouter un territoire" oncommand="GetElementIdFromAjax(document.getElementById('idRub').value);"/>
		</popup>
		<popup id="popetab" onpopupshowing="javascript:;">
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
			<menuitem label="Voir le(s) niveau(x)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Niveaux','niveau','Niveau');"/>
			<menuitem label="Ajouter un niveau" oncommand="AddNewGrille('Niveau');"/>
			<menuitem label="Voir la(les) cabine(s) d'ascenseur" oncommand="RefreshEcran(document.getElementById('idRub').value,'Cabines Ascenseurs','objetintbat','ObjetIntBat');"/>
			<menuitem label="Ajouter la(les) cabine(s) d'ascenseur" oncommand="AddNewGrille('ObjetIntBat');"/>
		</popup>
		<popup id="popBat" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un niveau" oncommand="AddNewGrille('Niveau');"/>
			<menuitem label="Ajouter la(les) cabine(s) d'ascenseur" oncommand="AddNewGrille('ObjetIntBat');"/>
			
		</popup>
		<popup id="popniveau" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s) intérieur(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces intérieurs','espaceint','EspaceInt');"/>
			<menuitem label="Ajouter un espace intérieur" oncommand="AddNewGrille('EspaceInt');"/>
			<menuitem label="Voir les objets intérieurs" oncommand="RefreshEcran(document.getElementById('idRub').value,'Tous les objets','objetint','ObjetInt');"/>
			<menuitem label="Ajouter un objet intérieur" oncommand="AddNewGrille('ObjetInt');"/>
		</popup>
		<popup id="popNiveau" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un espace intérieur" oncommand="AddNewGrille('EspaceInt');"/>
			<menuitem label="Ajouter un objet intérieur" oncommand="AddNewGrille('ObjetInt');"/>
		</popup>
		
		<popup id="popobjetintbat" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','objetgen','ObjetGen');"/>
		</popup>
			
		<popup id="popobjetint" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','objetgen','ObjetGen');"/>
		</popup>
		<popup id="popespaceint" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','espacegen','EspaceGen');"/>
		</popup>
		<popup id="popparcelle" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s) extérieur(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces extérieurs','espaceext','EspaceExt');"/>
			<menuitem label="Ajouter un espace extérieur" oncommand="AddNewGrille('EspaceExt');"/>
			<menuitem label="Voir les objets extérieurs" oncommand="RefreshEcran(document.getElementById('idRub').value,'Objets extérieurs','objetext','ObjetExt');"/>
			<menuitem label="Ajouter un objet extérieur" oncommand="AddNewGrille('ObjetExt');"/>
		</popup>
		<popup id="popParcelle" onpopupshowing="javascript:;">
			<menuitem label="Ajouter un espace extérieur" oncommand="AddNewGrille('EspaceExt');"/>
			<menuitem label="Ajouter un objet extérieur" oncommand="AddNewGrille('ObjetExt');"/>
		</popup>
		<popup id="popespaceext" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','espaceextparamgen','EspaceExtParamGen');"/>
		</popup>
		<popup id="popobjetext" onpopupshowing="javascript:;">
			<menuitem label="Voir les paramètres de contrôle" oncommand="RefreshEcran(document.getElementById('idRub').value,'Paramètres de contôle','objetgenext','ObjetGenExt');"/>
		</popup>
		<popup id="popSyncSrc" onpopupshowing="javascript:;">
			<menuitem label="Ajouter les objets local au serveur" oncommand="SyncAjout(document.getElementById('idRub').value,'Paramètres généraux','espaceextparamgen','EspaceExtParamGen');"/>
		</popup>
		<popup id="popSyncDst" onpopupshowing="javascript:;">
			<menuitem label="Récupérer les objets du serveur" oncommand="SyncAjout(document.getElementById('idRub').value,'Paramètres généraux','espaceextparamgen','EspaceExtParamGen');"/>
		</popup>
	</popupset>


	<vbox  flex="1" style="overflow:auto">
	
		<hbox class="menubar">
		
			<image src="images/logo.png" />
			<label id="idAuteur" value="<?php echo $idAuteur; ?>" class="menubartext"/>
			<label value="Auteur du diagnostic :" class="menubartext"/>
			<label id="login" value="<?php echo $login; ?>" class="menubartext" onclick="window.location.replace('exit.php') ; "/>
			<button id="btnSync" label="Synchroniser" onclick="Synchroniser()"/>
		
		</hbox>	
		
		<hbox id="nav-toolbar" >
			<label id="tbbAccueil" value="Accueil" class="text-link" />
			<label id="tbbterre" value="Territoires" class="text-link" onclick="RefreshEcran(9,'Territoires','terre','terre');"/>
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

</window>

