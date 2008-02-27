<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Google Maps</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ" type="text/javascript"></script>
  </head>
  <body onunload="GUnload()">


    <div id="map" style="width: 768px; height: 512px"></div>
    <a href="geoxml.htm">Back to the tutorial page</a>
    <br><input type="button" value="hide" onclick="map.removeOverlay(kml)">
        <input type="button" value="show" onclick="map.addOverlay(kml)">

    <noscript><b>JavaScript must be enabled in order for you to use Google Maps.</b> 
      However, it seems JavaScript is either disabled or not supported by your browser. 
      To view Google Maps, enable JavaScript by changing your browser options, and then 
      try again.
    </noscript>
 

    <script type="text/javascript">
    //<![CDATA[
    
    if (GBrowserIsCompatible()) { 


 
      var map = new GMap2(document.getElementById("map"));
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.setCenter(new GLatLng(50.63699617,3.07016373), 9);

      // ==== Create a KML Overlay ====
    
      var kml = new GGeoXml("http://localhost/onadabase/spip/IMG/kml/test.kml");
      map.addOverlay(kml);


    }
    
    // display a warning if the browser was not compatible
    else {
      alert("Sorry, the Google Maps API is not compatible with this browser");
    }

    // This Javascript is based on code provided by the
    // Blackpool Community Church Javascript Team
    // http://www.commchurch.freeserve.co.uk/   
    // http://econym.googlepages.com/index.htm

    //]]>
    </script>
  </body>

</html>




