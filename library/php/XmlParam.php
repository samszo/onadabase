<?php
/*
/////////////////////
Nom du fichier : XmlParam.php

Version : 1.0
Auteur : samszo
Date de modification : 21/11/2007

////////////////////
*/


Class XmlParam{
	public $FicXml;
	public $trace;
	private $xml;

	function __construct($FicXml) {
		$this->trace = false;

	    $this->FicXml = $FicXml;
		if($this->trace)
			echo "On charge les paramètres : ".$FicXml."<br/>\n";
		if ($xml = simplexml_load_file($FicXml))
			$this->xml = $xml;
		
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

	public function getAttribute($element, $name){
        foreach($element->attributes() as $key=>$val){
            if($key == $name){
                return (string)$val;
            }// end if
        }// end foreach
    }// end function getAttribute
}
?>