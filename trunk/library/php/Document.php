<?php
class Document{
  public $type;
  public $id;
  public $fichier;
  public $largeur;
  public $hauteur;
  public $trace;
  private $site;
  
  function __tostring() {
    return "Cette classe permet de définir et manipuler un document.<br/>";
    }

  function __construct($site, $data=-1) {
  	
		$this->site = $site;
		$this->trace = TRACE;
  		
		if($data!=-1){
		    $this->type = $data['id_type'];
		    $this->id = $data['id_document'];
		    $this->fichier = $site->infos["pathSpip"].$data['fichier'];
		    $this->largeur = $data['largeur'];
		    $this->hauteur = $data['hauteur'];
			if($data['dtitre'])
				$this->titre = $data['rtitre'];
		    if($data['dtitre'])
				$this->titre = $data['dtitre'];
	  	}	


    }

	 function DimensionImage($LargeurMax, $HauteurMax, $fic="") {
		
		if($fic=="")
			$fic=$this->fichier;
			
		//echo "$Image, $HauteurMax, $LargeurMax, $Hauteur, $Largeur \n";
		$Dimension ="";
		if($this->largeur > $this->hauteur) {
			$Dimension = "width='".$LargeurMax."' ";
		}else {
			$Dimension = "height='".$HauteurMax."' ";
		}
		//echo "src='".$Image."' ".$Dimension." \n";
		return "<img src=\"".$fic."\" ".$Dimension." alt=\"".$this->titre."\" border=\"0\" align=\"absbottom\" />";
	
	}

	function GetVignette($LargeurMax, $HauteurMax)
	{
		$vignette = $this->fichier;
		$vignette = str_replace("jpg/", "", $vignette);
		$vignette = str_replace("IMG/", "IMG/vignettes/", $vignette);
		$vignette = $this->DimensionImage($LargeurMax, $HauteurMax, $vignette);
		
		return $vignette;
		
	}
	
	function AddNew($row)
	{
		//ajoute un nouveau document dans la table
		if($this->trace)
			echo "Document:AddNew:row=".print_r($row)."<br/>";
		
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='AddNewDoc']";
		if($this->trace)
			echo "Document:AddNew:Xpath=".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);		
		$values = str_replace("-titre-", $row['titre'], $Q[0]->values);
		$values = str_replace("-type-", $row['type'], $values);
		$values = str_replace("-desc-", $row['desc'], $values);
		$values = str_replace("-fichier-", $row['fichier'], $values);
		$values = str_replace("-taille-", $row['taille'], $values);
		$values = str_replace("-largeur-", $row['largeur'], $values);
		$values = str_replace("-hauteur-", $row['hauteur'], $values);
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo "Document:AddNew:sql=".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$db->query($sql);
		$id = mysql_insert_id();
		$db->close();

		if($this->trace)
			echo "Document:AddNew:id=".$id."<br/>";

		//ajoute la relation avec la destination
		if($row['idArt']){	
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='AddNewArtDoc']";
			if($this->trace)
				echo "Document:AddNew:Xpath=".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$values = str_replace("-idArt-", $row['idArt'], $Q[0]->values);
			$values = str_replace("-idDoc-", $id, $values);
			$sql = $Q[0]->insert.$values;
			if($this->trace)
				echo "Document:AddNew:sql=".$sql."<br/>";
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$db->query($sql);
			$db->close();
		}
			
			
		return $id;
		
	}
	
}
?>