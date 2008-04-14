<?php
Class Synchro{
	public $trace;
	private $siteSrc;
	private $siteDst;
	
	function __construct($siteSrc, $siteDst) {
		$this->trace = true;
		$this->siteSrc = $siteSrc;
		$this->siteDst = $siteDst;
		
	}
	
	public function GetRub($idPar){
		if($this->trace)
			echo 'Synchro:GetRub:idPar='.$idPar.'<br/>';
		$g = new Granulat($idPar,$this->siteSrc);
		$arrEnfs = $g->GetEnfantIds().split(DELIM);
		foreach($arrEnfs as $Enf)
		{
			
			//echo $siteparent."=>".$type."<br/>";
			$valeur .=" ".$this->sites[$siteparent]["NOM"]." ";
				
		}
		
		return $this->xml->xpath($Xpath);
	}
	
	public function GetNew($titre,$idGroupe)
	{
		$id = $this->VerifExist($titre);
		if($id==-1){
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetNewMC']";
			if($this->trace)
				echo "MotClef:GetNew:Xpath=".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$set = str_replace("-titre-", $this->site->GetSQLValueString($titre, "text"), $Q[0]->set);
			$set = str_replace("-idGroupe-", $idGroupe, $set);
			$sql = $Q[0]->insert.$set;
			if($this->trace)
				echo "MotClef:GetNew:sql=".$sql."<br/>";
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$db->query($sql);
			$id = mysql_insert_id();
			$db->close();
		}
		if($this->trace)
			echo "MotClef:GetNew:id=".$id."<br/>";
		return $id;
		
	}

	public function VerifExist($titre)
	{
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='VerifExistMC']";
		if($this->trace)
			echo "MotClef:VerifExist:Xpath=".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-titre-", $this->site->GetSQLValueString($titre, "text"), $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "MotClef:VerifExist:sql=".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		if ($r = $db->fetch_assoc($req)){
			$id = $r['id_mot']; 
		} else {
			$id=-1;
		}
		
		return $id;
		
	}
	
	public function Verif($idAuteur=6) {
		
		$sql = "SELECT id_rubrique, titre
		FROM spip_auteurs_rubriques
		ORDER BY titre
		WHERE id_rubrique = ".$idAuteur
		;//LIMIT 0 , 93";

		$DB = new mysql($siteDst->infos["SQL_HOST"], $siteDst->infos["SQL_LOGIN"], $siteDst->infos["SQL_PWD"], $siteDst->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
	}
			
}
?>