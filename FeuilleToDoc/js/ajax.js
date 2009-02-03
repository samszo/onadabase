//--------------------------------------------
// AJAX Functions
//--------------------------------------------

function GetXmlUrlToDoc(url){ //chargement simplifié
	try {
		var xmlDoc;
		// code for IE
		if (window.ActiveXObject)
			xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
		// code for Mozilla, Firefox, Opera, etc.
		else if (document.implementation && document.implementation.createDocument)
			xmlDoc=document.implementation.createDocument("","",null);
		else
			alert('Your browser cannot handle this script');
		xmlDoc.async=false;
		xmlDoc.load(url);
		return(xmlDoc);
   } catch(ex2){alert("ajax:GetXmlUrlToDoc:"+ex2);}

}

/*
function GetResult(url) { //rendu inutile par le changement de GetXmlUrlToDoc()
	try { 
		response = "";
		var p;
		if (window.XMLHttpRequest)
			p = new XMLHttpRequest();
		else if (window.ActiveXObject)
			p = new ActiveXObject("Microsoft.XMLHTTP");
		else 
			return; 
		p.open('GET', url, false);
		p.send(null);
		return p.responseText;
   } catch(ex2){alert(ex2);}
}*/


function AppendResult(url,doc,ajoute) {
  try {
	dump("AppendResult IN "+url+"\n");
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", encodeURI(url), false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
	    //alert(response);
		xulData="<box id='dataBox' flex='1'  " +
	          "xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
	          response + "</box>";
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(xulData,"text/xml");
		if(!ajoute){
			//vide le conteneur
			while(doc.hasChildNodes())
				doc.removeChild(doc.firstChild);
		}
		//ajoute le résultat
		doc.appendChild(resultDoc.documentElement);
	}
	dump("AppendResult OUT \n");
   } catch(ex2){alert(ex2);dump("::"+ex2);}
}


function AfficheSvg(response,params) {
   	alert(params+response);
	document.getElementById(params).firstChild.data = response;
	document.getElementById('proc-trace').value = response;
}


function AfficheResult(response,params) {
   	alert(params);
	document.getElementById(params).value = response;
}

function RefreshResult(response, params) {
   	//alert(url);
	arrP = params.split(",");
	document.getElementById(arrP[0]).value = response;
	AjaxRequest(arrP[1],"AfficheResult",arrP[2])
}

/*
function AjaxRequest(url,fonction_sortie,params,id) {
   
 	this.url = encodeURI(url);
 	this.fonction_sortie = fonction_sortie;
 	this.params = params;
	this.id=id;
	//alert(params);
	alert(this.url);
	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		this.req.onreadystatechange = function () { processReqChange(); }
		this.req.open("GET", this.url,true);
		this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
        this.req.send(null);

		try {
	    	//console.log("request: "+url);
	   	} catch (e) {}

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("POST", this.url,false);
			this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
            this.req.send(this.urlparams);
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}

}*/

function AjaxRequest(url,fonction_sortie,params,id) {
	var ajaxRequest;
	if (window.XMLHttpRequest)
		ajaxRequest = new XMLHttpRequest();
	else if (window.ActiveXObject)
		ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
	else 
		return; 
	ajaxRequest.open('GET', url, true);
	ajaxRequest.onreadystatechange = function() 
		{
			if (ajaxRequest.readyState == 4 && ajaxRequest.status == 200) { 
				eval(fonction_sortie+"(ajaxRequest.responseText);");
			}
		}
	ajaxRequest.send(null);
}

function AjaxRequestPost(url,urlparams,fonction_sortie,params,id) {
  
 	this.url = encodeURI(url);
 	this.fonction_sortie = fonction_sortie;
 	this.urlparams =encodeURI(urlparams);
 	this.params = params;
	this.id=id;
	//alert(params);
 
	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		this.req.onreadystatechange = function () { processReqChange(); }
        
		this.req.open("POST", this.url,true);
		this.req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");  
        this.req.send(this.urlparams);

		try {
	    	//console.log("request: "+url);
	   	} catch (e) {}

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("POST", this.url,true);
			this.req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");  
            this.req.send(this.urlparams);
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}

}

function processReqChange() { 

	try {
	   	////console.log("state:"+this.req.readyState);
	} catch (e) {}

	if (this.req.readyState == 4) {		// quand le fichier est chargé
		

		if (this.req.status == 200) {			// detécter problèmes de format


			//eval(this.fonction_sortie+"(this.req.responseXML.documentElement)");
			eval(this.fonction_sortie+"(this.req.responseText)");
            
		} else {

			alert("Il y avait un probleme avec le XML: " + this.req.statusText);

		}
	}
}

function AppendResultPost(url,urlparams,doc,ajoute) {
  
  try {
  
	dump("AppendResultPost IN "+url+"\n");
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("POST",url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(urlparams);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
	    //alert(response);
		xulData="<box id='dataBox' flex='1'  " +
	          "xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
	          response + "</box>";
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(xulData,"text/xml");
		if(!ajoute){
			//vide le conteneur
			while(doc.hasChildNodes())
				doc.removeChild(doc.firstChild);
		}
		//ajoute le résultat
		doc.appendChild(resultDoc.documentElement);
		
	}
	dump("AppendResultPost OUT \n");
   } catch(ex2){alert(ex2);dump("::"+ex2);}
}
function Ajax(url,urlparams,params,i) {
  
 	this.url = encodeURI(url);
 	this.Arr= new Array;
 	this.urlparams =encodeURI(urlparams);
 	this.params = params;
	
	//alert(params);
 
	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		this.req.onreadystatechange = function () { processReqChange(); }
        
		this.req.open("POST", this.url,true);
		this.req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");  
        this.req.send(this.urlparams);

		try {
	    	//console.log("request: "+url);
	   	} catch (e) {}

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("POST", this.url,true);
			this.req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");  
            this.req.send(this.urlparams);
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}

}

function processReqChangeListe() {

	try {
	   	////console.log("state:"+this.req.readyState);
	} catch (e) {}

	if (this.req.readyState == 4) {		// quand le fichier est chargé
		

		if (this.req.status == 200) {			// detécter problèmes de format


			 Arr[i]="(this.req.responseText)"
			
			alert("helloo");
            
		} else {

			alert("Il y avait un probleme avec le XML: " + this.req.statusText);

		}
	}
}
