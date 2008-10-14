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

function ChangeBase(site){
		var url = "exit.php?site="+site;
		location.href = url;
}

function GetValueChecked(doc){
	var ValChecked = "";
	var url=""; 
	for (var i = 0; i < doc.childNodes.length; i++){
		if(doc.childNodes[i].getAttribute("checked")=="true"){
			ValChecked += " - "+doc.childNodes[i].getAttribute("label"); 
			url += "&"+doc.childNodes[i].id+"="+doc.childNodes[i].value;
		}
	}
	return new Array(ValChecked,url);
}

function SetChoixDiagnostic() {
	try {
		var url = urlExeAjax+"?f=SetSessionValues"
		var libChoix = "Diagnostique : ";
		var arrRep;
		
		//choix du site
		var doc = document.getElementById("mnuSite");
		arrRep = GetValueChecked(doc,url);
		libChoix += arrRep[0]; 
		url+=arrRep[1];
		
		//choix des versions
		doc = document.getElementById("mnuVersion");
		arrRep = GetValueChecked(doc,url);
		libChoix += arrRep[0]; 
		url+=arrRep[1];
		//choix des types de critères
		doc = document.getElementById("mnuTypeCrit");
		arrRep = GetValueChecked(doc,url);
		libChoix += arrRep[0]; 
		url+=arrRep[1];
		
		//choix des types de critères
		doc = document.getElementById("mnuContReg");
		arrRep = GetValueChecked(doc,url);
		libChoix += arrRep[0]; 
		url+=arrRep[1];
		
		doc = document.getElementById("ChoixDiagnostic");
		doc.value = libChoix;
		
		//met à jour les valeurs de session
		AppendResult(url,doc);
		

  	} catch(ex2){alert("interface:SetChoixDiagnostic:"+ex2);}
}

function CopyRub(idDst) {
	try {
		var verif = true;
		var doc = document.getElementById("FormSaisi");
		
		if(idDst=="?" || idDst=="-1"){
			verif = false;
		}
				
		//purge les formulaires déjà affiché
		while(doc.hasChildNodes())
			doc.removeChild(doc.firstChild);
	
		if(verif){
			var url = urlExeAjax+"?f=CopyRub&idDst="+idDst;
			//dump("SetNewGrille "+url+"\n");
			AppendResult(url,doc);
			alert("Copie terminée");
		}

  	} catch(ex2){alert("CopyRub::"+ex2+" "+type);;}
}

function OuvreLienAdmin(idRub){
	window.open(lienAdminSpip+"/?exec=naviguer&id_rubrique="+idRub);
}

function OuvreArticle(idArt){
	window.open(lienAdminSpip+"/?exec=articles&id_article="+idArt);
}

function OuvreControle(idDonnee){
	window.open(lienAdminSpip+"/?exec=donnees_edit&id_donnee="+idDonnee);
}


function OuvreDonnee(idForm,idDon){
	window.open(lienAdminSpip+"/?exec=donnees_edit&id_form="+idForm+"&id_donnee="+idDon);
}

function DelArticle(idDonnee, idSrc) {
	try {
		var doc = document.getElementById("FormSaisi");
		var url = urlExeAjax+"?f=ClearArticle&idDonnee="+idDonnee+"&idRub="+idSrc;
		AppendResult(url,doc);
	} catch(ex2){
		alert("DelArticle::"+ex2+" " +"url="+url);
	}
}

function DelArticleObs(idDonnee, idSrc) {
	try {
		var doc = document.getElementById("FormSaisi");
		var url = urlExeAjax+"?f=ClearArticleObs&idDonnee="+idDonnee+"&idRub="+idSrc;
		AppendResult(url,doc);
	} catch(ex2){
		alert("DelArticleObs::"+ex2+" " +"url="+url);
	}
}

