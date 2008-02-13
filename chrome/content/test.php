<?php
header('Content-type: application/vnd.mozilla.xul+xml');
print '<?xml version="1.0" encoding="ISO-8859-1" ?>
<window id="ieml-global" title="IEML-10eF v0.1 - information economy meta language - Dixième Famille" orient="horizontal" left="0" top="0" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<box id="singlebox" >

	<menu id="help-menu" label="Niveau 10">

		<menupopup id="help-popup">
	    <menuitem id="help-contents" label="RR" >

			<menu id="m1_1" label="Niveau 1.1">

				<menupopup>
					<menuitem label="Printer"/>
					<menuitem label="Mouse"/>
					<menuitem label="Keyboard"/>
				</menupopup>
			</menu>
		
		</menuitem>
	    <menuitem id="help-index" label="SS" />
	    <menuitem id="help-about" label="TT"/>

	  </menupopup>
	</menu>

	</box>
</window>';
?>