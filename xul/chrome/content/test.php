<?
session_start();
extract($_SESSION,EXTR_OVERWRITE);
extract($_POST,EXTR_OVERWRITE);

if(isset($_SESSION['loginSess']))
{
	$login=$_SESSION['loginSess'];
	$mdp=$_SESSION['mdpSess'];
}
else
{
	$login=$_POST['login_uti'];
	$mdp=$_POST['mdp_uti'];
}

function ChercheAbo ($login, $mdp)
	{
	
		// connexion serveur
	   $link = mysql_connect("mysql5-5", "mundilogcai", "CnVjzGxb")
		   or die("Impossible de se connecter : " . mysql_error());
	
		// Sélection de la base de données
		mysql_select_db("mundilogcai", $link);	
	
	
		$sql = "SELECT nom, login, email  FROM spip_auteurs WHERE login = '".$login."' AND pass = md5( CONCAT(alea_actuel,'$mdp'))";
		//echo $sql;
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			//echo $nbResultat."<br/>";
	  	$nbre_lignes = mysql_num_rows($req);
	  	//echo $nbre_lignes;
		if ($nbre_lignes == 1)
		{
			while($resultat = mysql_fetch_assoc($req))
				{	
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

ChercheAbo ($login, $mdp);


header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: BTS Informatique section de Gap");
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
		xmlParam = GetXmlUrlToDoc("http://www.mundilogiweb.com/onadabase/xul/chrome/content/param/onadabase.xml");
     </script>

	<popupset >
		<popup id="popterre" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) établissement(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Etablissements','etab','Etab');"/>
			<menuitem label="Ajouter un établissement" oncommand="AddNewGrille('Etab');"/>
		</popup>
		<popup id="popetab" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces','espace','Espace');"/>
			<menuitem label="Ajouter un espace" oncommand="AddNewGrille('Espace');"/>
		</popup>
		<popup id="popespace" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) parcelle(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Parcelles','parcelle','Parcelle');"/>
			<menuitem label="Ajouter une parcelle" oncommand="AddNewGrille('Parcelle');"/>
			<menuitem label="Voir le(s) bâtiment(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Bâtiments','bat','Bat');"/>
			<menuitem label="Ajouter un bâtiment" oncommand="AddNewGrille('Bat');"/>
		</popup>
		<popup id="popbat" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) niveau(x)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Niveaux','niveau','Niveau');"/>
			<menuitem label="Ajouter un niveau" oncommand="AddNewGrille('Niveau');"/>
		</popup>
		<popup id="popniveau" onpopupshowing="javascript:;">
			<menuitem label="Voir le(s) espace(s) intérieur(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces intérieurs','espaceint','EspaceInt');"/>
			<menuitem label="Ajouter un espace intérieur" oncommand="AddNewGrille('EspaceInt');"/>
			<menuitem label="Voir le(s) objet(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Objets','objet','Objet');"/>
			<menuitem label="Ajouter un objet" oncommand="AddNewGrille('Objet');"/>
		</popup>
		<popup id="popespaceint" onpopupshowing="javascript:;">
			<menuitem label="Voir les espaces généraux" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces généraux','espacegen','EspaceGen');"/>
			<menuitem label="Voir le(s) espace(s) spécifique(s)" oncommand="RefreshEcran(document.getElementById('idRub').value,'Espaces spécifiques','espacespe','EspaceSpe');"/>
		</popup>
	</popupset>


	<vbox  flex="1" style="overflow:auto">
	
		<hbox class="menubar">
		
			<image src="images/logo.png" />
		
		</hbox>	
		
		<hbox class="ariane">
		
			<toolbox class="toolbox">
				<toolbar id="nav-toolbar" class="toolbar">
					<toolbarbutton id="tbbAccueil" label="Accueil" class="toolbarbutton"/>
					<toolbarbutton id="tbbterre" label="Territoires" class="toolbarbutton" onclick="RefreshEcran(9,'Territoires','terre','terre');"/>
				</toolbar>
			</toolbox>
		
		</hbox>	
		
		<hbox class="global" flex="1">
		
			<vbox class="BoiteV" flex="1" >
				<hbox>
				 <label value="Selectionner un territoire" class="titre" />
				</hbox>
				<label id="idRub" value="-1"/>
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
		
		</hbox>	

		<hbox class="footer" >
			<label control="middle" value="Version 1.0" dir="reverse"/>
		</hbox>	
		
	</vbox>

</window>

