<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <title>Google Earth API Samples - Fetch KML (Interactive)</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
  <style type="text/css">@import "static/examples.css";</style>
  <style type="text/css">@import "static/prettify/prettify.css";</style>
  <script type="text/javascript" src="static/prettify/prettify.js"></script>

  <script type="text/javascript" src="http://www.google.com/jsapi?hl=en&amp;key=ABQIAAAAwbkbZLyhsmTCWXbTcjbgbRSzHs7K5SvaUdm8ua-Xxy_-2dYwMxQMhnagaawTo7L1FE1-amhuQxIlXw"></script>
  <script type="text/javascript">
  /* <![CDATA[ */
  var ge;
  google.load("earth", "1");

  function init() {
    google.earth.createInstance('map3d', initCB, failureCB);
  }
  
  function initCB(instance) {
    ge = instance;
    ge.getWindow().setVisibility(true);

    
    // add a navigation control
    ge.getNavigationControl().setVisibility(ge.VISIBILITY_AUTO);
    
    // add some layers
    ge.getLayerRoot().enableLayerById(ge.LAYER_BORDERS, true);
    ge.getLayerRoot().enableLayerById(ge.LAYER_ROADS, true);

    // fly to Santa Cruz
    var la = ge.createLookAt('');
    la.set(48.45140718648201, 1.2349748611450195,
      0, // altitude
      ge.ALTITUDE_RELATIVE_TO_GROUND,
      0, // heading
      0, // straight-down tilt
      5000 // range (inverse of zoom)
      );
    ge.getView().setAbstractView(la);
    
    document.getElementById('installed-plugin-version').innerHTML =
      ge.getPluginVersion().toString();
  }
  
  function failureCB(errorCode) {
  }
  
  var currentKmlObject = null;
  
  function fetchKmlFromInput() {
    // remove the old KML object if it exists
    if (currentKmlObject) {
      ge.getFeatures().removeChild(currentKmlObject);
      currentKmlObject = null;
    }
    
    var kmlUrlBox = document.getElementById('kml-url');
    var kmlUrl = kmlUrlBox.value;
    
    google.earth.fetchKml(ge, kmlUrl, finishFetchKml);
  }
  
  function finishFetchKml(kmlObject) {
    // check if the KML was fetched properly
    if (kmlObject) {
      // add the fetched KML to Earth
      currentKmlObject = kmlObject;
      ge.getFeatures().appendChild(currentKmlObject);
    } else {
      alert('Bad KML');
    }
  }
  
  function buttonClick() {
    fetchKmlFromInput();
  }
  
  /* ]]> */
  </script>
</head>
<body onload="if(window.prettyPrint)prettyPrint();init();">
  <h1>Google Earth API Samples - Fetch KML (Interactive)</h1>
  <dl>
            <dt>Last Modified:</dt><dd>10/31/2008</dd>

    <dt>Installed Plugin Version:</dt><dd id="installed-plugin-version">...</dd>
  </dl>
  <div style="clear: both;"></div>
  
  <div id="ui" style="position: relative;">
    <div id="map3d_container" style="border: 1px solid #000; width: 500px; height: 500px;">
      <div id="map3d" style="height: 100%;"></div>
    </div>
  
    <div id="extra-ui" style="position: absolute; left: 520px; top: 0;">

<h2>Fetch KML at this URL:</h2>
<form action="#" method="get" onsubmit="return false;">
<input type="text" id="kml-url" size="50" value="http://earth-api-samples.googlecode.com/svn/trunk/examples/static/red.kml"/><br/>
<input type="submit" onclick="buttonClick()" value="Fetch KML!"/>
</form>
      <h2>Relevant Resources:</h2>
      <ul>
<li><a href="http://code.google.com/apis/earth/documentation/reference/google_earth_namespace.html">google.earth Namespace Reference</a></li>
<li><a href="http://code.google.com/apis/earth/documentation/reference/interface_g_e_plugin.html">GEPlugin Reference</a></li>
<li><a href="http://code.google.com/apis/earth/documentation/reference/interface_g_e_feature_container-members.html">GEFeatureContainer Members</a></li>

<li><a href="http://code.google.com/apis/kml/documentation/kmlreference.html">KML Reference</a></li>
      </ul>
      <h2>Relevant Code Excerpt:</h2>
      <pre class="prettyprint lang-js">var currentKmlObject = null;

function fetchKmlFromInput() {
  // remove the old KML object if it exists
  if (currentKmlObject) {
    ge.getFeatures().removeChild(currentKmlObject);
    currentKmlObject = null;
  }
  
  var kmlUrlBox = document.getElementById('kml-url');
  var kmlUrl = kmlUrlBox.value;
  
  google.earth.fetchKml(ge, kmlUrl, finishFetchKml);
}

function finishFetchKml(kmlObject) {
  // check if the KML was fetched properly
  if (kmlObject) {
    // add the fetched KML to Earth
    currentKmlObject = kmlObject;
    ge.getFeatures().appendChild(currentKmlObject);
  } else {
    alert('Bad KML');
  }
}</pre>
    </div>
  </div>
</body>
</html>
