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
	
	public function GetCount($Xpath){
		
		if($this->trace)
			echo 'XmlParam GetCount du xpath '.$Xpath.'<br/>';
		return count($this->xml->xpath($Xpath));
	}
	
	public function XML_entities($str)
	{
	    return preg_replace(array("'&'", "'\"'", "'<'", "'>'"), array('&#38;', '&#34;','&lt;','&gt;'), $str);
	}
	
}
?>