function DelRubrique(idRub, idSrc) {
	try {
		var answer = confirm("Voulez vous réellement supprimer toutes les fiches signalement de problème de cette rubrique?")
		if (answer){
			var doc = document.getElementById("FormSaisi");
			var url = urlExeAjax+"?f=ClearRubrique&&idRub="+idRub+"&idRubParent="+idSrc;
			AppendResult(url,doc);
		}
				
	} catch(ex2){
		alert("DelRubrique::"+ex2+" " +"url="+url);
	}
}

function DelRubriqueObs(idRub, idSrc) {
	try {
		var answer = confirm("Voulez vous réellement supprimer toutes les fiches observations de cette rubrique?")
		if (answer){
			var doc = document.getElementById("FormSaisi");
			var url = urlExeAjax+"?f=ClearRubriqueObs&&idRub="+idRub+"&idRubParent="+idSrc;
			AppendResult(url,doc);
		}
				
	} catch(ex2){
		alert("DelRubriqueObs::"+ex2+" " +"url="+url);
	}
}

function DelRubriqueParent(idRub) {
	try {
		var answer = confirm("Voulez vous réellement supprimer toutes les fiches signalement de problème de cette rubrique?")
		if (answer){
			var doc = document.getElementById("FormSaisi");
			var url = urlExeAjax+"?f=ClearRubriqueParent&&idRubParent="+idRub;
			AppendResult(url,doc);
		}
				
	} catch(ex2){
		alert("DelRubriqueParent::"+ex2+" " +"url="+url);
	}
}

function DelRubriqueParentObs(idRub) {
	try {
		var answer = confirm("Voulez vous réellement supprimer toutes les fiches observations de cette rubrique?")
		if (answer){
			var doc = document.getElementById("FormSaisi");
			var url = urlExeAjax+"?f=ClearRubriqueParentObs&&idRubParent="+idRub;
			AppendResult(url,doc);
		}
				
	} catch(ex2){
		alert("DelRubriqueParentObs::"+ex2+" " +"url="+url);
	}
}

function SetLienAdmin(idRub){
	
	var lien = document.getElementById("LienAdmin");
	if(lien)
		document.getElementById("RefId").removeChild(lien);

	var lbl = document.createElement("label");
	lbl.setAttribute("id","LienAdmin");
	lbl.setAttribute("href",lienAdminSpip+"/?exec=naviguer&id_rubrique="+idRub);
	lbl.setAttribute("target","_new");
	lbl.setAttribute("class","text-link");
	lbl.setAttribute("value","Admin SPIP");
	document.getElementById("RefId").appendChild(lbl);
	
}

function GetXmlFicToDoc(fic){

	var xml = read(fic);
	//alert(xml);
	var parser = new DOMParser();
	//var serializer = new XMLSerializer();
	var doc = parser.parseFromString(xml, "text/xml");
	
	return doc;

}

function GetXmlUrlToDoc(url){

	//alert(url);
	var xml = GetResult(url);
	//alert(xml);
	var parser = new DOMParser();
	//var serializer = new XMLSerializer();
	var doc = parser.parseFromString(xml, "text/xml");
	
	return doc;

}

function CompareRubSrcDst() {
	try {
		var doc = document.getElementById("FormSaisi");
		var progressMeter = document.getElementById('progressMeter');	
		document.getElementById('progressMeter').style.visibility="visible";
		if (progressMeter.getAttribute("mode")=="determined") {
			progressMeter.setAttribute("mode", "undetermined");
		}
		
		var url = urlExeAjax+"?f=CompareSrcDst&idRub=80";
		AppendResult(url,doc);
		
		progressMeter.setAttribute("mode", "determined");
		progressMeter.setAttribute("value", "100");
		
		document.getElementById('progressMeter').style.visibility="hidden";
		progressMeter.setAttribute("value", "0");
		
	} catch(ex2){
		alert("interface:CompareRubSrcDst:"+ex2+" " +"url="+url);
	}
}

