
<html>
 <body onload="window.focus();">
	<?php
		$xml = $_GET['reponse'];
		echo ' URL '.$xml;
		//echo ' CONTENU '.$xml->saveXML();

		$dom = new DOMDocument('1.0', 'iso-8859-1');
		$dom->loadXML($_GET['reponse']);
		echo $dom->saveXML(); 
		
		
		
	?> 

	<p><a href="javascript:window.close();">Fermer la fenetre</a></p>
 </body>
</html>
