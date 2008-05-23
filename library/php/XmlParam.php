<?php
Class XmlParam{
	public $FicXml;
	public $trace;
	private $xml;

	function __construct($FicXml = -1, $src=-1) {
		$this->trace = false;
		
		if ($FicXml !=-1) {
		    $this->FicXml = $FicXml;
			if($this->trace)
				echo "On charge les paramètres : ".$FicXml."<br/>\n";
			if ($xml = simplexml_load_file($FicXml))
				$this->xml = $xml;
		}else{
			if ($xml = simplexml_load_string($src))
    			$this->xml = $xml;	
		}
	}
	
	public function GetElements($Xpath){
		if($this->trace)
			echo 'XmlParam GetElements On cherche le xpath '.$Xpath.'<br/>';
		return $this->xml->xpath($Xpath);
	}
	
	public function GetCount($Xpath){
		
		if($this->trace)
			echo 'XmlParam GetCount du xpath '.$Xpath.'<br/>';
		return count($this->xml->xpath($Xpath));
	}
	
	public function XML_entities($str)
	{
		//$str = str_replace("'","''",$str);
	    return preg_replace(array("'&'", "'\"'", "'<'", "'>'"), array('&#38;', '&#34;','&lt;','&gt;'), $str);
	}

}
?>