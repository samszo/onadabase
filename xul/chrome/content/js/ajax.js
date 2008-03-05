//--------------------------------------------
// AJAX Functions
//--------------------------------------------
//local
//var urlExeAjax = "http://localhost/onadabase/library/php/ExeAjax.php";
//naos
//var urlExeAjax = "http://www.naoss.fr:81/mundigo/library/php/ExeAjax.php";
//mundilogiweb
var urlExeAjax = "http://www.mundilogiweb.com/onadabase/library/php/ExeAjax.php";

function AppendResult(url,doc) {
  try {
	dump("AppendResult IN "+url+"\n");
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
		xulData="<box id='dataBox' flex='1'  " +
	          "xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul'>" +
	          response + "</box>";
		var parser=new DOMParser();
		var resultDoc=parser.parseFromString(xulData,"text/xml");
		//vide le conteneur
		while(doc.hasChildNodes())
			doc.removeChild(doc.firstChild);
		//ajoute le résultat
		doc.appendChild(resultDoc.documentElement);
	}
	dump("AppendResult OUT \n");
   } catch(ex2){alert(ex2);dump("::"+ex2);}
}

function GetResult(url) {
  try {
	dump("GetResult IN "+url+"\n");
    response = "";
	p = new XMLHttpRequest();
	p.onload = null;
	p.open("GET", urlExeAjax+"?f=GetCurl&url="+url, false);
	p.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	p.send(null);

	if (p.status != "200" ){
	      alert("Réception erreur " + p.status);
	}else{
	    response = p.responseText;
	}
	return response;
	dump("GetResult OUT \n");
   } catch(ex2){alert(ex2);dump("::"+ex2);}
}

function AfficheResult(response,params) {
	dump("AfficheResult IN response"+response+" "+params+"\n");
	document.getElementById(params).value = response;
}

function RefreshResult(response, params) {
   	//alert(url);
	arrP = params.split(",");
	document.getElementById(arrP[0]).value = response;
	AjaxRequest(arrP[1],"AfficheResult",arrP[2])
}

function AjaxRequest(url,fonction_sortie,params) {

 	this.url = encodeURI(url);
 	this.fonction_sortie = fonction_sortie;
 	this.params = params;
	dump("AjaxRequest IN "+url+" "+params+"\n");
	//alert(params);

	var ajaxRequest = this;

    if (window.XMLHttpRequest) {

	    this.req = new XMLHttpRequest();										// XMLHttpRequest natif (Gecko, Safari, Opera, IE7)

		try {
	    	netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");		// Mozilla Security
	   	} catch (e) {}

		this.req.onreadystatechange = function () { processReqChange(); }

		this.req.open("GET", this.url, true);
		this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
        this.req.send(null);

		try {
	    	//console.log("request: "+url);
	   	} catch (e) {}

	} else if (window.ActiveXObject) {

	    this.req = new ActiveXObject("Microsoft.XMLHTTP");						 // IE/Windows ActiveX

        if (this.req) {
            this.req.onreadystatechange = this.req.onreadystatechange = function () { processReqChange(); }
            this.req.open("GET", this.url, true);
			this.req.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT");
            this.req.send();
		}

    } else {

		alert("Votre navigateur ne connait pas l'objet XMLHttpRequest.");

	}

}

function processReqChange() {

	try {
	   	////console.log("state:"+this.req.readyState);
	} catch (e) {}

	if (this.req.readyState == 4) {				// quand le fichier est chargé

		if (this.req.status == 200) {			// detécter problèmes de format

			try {
    			netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");
   			} catch (e) {}

			try {
	   			////console.log(this.req.responseText);
			} catch (e) {}

			//eval(this.fonction_sortie+"(this.req.responseXML.documentElement)");
			eval(this.fonction_sortie+"(this.req.responseText,'"+this.params+"')");

		} else {

			alert("Il y avait un probleme avec le XML: " + this.req.statusText);

		}
	}
}