function SynchroTree(idRub) {
	try {
		
		var url="";
		var idTree = "treeCompareSrcDst";
		var tree = document.getElementById(idTree);

		SynchroInitTree(idTree);

		//construction des variables
		var cId = tree.treeBoxObject.columns[0];
		var cVal = tree.treeBoxObject.columns[1];
		var cType = tree.treeBoxObject.columns[2];
		var cAction = tree.treeBoxObject.columns[3];
		var cProgress = tree.treeBoxObject.columns[4];
	
		var i, id, val, type, action;

		var progressMeter = document.getElementById('progressMeter');	
		ProgressDeb(progressMeter);
		
		//for (i=0; i<tree.treeBoxObject.view.rowCount; i++)
		for (i=0; i<1; i++)
		{
			id = tree.treeBoxObject.view.getCellText(i,cId);
			val = tree.treeBoxObject.view.getCellText(i,cVal);
			type = tree.treeBoxObject.view.getCellText(i,cType);
			action = tree.treeBoxObject.view.getCellText(i,cAction);
			//récupère le progressmeter
			var idPm = idTree+DELIM+type+DELIM+id+DELIM+"pm";
			var pm = document.getElementById(idPm);
			pm.setAttribute("value", "10%");
			url = urlExeAjax+"?f=SynchroDstLoc&idRub="+idRub+"&id="+id+"&val="+val+"&type="+type+"&action="+action ;
			var r = GetResult(url);
			pm.setAttribute("value", "100%");
			if(r!=1)
				pm.setAttribute("style", "RedBar");
		}		

		ProgressEnd(progressMeter);

	} catch(ex2){
		alert("interface:SynchroTree:"+ex2+" " +"url="+url);
	}
}


function SynchroInitTree(idTree){

	try {

		var tree = document.getElementById(idTree);

		var cId = tree.treeBoxObject.columns[0];
		var cType = tree.treeBoxObject.columns[2];

		var i;
		
		for (i=0; i<tree.treeBoxObject.view.rowCount; i++)
		{
			id = tree.treeBoxObject.view.getCellText(i,cId);
			type = tree.treeBoxObject.view.getCellText(i,cType);
			//récupère le progressmeter
			var idPm = idTree+DELIM+type+DELIM+id+DELIM+"pm";
			var pm = document.getElementById(idPm);
			if(pm)
				pm.setAttribute("value", "0%");
		}		


	} catch(ex2){
		alert("interface:InitSynchroTree:"+ex2);
	}

}


function ProgressDeb(progressMeter){

	try {

		progressMeter.style.visibility="visible";
		progressMeter.setAttribute("hidden", "false");
		if (progressMeter.getAttribute("mode")=="determined") {
			progressMeter.setAttribute("mode", "undetermined");
		}

	} catch(ex2){
		alert("interface:ProgressDeb:"+ex2);
	}

}

function ProgressEnd(progressMeter){

	try {

		progressMeter.setAttribute("mode", "determined");
		progressMeter.setAttribute("value", "100");		
		progressMeter.style.visibility="hidden";
		progressMeter.setAttribute("hidden", "true");
		progressMeter.setAttribute("value", "0");

	} catch(ex2){
		alert("interface:ProgressEnd:"+ex2);
	}

}


function SynchroniserMajParam() {
	try {
		var doc = document.getElementById("FormSaisi");
		var progressMeter = document.getElementById('progressMeter');	
		document.getElementById('progressMeter').style.visibility="visible";
		if (progressMeter.getAttribute("mode")=="determined") {
			progressMeter.setAttribute("mode", "undetermined");
		}
		
		var url = urlExeAjax+"?f=ShowSynchro";
		AppendResult(url,doc);
		
		progressMeter.setAttribute("mode", "determined");
		progressMeter.setAttribute("value", "100");
		
		document.getElementById('progressMeter').style.visibility="hidden";
		progressMeter.setAttribute("value", "0");
		
	} catch(ex2){
		alert("SynchroniserMajParam::"+ex2+" " +"url="+url3);
	}
}

