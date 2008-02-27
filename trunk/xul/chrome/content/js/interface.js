//position la sécurité
//netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
//netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
//netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect UniversalBrowserAccess');
/*
 try  {
  //netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
  netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
  
 } catch (e) {
  alert("Permission refusée de lire le fichier (" + e + ")");
 }
*/

var fichierCourant;
var numFic = 0;
var DELIM = "*";

//initFocus();

function initFocus() {
  addEventListener("focus",setFocusedElement,true);
}

function setFocusedElement() {
  var focused = document.commandDispatcher.focusedElement;
  alert(focused.tagName);
}

function AddEtablissement(){
  try {
	var verif = true;
	
	dst = document.getElementById('idrub').value;
	if(dst=="?"){
		alert("Veillez choisir un territoire");
		verif = false;
	}
	doc = document.getElementById("FormSaisi");
	document.getElementById("TitreFormSaisi").value="Créer un nouvel établissement";
	//purge les formulaires déjà affiché
	while(doc.hasChildNodes())
		doc.removeChild(doc.firstChild);

	if(verif)
		AddNewGrille(45, dst, doc);

  } catch(ex2){alert("AddEtablissement::"+ex2);dump("::"+ex2);}
}

function AddNewGrille(src, dst, doc){
  try {

	dump("AddNewGrille("+src+", "+dst+"\n");
	
	url = urlExeAjax+"?f=AddEtablissement&src="+src+"&dst="+dst;
	//dump("SetNewGrille "+url+"\n");
	AppendResult(url,doc);

  } catch(ex2){dump("::"+ex2);}
}


function SetVal(doc){
  try {
	var verif = true;
	arrDoc = doc.id.split(DELIM);
	
	//gestion des type de control
	if(doc.tagName=="radiogroup")
		val = doc.selectedItem.id;
	else
		val = doc.value;
	//alert(doc.tagName+' '+val);	
	dump("SetVal "+arrDoc[0]+", "+arrDoc[1]+", "+arrDoc[2]+"\n");
	
	if(!verif)
		return;
	
	url = urlExeAjax+"?f=SetVal&idDon="+arrDoc[1]+"&champ="+arrDoc[2]+"&val="+val;
	//dump("SetNewGrille "+url+"\n");
	AjaxRequest(url,"AfficheResult","trace"+doc.id);

  } catch(ex2){alert("SetVal::"+ex2);dump("::"+ex2);}
}

function SetNewGrille(kml, src, dst, doc){
  try {
	var verif = true;
	Lkml = document.getElementById('lib'+kml).value;
	Lsrc = document.getElementById('lib'+src).value;
	Ldst = document.getElementById('lib'+dst).value;
	kml = document.getElementById('id'+kml).value;
	src = document.getElementById('id'+src).value;
	dst = document.getElementById('id'+dst).value;
	
	dump("SetNewGrille("+kml+", "+src+", "+dst+"\n");
	if(src=="?"){
		alert("Veillez choisir un formulaire");
		verif = false;
	}
	if(dst=="?"){
		alert("Veillez choisir une rubrique");
		verif = false;
	}
	
	if(!verif)
		return;
	
	url = urlExeAjax+"?f=AddGrilles&src="+src+"&dst="+dst;
	//dump("SetNewGrille "+url+"\n");
	AjaxRequest(url,"AfficheResult","btnTrace");
	//AppendResult(url,doc);
	
	
	if(kml!="?"){
		//création de la grille géolocalisation
		url = urlExeAjax+"?f=AddPlacemark&dst="+dst+"&kml="+kml;
		dump("création de la grille géolocalisation\n"+Lkml+"\n");
		AjaxRequest(url,"AfficheResult","btnTrace");
	}

	cells = new Array(dst,Ldst,Lsrc,Lkml)
	//cells = new Array("fic"+numFic,"Gare Lille Flandre à Rue Négrier, 59800 Lille.kml",'Fichier')
	Tree_AddItem(doc, cells);
		
	//src.value="";

  } catch(ex2){dump("::"+ex2);}
}

function ChargeBrower(id,url)
{
	/* bug sur le chargement de l'overlay tree.php : les valeurs du rdf ne sont pas chargée
	on charge un iframe avec les paramètres de page
	*/
	document.loadOverlay(url,null);
	
	//var Brower = document.getElementById(id);
	//alert(url);
	//pour un iframe
	//Brower.setAttribute("src",url);
	//pour un brower
	//Brower.loadURI(url, null, null);
	/*
	newChild = makeRequest('http://localhost/mundilogiweb/ieml/overlay/iframe.php');
	parent = document.getElementById("singlebox");
	while(parent.hasChildNodes())
	  parent.removeChild(parent.firstChild);
	parent.value=newChild;
	*/
	
}


