<?php
//vérifie si la page est include ou ajax
if(!$g){
	$ajax = true;
	require_once($_SERVER["DOCUMENT_ROOT"]."/onadabase/param/ParamPage.php");
}

//création du bloc 
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
	var defzoom = <?php echo 13;//$arrGeo['zoom']; ?>;
	var defType = <?php echo $arrGeo['type'];	?>;
	var idRub = <?php echo $g->id; ?>;
	var mot = -1;
	var mapQuery = 'idFiche';
	var site = '<?php echo $objSite->id; ?>';
	var alpha = 'a';
	MiniCarte = true;

</script>
  </head>
  <body onload="initPage()" onunload="GUnload()" >

		<div id='map' style="height:200px;width:300px;" ></div>

  </body>
</html>
