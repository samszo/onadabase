<?php
/*****************************************************************
PREPARATION AND PROCESSING
*****************************************************************/

require_once('inc/ShapeFile.inc');

$filename = "dep_france_L2/parcellaire(essaiOK_UTM31U)_region.shp";

$shp = new ShapeFile($filename); // along this file the class will use file.shx and file.dbf

// Let's see all the records:
foreach($shp->records as $record){
     echo "<pre>"; // just to format


     print_r($record->dbf_data);   // The alphanumeric information related to the figure

     print_r($record->shp_data);   // All the data related to the poligon

     echo "</pre>";
}			
?>