function ChargeTreeFromAjax(type,parentitem)
{
  try {
	dump("ChargeTreeFromAjax IN "+type+"\n");

	doc = document.getElementById(parentitem);
	//pour ne charger qu'une fois le tree
	if(document.getElementById('tree'+type))
		return


	url = urlExeAjax+"?f=GetTree&ParamNom=GetOntoTree&type="+type;
	//AjaxRequest(url,'AppendTreeChildren',parentitem)
	AppendResult(url,doc);
	
	dump("ChargeTreeFromAjax OUT\n");
   
   } catch(ex2){alert(url);dump("::"+ex2);}
	
}

function ChargeTabboxFromAjax(idSrc,idDst,type)
{
  try {
	dump("ChargeTabboxFromAjax IN "+type+"\n");
	
	doc = document.getElementById(idDst);
	id = document.getElementById(idSrc).value;
	
	while(doc.hasChildNodes())
		doc.removeChild(doc.firstChild);
  
	url = urlExeAjax+"?f=GetTabForm&ParamNom=GetTabForm&id="+id+"&type="+type;
	AppendResult(url,doc);
	//AjaxRequest(url,'AppendTreeChildren',item)
	
	dump("ChargeTabboxFromAjax OUT\n");
   
   } catch(ex2){dump(":ChargeTabboxFromAjax:"+ex2);}
	
}

function ChargeTreeFromKml(file,parentitem)
{
  try {
	//dump("ChargeTreeFromKml IN "+file.path+"\n");
		
	//xml = read("C:\\Users\\samszo\\Documents\\La Muse ment\\kml\\Gare Lille Flandre à Rue Négrier, 59800 Lille.kml");
	//xml = read(file.path);
	xml = GetResult(file);
	//nettoie le xml pour le Xpath
	//document.getElementById('proc-trace').value = xml.indexOf("<Document>",0)+','+xml.length+"\n";
	xml = "<kml>"+xml.substr(xml.indexOf("<Document>",0),xml.length)
	xml = xml.replace('&amp;',' ');

	//document.getElementById('proc-trace').value += xml;

	//cells = new Array("fic"+numFic,file.leafName,xml,'Fichier')
	cells = new Array("fic"+numFic,"Gare Lille Flandre à Rue Négrier, 59800 Lille.kml",'Fichier')
	Tree_AddItem(parentitem, cells);
	
	var parser = new DOMParser();
	var serializer = new XMLSerializer();
	var doc = parser.parseFromString(xml, "text/xml");

	var roottag = doc.documentElement;
	if (roottag.tagName == "parserError"){
	  alert("Parsing Error!");
	}

	p = evaluateXPath(doc, "//Placemark");
	dump("p "+p.value+"\n");

	
	//http://developer.mozilla.org/fr/docs/Introduction_%C3%A0_l'utilisation_de_XPath_avec_JavaScript
	var iterator = doc.evaluate('//Placemark', doc, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );

	try {
	  var thisNode = iterator.iterateNext();
	  var i = 0;
	  while (thisNode) {
		for (var i = 0; i < thisNode.childNodes.length; i++) {
			if(thisNode.childNodes[i].nodeName=="name")
				name = thisNode.childNodes[i].textContent;
		//dump("child name value = "+thisNode.childNodes[i].textContent+"\n");
		// faire quelque chose avec child
		//dump( "  child.nodeName : " + child.nodeName+"\n");
			 
		}
		cells = new Array(i,name,serializer.serializeToString(thisNode),thisNode.nodeName)
		//dump( "  thisNode xml : " + serializer.serializeToString(thisNode)+"\n");
		 
		Tree_AddItem("treeitemfic"+numFic, cells);
	    thisNode = iterator.iterateNext();
		i++;
	  }	
	}
	catch (e) {
	  dump( "Erreur : L'arbre du document a été modifié pendant l'itération " + e );
	}
		
	dump("ChargeTreeFromKml OUT\n");
   
   } catch(ex2){alert("::"+ex2);dump("::"+ex2);}
	
}

function evaluateXPath(aNode, aExpr) {
  try {
  var xpe = new XPathEvaluator();
  var nsResolver = xpe.createNSResolver(aNode.ownerDocument == null ?
    aNode.documentElement : aNode.ownerDocument.documentElement);
  var result = xpe.evaluate(aExpr, aNode, nsResolver, 0, null);
  var found = [];
  var res;
  while (res = result.iterateNext())
    found.push(res);
  return found;
   } catch(ex2){
	dump("::"+ex2);
    found.push('');
	return found;
	}

}

