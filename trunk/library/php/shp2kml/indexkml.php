<?php
/*****************************************************************
PREPARATION AND PROCESSING
*****************************************************************/

require_once('inc/ShapeFile.inc');
require_once('inc/convertL2WGS84.inc');
require_once('inc/shp2kml.inc');

//
//						.shx & .dbf files must be in the same folder
//
$filename = "donnees/parcTest.shp";

//
//						field name specific to .dbf file check it with index.php
// 						

$name = 'SHAPE_ID';
$desc[0] = 'SHAPE_ID';
$desc[1] = 'MAT_LIGNE';

//
//						LIIe for Lambert II étendue
//						LII  for Lambert II
//						L93  for Lambert 93
//
$orig = 'LIIe';
//
//                                
//
$output = 'outpout';

//
shp2kml($filename,$name,$desc,$orig,$output);
//
?>