function SynchroniserExportImport() {
	try {
		var doc = document.getElementById("synctreeRub");
		var idAuteur = document.getElementById('idAuteur').value;
		var progressMeter = document.getElementById('progressMeter');
		
		document.getElementById('progressMeter').style.visibility="visible";
		
		if (progressMeter.getAttribute("mode")=="determined") {
			progressMeter.setAttribute("mode", "undetermined");
		}
		
		var url3 = urlExeAjax+"?f=Synchronise&idAuteur="+idAuteur;
		AppendResult(url3,doc);
		
		progressMeter.setAttribute("mode", "determined");
		progressMeter.setAttribute("value", "100");
		
		alert("Synchronisation terminée");
		document.getElementById('progressMeter').style.visibility="hidden";
		progressMeter.setAttribute("value", "0");
		
	} catch(ex2){
		alert("SynchroniserExportImport::"+ex2+" " +"url="+url3);
	}
}

function AddNewGrille(type){
  try {
	var verif = true;
	
	//récupère les paramètres
	var Xpath ="/Params/Param[@nom='AddObj"+type+"']";
	var iterator = xmlParam.evaluate(Xpath, xmlParam, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );
	var n = iterator.iterateNext();
	var id = n.attributes["id"].value;
	var messNoVerif = n.childNodes[1].textContent;
	var TitreFormSaisi =  n.childNodes[3].textContent;
	
	
	var dst = document.getElementById('idRub').value;
	var login = document.getElementById('login').value;
	
	if(dst=="?"){
		alert(messNoVerif);
		verif = false;
	}
	var doc = document.getElementById("FormSaisi");
	document.getElementById("TitreFormSaisi").value=TitreFormSaisi;
	//purge les formulaires déjà affiché
	while(doc.hasChildNodes())
		doc.removeChild(doc.firstChild);

	if(verif){
		var url = urlExeAjax+"?f=AddNewGrille&src="+id+"&dst="+dst+"&type="+type+"&login="+login;
		//dump("SetNewGrille "+url+"\n");
		AppendResult(url,doc);
	}

  } catch(ex2){alert("AddNewGrille::"+ex2+" "+type);;}
}

function AddNewRubrique(idDst) {
	
	try {
		var verif = true;
		
		//alert("AddNewRubrique IN "+"idDst "+idDst);
		//récupère les paramètres
		var Xpath ="/Params/Param[@nom='AddObjTerritoire']";
		//alert("AddNewRubrique IN "+"xpath "+Xpath);
		//alert("AddNewRubrique IN 3"+"xmlParam "+xmlParam);
		var iterator = xmlParam.evaluate(Xpath, xmlParam, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );
		
		//alert("AddNewRubrique IN 0 iterator "+iterator.numberValue+"\n");
		//alert("AddNewRubrique IN 0 iterator "+iterator.stringValue+"\n");
		var n = iterator.iterateNext();
		//alert("AddNewRubrique IN 1 n "+n+"\n");
		
		var id = n.attributes["id"].value;
		var messNoVerif = n.childNodes[1].textContent;
		var TitreFormSaisi =  n.childNodes[3].textContent;
		
		//alert("AddNewRubrique IN "+"id "+id+" idDst "+idDst+" n "+n+"\n");
		
		//var dst = document.getElementById('idRub').value;
		var login = document.getElementById('login').value;
		if(idDst=="?"){
			alert(messNoVerif);
			verif = false;
		}
		if(idDst=="-1"){
			alert(messNoVerif);
			verif = false;
		}
		
		var doc = document.getElementById('FormSaisi');//treeRub FormSaisi
		//purge les formulaires déjà affiché
		
		document.getElementById("TitreFormSaisi").value=TitreFormSaisi;
		while(doc.hasChildNodes())
			doc.removeChild(doc.firstChild);
		
		var idAuteur = document.getElementById('idAuteur').value;	
		if(verif){
			var url = urlExeAjax+"?f=NewRubrique&idRubSrc="+id+"&idRubDst="+idDst+"&idAuteur="+idAuteur;
			AppendResult(url, doc);
		}
		//alert("AddNewRubrique url "+url+"\n");
		
		//var doc = document.getElementById('treeRub');
		//ChargeTreeFromAjax('idRub','treeRub','Terre')
	
	} catch(ex2){alert(":AddNewRubrique:"+ex2+" url="+url);}
}

