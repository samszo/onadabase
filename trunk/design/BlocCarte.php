<?php
//v�rifie si la page est include ou ajax
if(!$g){
	$ajax = true;
	require_once("../param/ParamPage.php");
}

//r�cup�ration de donn�e g�o 
$arrGeo = $g->GetGeo(-1,$idDon);
//print_r($arrGeo);

?>
<html >
  <head>

<script src="http://maps.google.com/maps?file=api&v=2.x&key=<?php echo $objSite->infos["gmKey"]; ?>" type="text/javascript"></script>
<script src="../library/js/GoogleCarto6.js" type="text/javascript"></script>

<script src="<?php echo $objSite->infos["pathXulJs"]; ?>interface.js" type="text/javascript"></script>
<script src="<?php echo $objSite->infos["pathXulJs"]; ?>ajax.js" type="text/javascript"></script>

<script type="text/javascript">
	var pathRoot = '<?php echo $objSite->infos["urlLibPhp"]; ?>';
	var deflat = <?php echo $arrGeo['lat']; ?>;
	var deflng = <?php echo $arrGeo['lng']; ?>;
	var defzoom = <?php echo $arrGeo['zoom']; ?>;
	var defType = <?php echo $arrGeo['type'];	?>;
	var idRub = <?php echo $arrGeo['id'];	?>;
	var mot = -1;
	var mapQuery = '<?php echo $arrGeo['query'];	?>';
	var site = '<?php echo $objSite->id; ?>';
	var alpha = 'a';
	MiniCarte = false;
	var urlExeAjax = "<?php echo $objSite->infos["urlExeAjax"]; ?>";

</script>
  </head>
  <body onload="initPage()" onunload="GUnload()" >

		<div id='BassinGare' style="visibility: hidden" >
			<div onclick="map.setCenter(new GLatLng(deflat, deflng), defzoom-6);map.setMapType(G_NORMAL_MAP);" >Afficher le bassin de gare</div>
			<div onclick="map.setCenter(new GLatLng(deflat, deflng), defzoom);GetMapType(defType)"; >Afficher la gare</div>
		</div>	
		<div id='map' style="height:500px;width:450px;" ></div>

  </body>
</html>
