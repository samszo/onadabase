<?php
//v�rifie si la page est include ou ajax
if(!$g){
	$ajax = true;
	require_once("../param/ParamPage.php");
}

//cr�ation du bloc 
$arrGeo = $g->GetGeo();
//print_r($arrGeo);

?>
<html >
  <head>

	<script src="http://maps.google.com/maps?file=api&v=2.x&key=<?php echo $objSite->infos["gmKey"]; ?>" type="text/javascript"></script>
<script src="../library/js/GoogleCarto3.js" type="text/javascript"></script>

<script type="text/javascript">
	var pathRoot = '<?php echo $objSite->infos["urlLibPhp"]; ?>';
	var deflat = <?php echo $arrGeo['lat']; ?>;
	var deflng = <?php echo $arrGeo['lng']; ?>;
	var defzoom = <?php echo $arrGeo['zoom']; ?>;
	var defType = <?php echo $arrGeo['type'];	?>;
	var idRub = <?php echo $g->id; ?>;
	var mot = -1;
	var mapQuery = 'admin';
	var site = '<?php echo $objSite->id; ?>';
	var alpha = 'a';
	MiniCarte = false;

</script>
  </head>
  <body onload="initPage()" onunload="GUnload()" >

		<div id='map' style="height:500px;width:450px;" ></div>

  </body>
</html>