function AddObservation(idDoc,val){
  try {
	//alert(idDoc);
	var arrDoc = idDoc.split(DELIM);
	
	var f = "SetVal";
	var val;
			

	var login = document.getElementById('login').value;
	
	var url = urlExeAjax+"?f="+f+"&idGrille="+arrDoc[1]+"&idDon="+arrDoc[2]+"&champ=mot_1&val="+val+"&login="+login;
	url +="&ppp=2";
	window.open(url,'_blank','width=650,height=300,resizable=no,left=200,top=200');
		
  } catch(ex2){alert("interface:AddObservation:"+ex2);}
}

function SetVal(idDoc){
  try {
	var verif = true;
	//alert(idDoc);
	var doc = document.getElementById(idDoc);
	var arrDoc = doc.id.split(DELIM);
	
	//alert(doc.tagName);	
	var f = "SetVal";
	var val;
	switch (doc.tagName)
	{
		case "radiogroup":
			val = doc.selectedItem.id;
			break;
		case "checkbox":
			val = arrDoc[5];
			if(!doc.checked)
				f = "DelVal";				
			break;
		default:
			val = doc.value;
			break;
	}
	
			
	//alert(doc.tagName+' '+val);	
	dump("SetVal "+arrDoc[0]+", "+arrDoc[1]+", "+arrDoc[2]+"\n");
	
	if(!verif)
		return;
	var login = document.getElementById('login').value;
	
	var url = urlExeAjax+"?f="+f+"&idGrille="+arrDoc[1]+"&idDon="+arrDoc[2]+"&champ="+arrDoc[3]+"&val="+val+"&login="+login;
	
	//dump("SetNewGrille "+url+"\n");
	//alert("SetVal 0 "+arrDoc[0]+", 1 "+arrDoc[1]+", 2 "+arrDoc[2]+", 3 "+ arrDoc[3]+", 4 "+arrDoc[4]+", val "+val);
	//récupère le formulaire de signalisation d'un problème dans le cas d'un diagnostic
	if(arrDoc[1]=="59" || arrDoc[3]=="Modif" || arrDoc[3]=="Sup") {
		//var reponse = AppendResult(url,doc.parentNode,true);
		
		if (arrDoc[3]=="mot_1" && val==2 || arrDoc[3]=="Modif" ) {
			//var reponse = GetXmlFicToDoc(url);
			//ajout de l'argument du popup
			window.open(url+"&ppp=1",'_blank','width=650,height=400,resizable=no,left=200,top=200');
			//récupère la ligne des question intermédiaires
			var docQi = document.getElementById("row_"+arrDoc[1]+"_"+arrDoc[2]+"_qi");
			AppendResult(url,docQi,false,"vbox");
		} else {
			//récupère la ligne des question intermédiaires
			var docQi = document.getElementById("row_"+arrDoc[1]+"_"+arrDoc[2]+"_qi");
			AppendResult(url,docQi,false,"vbox");
			//InsertBeforeResult(url,doc.parentNode);
		}
		
	}else
		AjaxRequest(url,"AfficheResult","trace"+doc.id);
	
	//modifie le titre du panel dans le cas du titre de l'établissement
	if(arrDoc[1]=="55")
		if(arrDoc[3]=="ligne_1")
			document.getElementById("tab"+arrDoc[4]).label=val;
		
  } catch(ex2){alert("SetVal::"+ex2);dump("::"+ex2);}
}

