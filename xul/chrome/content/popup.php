
<html>
 <body onload="window.focus();">
	<?php
		$xml = $_GET['reponse'];
		echo ' URL '.$xml;
		echo ' CONTENU '.$xml->saveXML();
		
	?> 

	<p><a href="javascript:window.close();">Fermer la fenetre</a></p>
 </body>
</html>
