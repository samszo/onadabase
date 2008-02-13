<?php
	require_once ("../param/ParamPage.php");


    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<overlay id="tabbox"
         xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

	<box id="<?php echo $objSite->scope["box"]; ?>">
<tabbox>
  <tabs>
    <tab label="Mail"/>
    <tab label="News"/>
  </tabs>
  <tabpanels>
    <tabpanel id="mailtab">
      <checkbox label="Automatically check for mail"/>
    </tabpanel>
    <tabpanel id="newstab">
      <button label="Clear News Buffer"/>
    </tabpanel>
  </tabpanels>
</tabbox>
</box>

</overlay>