function SetNewGrille(kml, src, dst, doc){
  try {
	var verif = true;
	var Lkml = document.getElementById('lib'+kml).value;
	var Lsrc = document.getElementById('lib'+src).value;
	var Ldst = document.getElementById('lib'+dst).value;
	var kml = document.getElementById('id'+kml).value;
	var src = document.getElementById('id'+src).value;
	var dst = document.getElementById('id'+dst).value;
	var login = document.getElementById('login').value;
	
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
	
	var url = urlExeAjax+"?f=AddGrilles&src="+src+"&dst="+dst+"&login="+login;
	//dump("SetNewGrille "+url+"\n");
	AjaxRequest(url,"AfficheResult","btnTrace");
	//AppendResult(url,doc);
	
	
	if(kml!="?"){
		//création de la grille géolocalisation
		url = urlExeAjax+"?f=AddPlacemark&dst="+dst+"&kml="+kml;
		dump("création de la grille géolocalisation\n"+Lkml+"\n");
		AjaxRequest(url,"AfficheResult","btnTrace");
	}

	var cells = new Array(dst,Ldst,Lsrc,Lkml)
	//cells = new Array("fic"+numFic,"Gare Lille Flandre à Rue Négrier, 59800 Lille.kml",'Fichier')
	Tree_AddItem(doc, cells);
		
	//src.value="";

  } catch(ex2){dump(":SetNewGrille:"+ex2);}
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

function RefreshEcran(id,titre,typeSrc,typeDst)
{
  try {	
	document.getElementById('idRub').value=id;
	
	//récupération des objets  du serveur
	if(typeSrc!="aucun"){
		ChargeTreeFromAjax('idRub','treeRub',typeSrc);
		ChargeFilArianeFromAjax(id,'tbFilAriane',titre,typeSrc,typeDst);
	}
	ChargeTabboxFromAjax('idRub','FormSaisi',typeDst);
	
	//gestion du menu contextuel du tree
	var cont = document.getElementById('treeRub');
	if(typeSrc!="aucun")
		cont.setAttribute("context","pop"+typeSrc);
	else
		cont.setAttribute("context","pop"+typeDst);
	
	
	//vérifie la présence su fil d'ariane
	if(typeSrc!="aucun"){
		var tb=document.getElementById("nav-toolbar");
		var tbb=document.getElementById("tbb"+typeSrc);
		if(!tbb){
			//ajoute un fil ariane sous forme de bouton
			/*
			tbb = document.createElement("toolbarbutton");
			tbb.setAttribute("id","tbb"+typeSrc);
			tbb.setAttribute("label",titre);
			tbb.setAttribute("class","toolbarbutton");
			*/
			//ajoute un fil ariane sous forme de label
			tbb = document.createElement("label");
			tbb.setAttribute("id","tbb"+typeSrc);
			tbb.setAttribute("value",titre);
			tbb.setAttribute("class","text-link");		
			tbb.setAttribute("onclick","RefreshEcran("+id+",'"+titre+"','"+typeSrc+"','"+typeDst+"');");
			tb.appendChild(tbb);
		}else{
			//récupère la place du tbb
			j = -1;		 
			for (var i = 0; i < tb.childNodes.length; i++) {
				if(tb.childNodes[i].id=="tbb"+typeSrc)
					j = i+1;		 
			}
			if(j!=-1){
				//supprime les enfants après le tbb
				nb = tb.childNodes.length
				for (var i = j; i < nb; i++) {
					tb.removeChild(tb.childNodes[j]);
				}
			}
		}
		//met à jour le titre tree
		document.getElementById("titreRub").value = "Sélectionner un(e) des "+titre;
		
	}
	//alert("RefreshEcran OUT\n");
   
   } catch(ex2){alert(":RefreshEcran:"+ex2+"");dump("::"+ex2);}
	
}

function ChargeFilArianeFromAjax(idSrc, idDst, titre,typeSrc,typeDst)
{
  try {
	//alert("ChargeFilArianeFromAjax IN "+idSrc+", "+idDst+"\n");
	
	var doc = document.getElementById(idDst);

	var url = urlExeAjax+"?f=GetFilAriane&id="+idSrc+"&titre="+titre+"&typeSrc="+typeSrc+"&typeDst="+typeDst;
	AppendResult(url,doc);
	
	//dump("ChargeFilArianeFromAjax OUT\n");
   
   } catch(ex2){alert(":ChargeFilArianeFromAjax:"+ex2+" url="+url);}
	
}

function ChargeTreeProb(idSrc,idDst)
{
  try {
	//alert("ChargeTreeFromAjax IN "+type+"\n");

	var id = document.getElementById(idSrc).value;
	var doc = document.getElementById(idDst);
	//pour ne charger qu'une fois le tree
	//if(document.getElementById('tree'+type))
	//	return


	var url = urlExeAjax+"?f=GetTreeProb&id="+id;
	//alert("ChargeTreeFromAjax url "+url+"\n");
	//AjaxRequest(url,'AppendTreeChildren',parentitem)
	AppendResult(url,doc);
	
	dump("ChargeTreeProb OUT\n");
   
   } catch(ex2){alert(":ChargeTreeProb:"+ex2+" url="+url);}
	
}

function ChargeTreeObs(idSrc,idDst)
{
  try {
	//alert("ChargeTreeFromAjax IN "+type+"\n");

	var id = document.getElementById(idSrc).value;
	var doc = document.getElementById(idDst);
	//pour ne charger qu'une fois le tree
	//if(document.getElementById('tree'+type))
	//	return


	var url = urlExeAjax+"?f=GetTreeObs&id="+id;
	//alert("ChargeTreeFromAjax url "+url+"\n");
	//AjaxRequest(url,'AppendTreeChildren',parentitem)
	AppendResult(url,doc);
	
	dump("ChargeTreeObs OUT\n");
   
   } catch(ex2){alert(":ChargeTreeObs:"+ex2+" url="+url);}
	
}

function ChargeTreeCsv(idSrc,idDst)
{
  try {
	//alert("ChargeTreeFromAjax IN "+type+"\n");

	var id = document.getElementById(idSrc).value;
	var doc = document.getElementById(idDst);
	//pour ne charger qu'une fois le tree
	//if(document.getElementById('tree'+type))
	//	return


	var url = urlExeAjax+"?f=GetTreeCsv&id="+id;
	//alert("ChargeTreeFromAjax url "+url+"\n");
	//AjaxRequest(url,'AppendTreeChildren',parentitem)
	window.open(url);
	
	dump("ChargeTreeCsv OUT\n");
   
   } catch(ex2){alert(":ChargeTreeCsv:"+ex2+" url="+url);}
	
}

function ChargeTreeFromAjax(idSrc,idDst,type)
{
  try {
	//alert("ChargeTreeFromAjax IN "+type+"\n");

	var id = document.getElementById(idSrc).value;
	var doc = document.getElementById(idDst);
	//pour ne charger qu'une fois le tree
	//if(document.getElementById('tree'+type))
	//	return


	var url = urlExeAjax+"?f=GetTree&ParamNom=GetOntoTree&type="+type+"&id="+id;
	//alert("ChargeTreeFromAjax url "+url+"\n");
	//AjaxRequest(url,'AppendTreeChildren',parentitem)
	AppendResult(url,doc);
	
	dump("ChargeTreeFromAjax OUT\n");
   
   } catch(ex2){alert(":ChargeTreeFromAjax:"+ex2+" url="+url);dump("::"+ex2);}
	
}

function ChargeTabboxFromAjax(idSrc,idDst,type)
{
  try {
	dump("ChargeTabboxFromAjax IN "+type+"\n");
	
	//ajoute le lien vers spip admin
	//SetLienAdmin(document.getElementById("idRub").value);	

	var doc = document.getElementById(idDst);
	if(document.getElementById(idSrc))
		var id = document.getElementById(idSrc).value;
	else
		var id = idSrc;

	//gestion de menu contextuel du formulaire
	//if(document.getElementById('dataBox').childNodes.length>0){
		//construction dynamique du menu
		ChargeMenuFromAjax(id,idDst,type);
		//attribution du menu
		var fs = document.getElementById(idDst);
		fs.setAttribute("context","pop"+type);
	//}
		
	while(doc.hasChildNodes())
		doc.removeChild(doc.firstChild);
  
	var url = urlExeAjax+"?f=GetTabForm&ParamNom=GetTabForm&id="+id+"&type="+type;
	AppendResult(url,doc);
	//AjaxRequest(url,'AppendTreeChildren',item)
	
	dump("ChargeTabboxFromAjax OUT\n");
   
   } catch(ex2){dump(":ChargeTabboxFromAjax:"+ex2);}
	
}

function ChargeMenuFromAjax(id,idDst,type)
{
  try {
	dump("ChargeMenuFromAjax IN "+type+"\n");
	
	var doc = document.getElementById("menu_"+type+"_voir");
	if(!doc)
		return;
	
	while(doc.hasChildNodes())
		doc.removeChild(doc.firstChild);
  
	var url = urlExeAjax+"?f=GetMenuPopUp&id="+id+"&type="+type;
	//AppendResult(url,doc);
	var menu = GetAjaxResult(url);
	xulData="<menupopup id='popup_"+type+"_voir' " +
          " xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul' >" +
          menu + "</menupopup>";
	var parser=new DOMParser();
	var resultDoc=parser.parseFromString(xulData,"text/xml");
	//ajoute le résultat
	doc.appendChild(resultDoc.documentElement);
	
	
	//AjaxRequest(url,'AppendTreeChildren',item)
	
	dump("ChargeMenuFromAjax OUT\n");
   
   } catch(ex2){dump(":ChargeMenuFromAjax:"+ex2);}
	
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

function GetFichierKml(doc)
{
	netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	
	var types = new Array("kml", "flv", "mpg", "mov");
	fichierCourant = GetFichier(types);
	//fichierCourant = document.getElementById("NomFichier").value;
	
	if(fichierCourant){
		//var extension = pathinfo(fichierCourant,PATHINFO_EXTENSION);
		
		var tabDecomp = fichierCourant.leafName.split('.');
		var extension = tabDecomp[tabDecomp.length-1];   
		
		var progressMeter = document.getElementById('progressMeter');
		
		document.getElementById('progressMeter').style.visibility="visible";
		
		if (progressMeter.getAttribute("mode")=="determined") {
			progressMeter.setAttribute("mode", "undetermined");
		}
		
		//alert(extension);
		if (extension == 'kml') document.getElementById(doc).value = fichierCourant.path;
		else {
			//alert(doc);
			var fichier2 = doc.replace('fichier_1', 'ligne_4');
			//alert(fichier2);
			document.getElementById(fichier2).value = fichierCourant.path;
		}
		//document.getElementById('wSaisiDiag').canAdvance=true;
		//ChargeTreeFromKml(fichierCourant,'TreeRoot');
		UploadFile(urlExeAjax+"?f=AddDocToArt&idDoc="+doc, fichierCourant);
		
		progressMeter.setAttribute("mode", "determined");
		progressMeter.setAttribute("value", "100");
		
		alert("Ajout du fichier terminé");
		document.getElementById('progressMeter').style.visibility="hidden";
		progressMeter.setAttribute("value", "0");
		
	}else
		document.getElementById(doc).value = "Aucun fichier n'est sélectionné !";
 
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

function GetFichier(types)
{
	
  try {
	
	netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	var nsIFilePicker = Components.interfaces.nsIFilePicker;
	var fp = Components.classes["@mozilla.org/filepicker;1"]
	        .createInstance(nsIFilePicker);
	var i = 0;
	while (i < types.length) {
		//if (types[i]=='mpeg') fp.appendFilter("Fichiers "+types[i],"*."+types[i]+"; *.mpg; *.mpe");
		//else 
		fp.appendFilter("Fichiers "+types[i],"*."+types[i]);
		i++;
	}
	fp.appendFilters(nsIFilePicker.filterImages);
	//fp.appendFilters(nsIFilePicker.filterAll);
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