<?php
function shp2kml($filename,$name,$desc,$orig,$output)
{
	$shp = new ShapeFile($filename); // along this file the class will use file.shx and file.dbf

	ob_start();

?>
<?xml version="1.0" encoding="UTF-8" ?> 
<kml xmlns="http://earth.google.com/kml/2.1"> 
	<Document> 
<?php
	foreach($shp->records as $record)
	{
?>

		<Placemark>
  			<name><?php echo($record->dbf_data[$name]); ?></name>
  			<description><?php echo(
				'SHAPE_ID : '.str_replace(' ','',$record->dbf_data[$desc[0]]).' - '
				.'MAT_LIGNE : '.$record->dbf_data[$desc[1]].' - ' 
				); 
				?></description>

  			<Style>
    				<PolyStyle>
     					<color>44af93f3</color>
     					<fill>1</fill>
      				<outline>1</outline>
    				</PolyStyle>
    				<LineStyle>
	      				<width>2</width>
    	  				<color>ffffffff</color>
    				</LineStyle>
  			</Style>

  			<MultiGeometry>
<?php
		$toto = $record->shp_data;
		$toto = $toto['parts'];

		for ($j = 0; ($j < sizeof($toto)) ; $j++) 
		{
?>
    				<Polygon>
      				<outerBoundaryIs>
        					<LinearRing>
          						<coordinates>
<?php
			$coords = $toto [$j] ['points'];

			for ($i = 0; ($i < sizeof($coords)) ; $i++) 

			{

//
  				$tata = Lambert2WGS84($orig,$coords[$i]['x'],$coords[$i]['y']);
  				echo (" " . $tata['long'] . ", " . $tata['lat'] . ", 0 \n");
			}
?>
          						</coordinates>
        					</LinearRing>
      				</outerBoundaryIs>
    				</Polygon>
<?php
		}
?>
  			</MultiGeometry>
		</Placemark>

<?php
	}	
?>
	</Document> 
</kml>
<?php		

	$kml = ob_get_contents();

	ob_end_clean();

	$output .= '.kml';
	$fkml = fopen ($output, 'w+');	
	fputs($fkml, $kml); 
	fclose($fkml);
}
?>