function GetFichierKml()
{
	numFic ++;
	//fichierCourant = GetFichier("kml");
	fichierCourant = document.getElementById("NomFichier").value;
	
	if(fichierCourant){
		document.getElementById("NomFichier").value = fichierCourant;
		document.getElementById('wSaisiDiag').canAdvance=true;
		ChargeTreeFromKml(fichierCourant,'TreeRoot');
	}else
		document.getElementById("NomFichier").value = "Impossible de continuer si aucun fichier n'est sélectionné !";
 
}


function lecture(url) {
  try {
  //url : adresse chrome:// ou http://, absolue ou relative
   var baseurl= window.location.toString();
   var uri = Components.classes['@mozilla.org/network/standard-url;1']
            .createInstance(Components.interfaces.nsIURI);
   uri.spec=baseurl;
   //Résolution de l'URL du fichier par rapport à l'URL de la fenêtre de l'application
   var strUrlFichier = uri.resolve(url);
   uri.spec=strUrlFichier;
   //Ouvrir un canal correspondant à cette URL
   var ios = Components.classes['@mozilla.org/network/io-service;1']
            .getService(Components.interfaces.nsIIOService);
   var chann = ios.newChannelFromURI ( uri );
   //Charger le document depuis ce canal
   var domsrv = Components.classes['@mozilla.org/content/syncload-dom-service;1']
               .getService(Components.interfaces.nsISyncLoadDOMService);
   var doc = domsrv.loadDocumentAsXML( chann , uri);
   //doc est un objet XMLDocument
   return doc;
  } catch(ex2){ alert("lecture::"+ex2); }
}


function GetFichier(type)
{
	
  try {
	
	var nsIFilePicker = Components.interfaces.nsIFilePicker;
	var fp = Components.classes["@mozilla.org/filepicker;1"]
	        .createInstance(nsIFilePicker);
	fp.appendFilter("Fichiers "+type,"*."+type);
	fp.init(window, "Sélectionnez un fichier", nsIFilePicker.modeOpen);
	
/*	var aLocalFile = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
	aLocalFile.initWithPath('C:/Program Files/life explorer');
	fp.displayDirectory = aLocalFile;*/
	
	var res = fp.show();
	if (res == nsIFilePicker.returnOK){
	  // --- faire quelque chose avec le fichier ici ---
		
		//alert("chemin : " + fp.file.path );
		//var pathcode = encodeURI(fp.file.path);
		
		//ChargeTreeFromRdf(fp.file);
		  
		return fp.file;
	}else{
		return false;
	}
  } catch(ex2){ alert("GetFichier::"+ex2); }

}
	
function read(filepath) {
  try {
	//http://xulfr.org/wiki/RessourcesLibs/LectureFichierCodeAvecCommentaires
	netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	  
	//Le fichier est ouvert
	 var file =  Components.classes["@mozilla.org/file/local;1"]
	            .createInstance(Components.interfaces.nsILocalFile);
	 file.initWithPath(filepath);
	 if ( file.exists() != true) {
	  alert("Le fichier "+filepath+" n'existe pas");
	  return ;
	 }

	 //Mode de lecture du fichier, un flux est nécessaire
	 //Le second argument définit les différents modes de lecture parmis
	 //PR_RDONLY     =0x01 lecture seulement
	 //PR_WRONLY     =0x02 écriture seulement
	 //PR_RDWR       =0x04 lecture ou écriture
	 //PR_CREATE_FILE=0x08 si le fichier n'existe pas, il est créé (sinon, sans effet)
	 //PR_APPEND     =0x10 le fichier est positionné à la fin avant chaque écriture
	 //PR_TRUNCATE   =0x20 si le fichier existe, sa taille est réduite à zéro
	 //PR_SYNC       =0x40 chaque écriture attend que les données ou l'état du fichier soit mis à jour
	 //PR_EXCL       =0x80 idem que PR_CREATE_FILE, sauf que si le fichier existe, NULL est retournée
	 //Le troisième argument définit les droits

	 var inputStream = Components.classes["@mozilla.org/network/file-input-stream;1"]
	         .createInstance( Components.interfaces.nsIFileInputStream );
	 inputStream.init(file, 0x01, 00004, null);
	 var sis = Components.classes["@mozilla.org/binaryinputstream;1"]
	          .createInstance(Components.interfaces.nsIBinaryInputStream);

	 sis.setInputStream( inputStream );
	 var output = sis.readBytes( sis.available() );
	 return output;
 
  } catch(ex2){ alert("read::"+ex2); }
 
 }