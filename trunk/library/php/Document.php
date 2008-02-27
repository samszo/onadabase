<?php
class Document{
  public $type;
  public $id;
  public $fichier;
  public $largeur;
  public $hauteur;

  function __tostring() {
    return "Cette classe permet de définir et manipuler un document.<br/>";
    }

  function __construct($site, $data) {
    $this->type = $data['id_type'];
    $this->id = $data['id_document'];
    $this->fichier = $site->infos["REP_SPIP"].$data['fichier'];
    $this->largeur = $data['largeur'];
    $this->hauteur = $data['hauteur'];
	if($data['dtitre'])
		$this->titre = $data['dtitre'];
	else
		$this->titre = $data['rtitre'];
		
    //$this->DB = new mysql ($this->site["SQL_HOST"], $this->site["SQL_LOGIN"], $this->site["SQL_PWD"], $this->site["SQL_DB"], $dbOptions);


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
}

  

?>