<?php
	require_once ("param/ParamPage.php");

    header('Content-type: application/vnd.mozilla.xul+xml');
?>
<?xml version="1.0" encoding="ISO-8859-1" ?>
<?xul-overlay href="overlay/menu.php?box=box1"?>
<window id="ieml-global" title="IEML-10eF v0.1 - information economy meta language - Dixième Famille" xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul" xmlns:html="http://www.w3.org/1999/xhtml">
	<script language="JavaScript" type="application/x-javascript" src="js/Interface.js"/>
	<script language="JavaScript" type="application/x-javascript" src="js/processus.js"/>
	<script language="JavaScript" type="application/x-javascript" src="js/ajax.js"/>


	<vbox >
		<hbox id="box2" >
			<vbox >
				<hbox>
					<label value="Processus en cours"/>
					<label id="proc-id" hidden="true"/>
				</hbox>
				<hbox>
					<label value="code :"/>
					<textbox id="proc-code" style="width:600px;" value=""/>
				</hbox>
				<hbox>
					<label value="descriptif : "/>
					<textbox id="proc-desc" style="width:600px;" value=""/>
				</hbox>
				<hbox>
					<label value="traduction : "/>
					<textbox id="proc-trad" style="width:600px;" value=""/>
				</hbox>
				<hbox>
					<button label="Modifier" oncommand="SetProc();"/>				
					<button label="Valider" oncommand="ValProc();"/>				
				</hbox>
				<hbox>
					<label id="proc-message" />	
				</hbox>
			</vbox>
		</hbox>
		<hbox >
			<label value="Explorer les processus"/>
			<vbox id="box1" >
			</vbox>
		</hbox>

	</vbox>

</window>

