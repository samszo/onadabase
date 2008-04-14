<?php
class Grille{
  public $id;
  public $XmlParam;
  public $trace;
  private $site;

  function __tostring() {
    return "Cette classe permet de d�finir et manipuler des grilles.<br/>";
    }

  function __construct($site, $id=-1, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";
	$this->trace = true;

    $this->site = $site;
    $this->id = $id;
	if($this->site->scope["FicXml"]!=-1)
		$this->XmlParam = new XmlParam($this->site->scope["FicXml"]);
	
	if($complet){
	}

	//echo "FIN new grille <br/>";
		
    }

    function GetObjId($donId,$obj) {
    	$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetId".$obj."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row["id"];
    	
    }
    
    function GereWorkflow($row, $donId) {

    	
    	$Xpath = "/XmlParams/XmlParam/workflow[@srcId='".$row['grille'].";".$row['champ']."']";
		if($this->trace)
			echo "Grille:GereWorkflow:r�cup�re les param�tre du workflow � ex�cuter ".$Xpath."<br/>";
    	$wfs = $this->site->XmlParam->GetElements($Xpath);
		foreach($wfs as $wf)
		{
			$id = $this->GetObjId($donId,$wf['dstObj']);
			if($this->trace)
				echo "//r�cup�re l'identifiant de l'objet ".$wf['dstObj']." ".$id."<br/>";
			switch ($wf['dstQuery']) {
				case "AddNewArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewArtGrille ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";					
					if($row['valeur']==$wf['srcCheckVal']){						
						//r�cup�ration du granulat
						$gra = new Granulat($id,$this->site);
						$gTrs = new Granulat($wf['trsId'],$this->site);
						
						if($wf['trsObjet']=="controles" ){
							$id = $gra->SetNewEnfant($gTrs->titre." ".date('j/m/y - H:i:s'));
							$this->AddQuestionReponse($wf['trsId'],$id);
							if($wf['trsId']=="50" || $wf['trsId']=="74" ){ // Porte
								$id1 = $gra->SetNewEnfant("Face 1 ".date('j/m/y - H:i:s'));
								$this->AddQuestionReponse(1342,$id1);
								$id2 = $gra->SetNewEnfant("Face 2 ".date('j/m/y - H:i:s'));
								$this->AddQuestionReponse(1341,$id2);
							}
						}else{
							$idArt = $gra->SetNewArticle($gTrs->titre." ".date('j/m/y - H:i:s'));
							if($this->trace)
								echo ":GereWorkflow://ajoute une nouveau article ".$idArt."<br/>";
							//ajoute une nouvelle donnee
							$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);
							//r�cup�re le formulaire xul
							$xul = $this->GetXulForm($idDon,$wf['trsId']);
						}
						//renvoie le formulaire
						return $xul;
					}
					break;	
				case "AddNewMotClef":	
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewMotClef ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";	
					if($row['valeur']==$wf['srcCheckVal']){	
						$gra = new Granulat($id,$this->site);	
						if($wf['trsObjet']=="motclef" ){
							$gra->SetMotClef($wf['trsId'],$id);
						}	
					}
					break;	
				default:								
					if($this->trace)
						echo "//workflow path query ".$wf['dstQuery']."<br/>";
					
					$Q = $this->site->XmlParam->GetElements($wf['dstQuery']);
					$where = str_replace("-id-", $id, $Q[0]->where);
					$set = str_replace("-param-", $row['valeur'], $Q[0]->set);
					$sql = $Q[0]->update.$set.$where;
					$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
					$db->connect();
					$db->query($sql);
					$db->close();
					if($this->trace)
						echo "//ex�cution du workflow ".$sql."<br/>";
				break;
			}								
		}
		
	}	

	function GetGrilleId($rows, $donId) {

    	$Xpath = "/XmlParams/XmlParam/majliee[@srcId='55;ligne_1']/@dstQuery";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "//r�cup�ration des valeurs de workflow ".$donnees."<br/>";
    	
		//suppression des �ventuelle champ pour la donn�e
		$this->DelDonnee($donId);
		
		//cr�ation des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	
	function AddXmlDonnee($xmlSrc){
			
		if($this->trace)
			echo "Grille/AddXmlDonnee IN //r�cuparation de la d�finition des donn�es ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc);		
		
		$Xpath = "/donnees";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "Grille/AddXmlDonnee/r�cup�ration des valeurs de donn�e ".$donnees."<br/>";
		
		$idGrille = $donnees[0]->grille;
		if($this->trace)
			echo "Grille/AddXmlDonnee/r�cup�ration de l'identifiant de la grille ".$idGrille."<br/>";
		
		//r�cup�ration de la d�finition des champs
		$Xpath = "/donnees/champs";
		$champs = $xml->GetElements($Xpath);
		$first=true;
		foreach($donnees[0]->donnee as $donnee)
		{
			$idRub = $donnee->rub;
			if($this->trace)
				echo "Grille/AddXmlDonnee/- r�cup�ration de l'identifiant de la rubrique ".$idRub."<br/>";
			
			//r�cuparation du granulat
			$g = new Granulat($idRub, $this->site); 
			$idArt = $g->GetArticle();
			if($this->trace)
				echo "Grille/AddXmlDonnee/- r�cup�ration ou cr�ation du dernier article en cours de r�daction ".$idArt."<br/>";
			
			if($first){
				$this->DelGrilleArt($idGrille,$idArt);
				if($this->trace)
					echo "Grille/AddXmlDonnee/suppression des anciennes donn�es ".$idArt."<br/>";
				$first=false;
			}
				
			$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
			if($this->trace)
				echo "Grille/AddXmlDonnee/- cr�ation de la donnee ".$idDon."<br/>";

			$i=0;
			foreach($donnee->valeur as $valeur)
			{
				if($valeur!='non'){
					$valeur=utf8_decode($valeur);
					$champ = $champs[0]->champ[$i];
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
					if(substr($champ,0,8)=="multiple"){
						$valeur=$champ;
						//attention il ne doit pas y avoir plus de 10 choix
						$champ=substr($champ,0,-2);
					}
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- r�cup�ration du type de champ ".$champ."<br/>";
					$row = array('champ'=>$champ, 'valeur'=>$valeur);
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- r�cup�ration de la valeur du champ ".$valeur."<br/>";
					$this->SetChamp($row, $idDon,false);
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- cr�ation du champ <br/>";
				}
				$i++;
			}
			
		}
		if($this->trace)
			echo "Grille/AddXmlDonnee OUT //<br/>";
		
	}
    
    function AddGrilles($idRubSrc, $idRubDst, $redon=false){
			
		//r�cuparation des grilles des articles publi�s de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetGrillesPublie']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "AddGrilles ".$idRubSrc." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		
		$result = ""; 
		while ($row =  $db->fetch_assoc($rows)) {
			$idDon = $this->AddDonnee($idRubDst, $row["id_form"], $redon);
			$result .= $row["id_form"]." ".$row["titre"]." ".$idDon."<br/>";		
		}
		
		return $result;
	}
	
	
	function AddQuestionReponse($idRubSrc, $idRubDst){
		
		//cr�ation du granulat
		$g = new Granulat($idRubDst,$this->site);
					
		//pour les controles r�cup�ration des rubriques dans les liens de la rubrique Src 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubInLiens']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:AddQuestionReponse:rubSrc".$sql."<br/>";
		
		//r�cup�ration du droit de la derni�re donn�e pour la rubrique parente de la destination
		$droit = $this->GetDroitParent($g->IdParent);
				
		while ($row =  $db->fetch_assoc($rows)) {		
			//r�cup�ration des questions publi� pour un type de controle
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_AddQuestion']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-idRubSrc-", $row['idRub'], $Q[0]->where);
			$sql = $Q[0]->select.$Q[0]->from.$where;
			$dbQ = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
			$dbQ->connect();
			$rowsQ = $dbQ->query($sql);
			$dbQ->close();
			if($this->trace)
				echo "Grille:AddQuestionReponse:Liste question".$sql."<br/>";
			$first=true;
			$rowQo = -1;
			while ($rowQ =  $dbQ->fetch_assoc($rowsQ)) {
				if($first){
					//ajoute le mot clef type de controle � la rubrique
					$g->SetMotClef($rowQ["typecon"]);
					$first=false;
				}
				//v�rifie si le contr�le est coh�rent par rapport au parent
				if($this->GereCoheDroit($rowQ, $droit)){
					//prise en compte des doublons suite � l'attribution de plusieurs droits
					if($rowQo != $rowQ["ref"]){
						//ajoute une nouvelle donn�e r�ponse pour la question
						$idDon = $g->GetIdDonnee($rowQ["FormRep"],-1,true);
						if($this->trace)
							echo "Grille:AddQuestionReponse:ajoute une nouvelle donn�e r�ponse pour la question".$idDon."<br/>";
						//ajoute la question
						$r = array("champ"=>"ligne_2","valeur"=>$rowQ["question"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la r�f�rence
						$r = array("champ"=>"ligne_1","valeur"=>$rowQ["ref"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la r�ponse par d�faut
						$r = array("champ"=>"mot_1","valeur"=>$rowQ["valdef"]);
						$this->SetChamp($r,$idDon,false);					
						$rowQo = $rowQ["ref"];
					}
				}
			}
		}
		
	}
	
	function GereCoheDroit($rQ, $droit){

		return true;
		
		//v�rifie si la question est coh�rente par rapport au questionnaire parent
		//$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rQ['id_form'].";".$row['droit']."']";
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@dstId='".$rQ['id_form']."' and @dstCheckVal='".$rQ['droit']."' and @srcCheckVal='".$droit."' ]";
		if($this->trace)
			echo "Grille:GereCoheDroit:r�cup�re la coh�rence ".$Xpath."<br/>";
    	$coh = $this->site->XmlParam->GetCount($Xpath);
		if($this->trace)
			echo "Grille:GereCoheDroit:coh=".$coh."<br/>";
    
    	if($coh>0)
    		$cohe=true;
    	else
    		$cohe=false;
		return $cohe;
	}

	function GetDroitParent($id){
		//r�cup�ration des droits pour la rubrique parente
		$rParDon = $this->GetLastDonne($id);

		//r�cup�re le champ droit de la donn�e du parent
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rParDon['id_form']."']/@srcChamp";
    	$srcChamps = $this->site->XmlParam->GetElements($Xpath);
		$srcChamp = $srcChamps[0];
		
		//r�cup�re la valeur du champ droit
		$droit = $this->GetValeur($rParDon['id_donnee'], $srcChamp);
		
		return $droit;
	}

	function GetValeur($idDon, $champ){
		//r�cup�re la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetValeurChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-champ-", $champ, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['valeur'];
	}
	
	function GetLastDonne($id){
		//r�cup�ration de la derni�re donn�e d'une rubriques 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetLastDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row;
	}
	
	function AddDonnee($idRub, $idGrille=-1, $redon=false, $idArt=-1){
		
		if($idGrille==-1)
			$idGrille=$this->id;
			
		//r�cuparation du granulat
		$g = new Granulat($idRub, $this->site);
		
		if($idArt==-1)
			//"r�cup�ration ou cr�ation du dernier article en cours de r�daction"; 
			$idArt = $g->GetArticle(" AND a.statut='prepa'");
				
		if($redon){
			//r�cup�re les derni�res donn�es publi�es
			$g = new Granulat($redon, $this->site);
			$rows = $g->GetGrille($idGrille, " AND a.statut='publie'");
			$oDonnee="";
			while ($row =  mysql_fetch_assoc($rows)) {
				//v�rifie s'il on change de donnee
				if($row["id_donnee"]!=$oDonnee){
					$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
					$oDonnee=$row["id_donnee"];
				}
				$this->SetChamp($row, $idDon, false);
				//echo "--- ".$donId." nouvelle valeur ".$i;
			}
		}else{
			//r�cup�ration ou cr�ation d'une nouvelle donn�e
			$idDon = $g->GetIdDonnee($idGrille, $idArt);
			//r�cup�re la d�finition des champs sans valeur
			$rows = $this->GetChamps($idGrille);
			//initialisation de la donn�e
			$this->SetChamps($rows, $idDon);
		}

		//echo "idRub = ".$idRub." idArt = ".$idArt." idDon = ".$idDon."<br/>"; 
		return $idDon;
	
	}

	function GetChamps($idGrille=-1){
	
		if($idGrille==-1)
			$idGrille=$this->id;

		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChamps']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idGrille-", $idGrille, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo $sql."<br/>";
		
		return $result;
	
	}
	
	function SetChamps($rows, $donId) {

		//suppression des �ventuelle champ pour la donn�e
		$this->DelDonnee($donId);
		
		//cr�ation des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	  
	function DelDonnee($donId) {

		//Supression des valeurs de champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "Grille:DelDonnee:".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	

	function DelGrilleDonnee($donId) {

		//Supression des valeurs de champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelGrilleDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "Grille:DelGrilleDonnee:".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	
	
	function DelGrilleArt($idGrille, $idArt) {

		if($this->trace)
			echo "Grille:DelGrilleArt:GetDonneeArtForm $idGrille, $idArt<br/>";
		//r�cup�ration des donn�es pour un article et une grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeArtForm']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idArt-", $idArt, $Q[0]->where);
		$from = str_replace("-idGrille-", $idGrille, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:DelGrilleArt:GetDonneeArtForm=".$sql."<br/>";
		//echo $sql."<br/>";
		while ($r =  $db->fetch_assoc($result)) {
			//Supression des valeurs de champ
			$this->DelDonnee($r["id_donnee"]);
			//suppression des donnee
			$this->DelGrilleDonnee($r["id_donnee"]);
		}
		
	}	
	
	function SetChamp($row, $donId, $del=true) {

		if($del)
			//supression de la valeur
			$this->DelChamp($row, $donId); 
		
		//prise en compte des choix multiple
		if($row["valeur"]=="supprime")
			return;
		
		//cr�ation de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_InsChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$values = str_replace("-idDon-", $donId, $Q[0]->values);
		$values = str_replace("-champ-", $row["champ"], $values);
		$values = str_replace("'-val-'", $this->site->GetSQLValueString($row["valeur"],"text"), $values);
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
		
	function DelChamp($row, $donId) {

		//supression de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$where = str_replace("-champ-", $row["champ"], $where);
		//prise en compte des choix multiples
		if(substr($row['champ'], 0, 8)=='multiple')
			$where = str_replace("-choix-", $row["valeur"], $where);
		else
			$where = str_replace("AND valeur = '-choix-'", "", $where); 
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo $sql." ".substr($row['champ'], 0, 8)."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
	
	function GetXulTab($src, $id, $dst="Rub", $recur = false){

		//chaque ligne est un onglet
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabForm".$dst."']";
		if($this->trace)
			echo "GetXulTab Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetXulTab ".$dst." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();

		//initialisation de la tabbox
		$tabbox = '<tabbox flex="1" id="tabbox_'.$src.'_'.$dst.'_'.$id.'">';
		$tabbox .= '<tabs>';
		$i=0;
		while ($r =  $db->fetch_assoc($result)) {
			$tabbox .= '<tab id="tab'.$r["id"].'" label="'.$r["titre"].'" />';
			if($Q[0]->dst=='Form')
				$tabpanels .= $this->GetXulTabPanels($r["idArt"], $r["id"],'Form',$recur);
			else
				$tabpanels .= $this->GetXulTabPanels($src, $r["id"],$Q[0]->dst,$recur);
			$i++;
		}
		
		if($i!=0){
			$tabbox .= '</tabs>';
			$tabbox .= '<tabpanels>';
			$tabbox .= $tabpanels;
			$tabbox .= '</tabpanels>';
			$tabbox .= '</tabbox>';
		}else
			$tabbox = "";
			
		return $tabbox;
		
	}


	function GetXulTabPanels($src, $id, $dst="Rub", $recur = false){

		//r�cup�re les articles de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabPanels".$dst."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$where = str_replace("-src-", $src, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetXulTabPanels ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();

		//initialisation du panel
		$tabpanel = '<tabpanel flex="1" id="tabpanel_'.$src.'_'.$dst.'_'.$id.'">';	
		
		//ajoute les onglets des sous rubriques
		if($recur)
			$tabpanel .= $this->GetXulTab($src, $id, $dst, $recur);
		
		//ajoute les groupbox pour chaque article
		If($id==$this->site->infos["GRILLE_REP_CON"]){
			$tabpanel .='<grid flex="1">';
			//on cache la colonne de r�f�rence	
			$tabpanel .='<columns>';	
			$tabpanel .='<column hidden="true"/>';	
			$tabpanel .='<column flex="1"/>';
			$tabpanel .='<column />';			
			$tabpanel .='</columns>';	
			$tabpanel .='<rows>';	
			$tabpanel .='<row><label value="R�f�rence" hidden="true" /><label value="Question"/><label value="R�ponse"/></row>';	
		}
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form'){
				//ajoute les donn�es de chaque article
				$tabpanel .= $this->GetXulForm($r["id"], $id);
			}else{
				//ajoute la tabbox de destination
				$tabpanel .= $this->GetXulTab($src, $r["id"], $Q[0]->dst, $recur);	
			}
		}
		If($id==$this->site->infos["GRILLE_REP_CON"]){
			$tabpanel .='</rows>';	
			$tabpanel .='</grid>';	
		}
		$tabpanel .= '</tabpanel>';

		return $tabpanel;
	}

  function GetRubDon($idDon) {
  
  
		//requ�te pour r�cup�rer la rubrique de la donn�e
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubDon']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetRubDon ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$r = $db->fetch_assoc($req);
		
		return $r["id"];
		
		
	}
	
			
  function GetXulForm($idDon, $idGrille) {
  
  
		//requ�te pour r�cup�rer les donn�es de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-idGrille-", $idGrille, $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetXulForm ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		//ajoute les controls pour chaque grille
		If($idGrille==$this->site->infos["GRILLE_REP_CON"]){
			$form = '<row>';	
		}else{
			$form = '<grid flex="1">';	
			$form .= '<columns flex="1">';	
			$labels = '<column flex="1" align="end">';	
			//$form .= '<caption label="Donn�e : '.$idDon.'"/>';
			$controls = '<column flex="1">';
		}
		$oChamp = "";
		while($r = $db->fetch_assoc($req)) {
			
			$idDoc = 'val'.DELIM.$idGrille.DELIM.$r["id_donnee"].DELIM.$r["champ"].DELIM.$r["id_article"];
			if($this->trace)
				echo "GetXulForm/construction de l'identifiant ".$idDoc."<br/>";
			switch ($idGrille) {
				case $this->site->infos["GRILLE_REG_LEG"]:
					//construstion de la r�gle l�gislative
					$labels .= '<label control="first" multiligne="true" value="'.$r['titre'].'"/>';
					$controls .= $this->GetXulRegLeg($idDoc, $r);
					break;					
				default:
					if($this->trace)
						echo "GetXulForm //prise en compte des champs multiples ".$oChamp." MultiSelect=".$MultiSelect."<br/>";
					if($oChamp == $r['champ']){
						if($this->trace)
							echo "GetXulForm affiche le nouveau champ<br/>";
						$controls .= $this->GetXulControl($idDoc, $r);
						//conserve la valeur
						$MultiSelect .= "'".$r['valeur']."',";
					}else{
						$labels .= '<label control="first" multiligne="true" value="'.$r['titre'].'"/>';
						if(substr($r['champ'], 0, 8)=='multiple'){
							if($this->trace)
								echo "GetXulForm d�but construction du multiple<br/>";
							$controls .= '<groupbox id="'.$id.'" '.$js.' >';
							$controls .= '<hbox>';
							//affiche le bouton s�lecionn�
							$controls .= $this->GetXulControl($idDoc, $r);
							//conserve la valeur
							$MultiSelect .=  "'".$r['valeur']."',";
						}else{
							//v�rifie si la ligne pr�c�dente �tait multiple
							if($MultiSelect!=""){
								//r�cup�re les multiples non s�lectionn�
								$controls .= $this->GetXulControl($idDoc, $r,substr($MultiSelect,0,-1));
								//fin du multiselect
								$controls .= '</hbox>';
								$controls .= '</groupbox>';
								$MultiSelect = "";
							}else{
								$controls .= $this->GetXulControl($idDoc, $r);
							}
						}
						//conserve la ligne pour la fin
						$lastRow = $r;
						$oChamp = $r['champ'];						
					}
			}
		}
		if($this->trace)
			echo "GetXulForm // FIN prise en compte des champs multiples ".$oChamp." MultiSelect=".$MultiSelect."<br/>";
		if($MultiSelect!=""){
			//r�cup�re les multiple non s�lectionn�
			$controls .= $this->GetXulControl($idDoc, $lastRow, substr($MultiSelect,0,-1));
			//fin du multiselect
			$controls .= '</hbox>';
			$controls .= '</groupbox>';
		}
		If($idGrille!=$this->site->infos["GRILLE_REP_CON"]){
			$controls .= '</column>';	
			$labels .= '</column>';	
			$form .= $labels.$controls.'</columns>';
		}
		if($idGrille == $this->site->infos["GRILLE_GEO"]){
			$form .= '<groupbox >';	
			$form .= '<caption label="Cartographie"/>';
			//ajoute la carte
			$form .= $this->GetXulCarto($idDon);
			$form .= '</groupbox>';
		}
		
		If($idGrille==$this->site->infos["GRILLE_REP_CON"])
			$form .= $controls.'</row>';	
		else
			$form .= '</grid>';	

		return $form;
	
	}
	
	function GetXulCarto($idDon)
	{
	
		return	"<iframe height='550px' width='450px' src='http://www.mundilogiweb.com/onadabase/design/BlocCarte.php?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
	
	
	}

	function GetXulRegLeg($id, $row)
	{
		
		/*r�sultat de row
		champ 	rang 	titre 	type 	obligatoire 	extra_info 	
		ligne_1 	6 	valeur �talon 	ligne 	  	  	  	  	  	 
		ligne_2 	7 	valeur �talon 2 	ligne 	  	  	  	  	  	 
		ligne_3 	4 	Nom de la valeur 	ligne 	  	  	  	  	  	 
		mot_1 	5 	op�rateur 		mot 	18 	  	  	  	 
		mot_2 	8 	Unit�s 		mot 	19 	  	  	  	 
		select_1 	9 	r�gle respect�e 	select radio		
		*/
		
		switch ($row['champ']) {
			case 'ligne_1':
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_2':
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_3':
				//construction du control
				$control = '<label value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			case 'mot_1':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'mot_2':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'select_1':
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '<label id="trace'.$id.'" value=""/>';
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
		}

		return $control;
	
	}
	
	function GetXulControl($id, $row, $MultiSelect="")
	{
		$control = '';
		switch ($row['type']) {
			case 'multiple':
				if($this->trace)
					echo "GetXulControl MultiSelect=".$MultiSelect."<br/>";
				$id = 'val'.DELIM.$row["grille"].DELIM.$row["id_donnee"].DELIM.$row["champ"].DELIM.$row["id_article"].DELIM.$r['choix'];
				$control .= $this->GetChoixVal($row,'multiple',$MultiSelect);
				break;
			case 'select':
				//prise en compte de l'affichage liste
				if($row['extra_info']=="liste"){
					//r�cup�ration des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
					$control .= $this->GetChoixVal($row,'menuitem');				
					$control .= '</menupopup></menulist>';
				}else{				
					//r�cup�ration des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<groupbox>';
					//$control .= '<caption label="'.$row['titre'].'"/>';
					$control .= '<radiogroup id="'.$id.'" '.$js.' >';
					$control .= '<hbox>';
					$control .= $this->GetChoixVal($row);
					$control .= '</hbox>';
					$control .= "</radiogroup>";
					$control .= '</groupbox>';
				}
				break;
			case 'mot':
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
				$control .= $this->GetChoixVal($row,'menuitem');				
				$control .= '</menupopup></menulist>';
				break;
			default:
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				if($row["grille"]==$this->site->infos["GRILLE_REP_CON"]){
					//on cache le textbox r�f�rence
					if($row["champ"]=="ligne_1")
						$control .= '<textbox  '.$js.' hidden="true" multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
					else
						$control .= '<textbox  '.$js.' multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				}else{
					$control .= '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				}
		}	
		
		$control .= '<label id="trace'.$id.'" hidden="true" value=""/>';

		return $control;

	}

	function GetChoixVal($row,$type='radio',$multiSelect="")
	{
		//requ�te pour r�cup�rer les donn�es de la grille
		if($multiSelect!="")
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."NotIn']";
		else
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."']";
		
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $row['grille'], $Q[0]->where);
		$where = str_replace("-champ-", $row['champ'], $where);
		$where = str_replace("-extra_info-", $row['extra_info'], $where);
		$where = str_replace("-valeur-", $row['valeur'], $where);
		$where = str_replace("-multiSelect-", $multiSelect, $where);
				
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetChoixVal ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();

		$control = "";
		while($r = $db->fetch_assoc($req)) {
			$select = 'false';
			if($row['valeur']==$r['choix'])
				$select = 'true';
			
			if($this->trace)
				echo "extra_info ".$row['extra_info']." type ".$type." "."select ".$select." ".$row['valeur']."==".$r['choix']."<br/>";
						
			switch ($type) {
				case 'radio':
					$control .= "<radio id='".$r['choix']."' selected='".$select."' label=\"".$this->site->XmlParam->XML_entities($r["titre"])."\"/>";
					break;
				case 'menuitem':
					$control .= "<menuitem id='".$r['choix']."' value='".$r['choix']."' selected='".$select."' label=\"".$this->site->XmlParam->XML_entities($r['titre'])."\"/>";
					break;
				case 'multiple':
					if($multiSelect=="")
						$select = 'true';
					//r�cup�ration des js
					$id = 'val'.DELIM.$row["grille"].DELIM.$row["id_donnee"].DELIM.$row["champ"].DELIM.$row["id_article"].DELIM.$r['choix'];
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='multiple']";
					$js = $this->site->GetJs($Xpath, array($id));
					$control .= "<checkbox ".$js." id='".$id."' checked='".$select."' label=\"".$this->site->XmlParam->XML_entities($r['titre'])."\"/>";
					break;
			}
		}
		
		return $control;

	}
	
  }


?>