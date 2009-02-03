function load(){
	google.load("visualization", "1");
}
// Query response handler function. 
function handleQueryResponse(response) {
	if (response.isError()) {
		alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
		return;
	}
	// construction de document html
	var data = response.getDataTable();
	console.log(data);
	var indice1=0; indice2=1;indice3=0;indice4=0;
	var html=new Array();
	html.push("<html><body>");
	for(var col = 7; col < data.getNumberOfColumns()-1; col+=2){
		//v�rifie que les colonnes sont remplies
		if(data.getFormattedValue(0, col)!=""){
			indice1=0; indice2=0;indice3=0;indice4=0;
			var titreProb = data.getValue(1,col);
			var numProb = data.getFormattedValue(0,col);
			html.push("<p style='mso-style-unhide:no;mso-style-qformat:yes;mso-style-parent:\"\";margin:0cm;margin-bottom:.0001pt;mso-pagination:widow-orphan;font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-fareast-font-family:\"Times New Roman\";mso-outline-level:4;mso-special-character:line-break;page-break-before:always'><b><span style='font-size:10.0pt;font-family:\"Arial\",\"sans-serif\"'>Probl&egrave;me "+numProb+": </span></b><span style='font-size:10.0pt;font-family:\"Arial\",\"sans-serif\";mso-bidi-font-weight:bold'>"+titreProb+"<b><o:p></o:p></b></span></p>"+newPB("&nbsp;"));
			html.push(newPB("Diagnostic des crit�res r�glementaires posant probl�mes :")+newPB("&nbsp;"));
			html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
			html.push("<tr>");
			html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Crit�re r�glementaire </td>" );
			html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Mesures et observations </td>" );
			html.push("<td colspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Solutions </td>");
			html.push("</tr>");
			html.push("<tr>");
			html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Pr�conisations </td>" );
			html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Estimations </td>" );
			html.push("</tr>");
			var nbrCrit=0;
			for (var row = 0; row < data.getNumberOfRows()-3; row++) {
				//v�rifie s'il faut prendre en compte le crit�re
				style="";
				var idCrit = data.getFormattedValue(row, 0);
				if(!VerifSupCrit(idCrit)){
					if(trace)
						console.log((row)+','+(col)+" "+data.getValue((row), col));
					
					rowspan=getSolutions(idCrit,true);
					
					if(escapeHtml(data.getValue(row, col))=="F"){
						html.push("<tr>");
						html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
						html.push(escapeHtml(data.getValue(row, 2))+" ");
						html.push("</td>");
						html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
						html.push(escapeHtml(data.getValue(row, col+1))+" ");
						html.push("</td>");
						//calcule la solution
						html.push(getSolutions(idCrit,false,rowspan));
						if(indice1 < data.getValue(row, 3))
							indice1= data.getValue(row, 3);
						if(indice2 < data.getValue(row, 4))
							indice2= data.getValue(row, 4);
						if(indice3 < data.getValue(row, 5))
							indice3= data.getValue(row, 5);
						if(indice4 < data.getValue(row, 6))
							indice4= data.getValue(row, 6);
						nbrCrit++;
					}
					
				}
			}
			
			html.push("</table>");
			if(nbrCrit<=5)
				style="text-align:center;font-family:Arial;font-size:10pt;background:#FF8C00";
			if(nbrCrit>5)
				style="text-align:center;font-family:Arial;font-size:10pt;background:#FF0000;color:#FFF;";
			if(nbrCrit>10)
				style="text-align:center;font-family:Arial;font-size:10pt;background:#000;color:#FFF;";    
			html.push(newPB("Diagnostic par type de d�ficience :")+newPB("&nbsp;"));
			html.push("<table cellspacing='10' border='2' width='6cm' height='2.5cm'  style='border-collapse:collapse' >");
			html.push("<tr>");
			html.push("<td>"); 
			html.push("<img src='"+urlImg+"logo_moteur.png'/>");
			html.push("</td>");
			html.push("<td>"); 
			html.push("<img src='"+urlImg+"logo_auditif.png'/>");
			html.push("</td>");
			html.push("<td>"); 
			html.push("<img src='"+urlImg+"logo_visuel.png'/>");
			html.push("</td>");
			html.push("<td>"); 
			html.push("<img src='"+urlImg+"logo_cognitif.png'/>");
			html.push("</td>");
			html.push("</tr>");
			html.push("<tr>");
			html.push("<td  style='"+style+"' >"+indice1+"</td>");
			html.push("<td  style='"+style+"'>"+indice2+"</td>");
			html.push("<td  style='"+style+"'>"+indice3+"</td>");
			html.push("<td  style='"+style+"'>"+indice4+"</td>");
			html.push("</tr>");
			html.push("</table>");
			html.push("</table>"+newPB("&nbsp;"));
			html.push(newPB("Pr�conisation(s) d'am�lioration r�pondant � la r�glementation :")+newPB("&nbsp;"));
			html.push(newP("Pour les pr�conisations usuelles, se reporter au tableau ci-dessus. Pour les pr�cisions, se reporter au descriptif ci-dessous. ")+newPB("&nbsp;"));
			html.push(newPB("Pr�conisation(s) optionnelle(s) d'am�nagement :")+newPB("&nbsp;"));
			html.push(newP("Pas de pr�conisation(s) optionnelle(s).")+newPB("&nbsp;"));
			html.push(newPB("Estimations des couts des pr�conisation(s) d'am�lioration r�pondant � la r�glementation :")+newPB("&nbsp;"));
			html.push(newPB("Estimations des couts des pr�conisation(s) optionnelle(s) d'am�lioration :")+newPB("&nbsp;"));
			html.push("<p style='font-family:Arial;font-size:8pt'>Note : les co�ts sont donn�s en prix HT (Hors Taxe) et en Euro. Ils sont donn�s en fonction des conditions de march� avec une approximation de 15 % et sans tenir compte des �ventuels probl�mes li�s � la structure de b�timent, aux co�ts de d�molitions ou d'�ventuelles �tudes compl�mentaires (�tude de portance...).</p>");
		}
		
	}
	html.push("</body></html>");
	//alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
	params="html="+ html.join('')+"&file="+cleanAccent(id[0])+".html";
	//AjaxRequestPost(urlAjax+"index/creatrepport",params,'ProgressIndicatorStop','',true);
	popup = null;
	popup = window.open("","Rapport");
	popup.document.write(html.join(''));
	popup.stop();
	html = null;
	
}		
// Query response handler function.
function handleQueryAllResponse(response) {
	if (response.isError()) {
		alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
		return;
	}
	// construction de document html
	var data = response.getDataTable();
	console.log(data);
	var indice1=0; indice2=1;indice3=0;indice4=0;
	var html=new Array();
	html.push("<html><body>");
	for(var col = 7; col < data.getNumberOfColumns()-1; col+=2){
		//v�rifie que les colonnes sont remplies
		if(data.getFormattedValue(0, col)!=""){
			indice1=0; indice2=0;indice3=0;indice4=0;
			var titreProb = cleanAccent(data.getValue(1,col));
			var numProb = data.getFormattedValue(0,col);
			//v�rifie si la feuille est bien formatt�e
			if(isNaN(numProb)){
				label= data.getColumnLabel(col);
				numProbStr= label.replace('V/F','').replace(/([aA-zZ]|�|,)*/g,'').replace('mixte','');
				numProb=parseInt(numProbStr);
				titreProbStr= label.replace('V/F','').replace('mixte','').split(/[1-9]/g);;
				titreProb=cleanAccent(titreProbStr[1]);
			} 
			cle=numProb+"_"+titreProb;
			//v�rifie si le tableau des probl�mes existe
			if(!arrProb[cle]){
				arrProb[cle]= new Array([],0,0,0,0,titreProb,0);
				// table des indexs du tableau des probl�mes tri�s
				if(!arrIndex[numProb])
					arrIndex[numProb]=new Array();
				arrIndex[numProb].push(cle);
			}
			for (var row = 0; row < data.getNumberOfRows()-3; row++) {
				//v�rifie s'il faut prendre en compte le crit�re
				style="";
				if(escapeHtml(data.getValue(row, col))=="F"){
					var idCrit = data.getFormattedValue(row, 0);
					if(!VerifSupCrit(idCrit)){
						if(trace)
							console.log((row)+','+(col)+" "+data.getValue((row), col));
						//r�cup�re la ligne de probl�me au tableau
						var ligneProb = getLigneProb(data,idCrit,row,col);
						if(ligneProb!=","){
							//ajoute la ligne de probl�me au tableau
							arrProb[cle][0].push(ligneProb);
							//incr�mente le nombre de crit�re
							arrProb[cle][6]++;

							//v�rifie l'indice le plus �l�v�
							if(arrProb[cle][1] < data.getValue(row, 3))
								arrProb[cle][1]= data.getValue(row, 3);
							if(arrProb[cle][2] < data.getValue(row, 4))
								arrProb[cle][2]= data.getValue(row, 4);
							if(arrProb[cle][3] < data.getValue(row, 5))
								arrProb[cle][3]= data.getValue(row, 5);
							if(arrProb[cle][4] < data.getValue(row, 6))
								arrProb[cle][4]= data.getValue(row, 6);
						}
					}
				}
			}
		}
	}
	//v�rifie s'il reste des feuilles � traiter
	if(idFeuille<ul.length-1){
		//met � jour le libelle de la feuille
		idFeuille ++;
		CreaAllReport(); 
	}else{
		//boucle sur les probl�mes
		for(vari=0;vari<=arrIndex.length;vari++){
			if(arrIndex[vari]){
				for(k=0;k < arrIndex[vari].length ; k++){
					var Prob = arrProb[arrIndex[vari][k]];
					html.push(getEnteteProb(vari,Prob[5],Prob[0]));
					html.push(getResumeProb(Prob[6],Prob[1],Prob[2],Prob[3],Prob[4]));
				}
			}
		}
		html.push("</body></html>");	     
		//alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
		var fic = spreadsheet+".html";
		params="html="+ html.join('')+"&file="+fic;
		//AjaxRequestPost(urlAjax+"index/creatrepport",params,"ProgressIndicatorStop",'',true);
		popup = null;
		popup = window.open("","Rapport");
		popup.document.write(html.join(''));
		popup.stop();
		html = null;
	}
}    
function getLigneProb(data,idCrit,row,col) {
	try {
        var html=new Array();
			var rowspan=getSolutions(idCrit,true);
	        	html.push("<tr>");
	        	html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
	        	html.push(escapeHtml(data.getValue(row, 2))+" ");
	        	html.push("</td>");
	        	html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
	        	html.push(escapeHtml(data.getValue(row, col+1))+" ");
	        	html.push("</td>");
	        	//calcule la solution
	        	html.push(getSolutions(idCrit,nbre=false,rowspan));
			return html.join('');		
	} catch(ex2){
		alert("GoogleDocs:getLigneProb:"+ex2);
	}
}
function getEnteteProb(numProb,titreProb,lignesProb) {
	try {
		var html=new Array();
		html.push("<p style='mso-style-unhide:no;mso-style-qformat:yes;mso-style-parent:\"\";margin:0cm;margin-bottom:.0001pt;mso-pagination:widow-orphan;font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-fareast-font-family:\"Times New Roman\";mso-outline-level:4;mso-special-character:line-break;page-break-before:always'><b><span style='font-size:10.0pt;font-family:\"Arial\",\"sans-serif\"'>Probl&egrave;me "+numProb+": </span></b><span style='font-size:10.0pt;font-family:\"Arial\",\"sans-serif\";mso-bidi-font-weight:bold'>"+titreProb+"<b><o:p></o:p></b></span></p>"+newPB("&nbsp;"));
		html.push(newPB("Diagnostic des crit�res r�glementaires posant probl�mes :")+newPB("&nbsp;"));
		html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse;' > ");
		html.push("<tr>");
		html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Crit�re r�glementaire </td>" );
		html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Mesures et observations </td>" );
		html.push("<td colspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Solutions </td>");
		html.push("</tr>");
		html.push("<tr>");
		html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Pr�conisations </td>" );
		html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Estimations </td>" );
		html.push("</tr>");
		html.push(lignesProb);
		html.push("</table>"+newPB("&nbsp;"));
		return html.join('').replace(/,/g,'');		
	} catch(ex2){
		alert("GoogleDocs:getResumeProb:"+ex2);
	}
}
function getResumeProb(nbrCrit,indice1,indice2,indice3,indice4) {
	try {
        var html=new Array();
        var style;
        if(nbrCrit<=5)
			style="text-align:center;font-family:Arial;font-size:10pt;background:#FF8C00;mso-outline-level:4";
		if(nbrCrit>5)
			style="text-align:center;font-family:Arial;font-size:10pt;background:#FF0000;color:#FFF;mso-outline-level:4";
		if(nbrCrit>10)
			style="text-align:center;font-family:Arial;font-size:10pt;background:#000;color:#FFF;mso-outline-level:4";    
        html.push(newPB("Diagnostic par type de d�ficience :")+newPB("&nbsp;"));
		html.push("<table cellspacing='10' border='2' width='6cm' height='2.5cm'  style='border-collapse:collapse;mso-special-character:line-break;page-break-before:always;mso-outline-level:4' >");
        html.push("<tr>");
        html.push("<td>"); 
        html.push("<img src='"+urlImg+"logo_moteur.png'/>");
        html.push("</td>");
        html.push("<td>"); 
        html.push("<img src='"+urlImg+"logo_auditif.png'/>");
        html.push("</td>");
        html.push("<td>"); 
        html.push("<img src='"+urlImg+"logo_visuel.png'/>");
        html.push("</td>");
        html.push("<td>"); 
        html.push("<img src='"+urlImg+"logo_cognitif.png'/>");
        html.push("</td>");
        html.push("</tr>");
        html.push("<tr>");
        html.push("<td  style='"+style+"' >"+indice1+"</td>");
        html.push("<td  style='"+style+"'>"+indice2+"</td>");
        html.push("<td  style='"+style+"'>"+indice3+"</td>");
        html.push("<td  style='"+style+"'>"+indice4+"</td>");
        html.push("</tr>");
        html.push("</table>"+newPB("&nbsp;"));
		html.push(newPB("Pr�conisation(s) d'am�lioration r�pondant � la r�glementation :")+newPB("&nbsp;"));
		html.push(newP("Pour les pr�conisations usuelles, se reporter au tableau ci-dessus. Pour les pr�cisions, se reporter au descriptif ci-dessous. ")+newPB("&nbsp;"));
		html.push(newPB("Pr�conisation(s) optionnelle(s) d'am�nagement :")+newPB("&nbsp;"));
		html.push(newP("Pas de pr�conisation(s) optionnelle(s).")+newPB("&nbsp;"));
		html.push(newPB("Estimations des couts des pr�conisation(s) d'am�lioration r�pondant � la r�glementation :")+newPB("&nbsp;"));
		html.push(newPB("Estimations des couts des pr�conisation(s) optionnelle(s) d'am�lioration :")+newPB("&nbsp;"));
		html.push("<p style='font-family:Arial;font-size:8pt'>Note : les co�ts sont donn�s en prix HT (Hors Taxe) et en Euro. Ils sont donn�s en fonction des conditions de march� avec une approximation de 15 % et sans tenir compte des �ventuels probl�mes li�s � la structure de b�timent, aux co�ts de d�molitions ou d'�ventuelles �tudes compl�mentaires (�tude de portance...).</p>");
		return html.join('');		
	} catch(ex2){
		alert("GoogleDocs:getResumeProb:"+ex2);
	}
}
function newPB(texte){
	return "<p style='mso-style-unhide:no;mso-style-qformat:yes;mso-style-parent:\"\";margin:0cm;margin-bottom:.0001pt;mso-pagination:widow-orphan;font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-fareast-font-family:\"Times New Roman\";mso-outline-level:4'><b><span style='font-size:10.0pt;font-family:\"Arial\",\"sans-serif\"'>"+texte+"<o:p></o:p></span></b></p>";
}
function newP(texte){
	return "<p style='mso-style-unhide:no;mso-style-qformat:yes;mso-style-parent:\"\";margin:0cm;margin-bottom:.0001pt;mso-pagination:widow-orphan;font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-fareast-font-family:\"Times New Roman\";mso-outline-level:4'><span style='font-size:10.0pt;font-family:\"Arial\",\"sans-serif\"'>"+texte+"<o:p></o:p></span></p>";
}
function getSolutions(idCrit,nbre,k) {
	try {
		var Xpath ="/solutions/solution[@idcrit='"+idCrit+"']";
		var iterator = xmlSols.evaluate(Xpath, xmlSols, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );
		var precos=new Array();
		var precosSorted=new Array();
		var n;
		while(n = iterator.iterateNext()){
			if(n.attributes[3].nodeValue!="xxx")
				precos[n.attributes[1].nodeValue] = "<tr><td style='font-family:Calibri;font-size:9pt;text-align:center;'>"+n.attributes[1].nodeValue+"</td><td style='width:3cm;font-family:Calibri;font-size:9pt;text-align:center;' >"+n.attributes[2].nodeValue+" par "+n.attributes[3].nodeValue+"</td></tr>";
			else
				precos[n.attributes[1].nodeValue] = "<tr><td style='font-family:Calibri;font-size:9pt;text-align:center;'>"+n.attributes[1].nodeValue+"</td><td style='width:3cm;font-family:Calibri;font-size:9pt;text-align:center;' >"+n.attributes[2].nodeValue+"</td></tr>";	
		}
		for(n in precos){
			precosSorted.push(precos[n]); // Cr�ation d'un tableau index� pour supprimer les doublons
		}
		if(nbre==true){
			return precosSorted.length;
		}		
		precosSorted.sort();
			return (precosSorted.join('')).slice(4).replaceAll("A estimer","Estimation trop al�atoire");		

	} catch(ex2){
		alert("GoogleDocs:getSolutions:"+ex2);
	}
}
String.prototype.replaceAll = function(pcFrom, pcTo){
	var i = this.indexOf(pcFrom);
	var c = this;
	while (i > -1){
		c = c.replace(pcFrom, pcTo); 
		i = c.indexOf(pcFrom);
	}
	return c;
}
function escapeHtml(text) {
	if (text == null ||!isNaN(text) )
		return '';
	return text.replace(/&/g, '&amp;').replace(/</g, 'inf�ieur;').replace(/>/g, 'sup�rieur;').replace(/"/g, '\"');
}
function cleanAccent(text) {
	if (text == null)
		return '';
	return text.replace(/�/g, 'e').replace(/�/g, 'e');
}
function ViewSpreadsheet(feuille){
	id=feuille.split("-");
	var url=urlSpread+'&output=html&gid='+id[1]+'&single=true&widget=true';
	console.log(urlSpread);
	document.getElementById('ViewSpreadsheet').setAttribute("src",url);
	WorkSheetTitle=cleanAccent(id[0]);
}
function ListeFeuilles(response){ 
	var liste="";
	sheets=document.getElementById('ListeFeuilles');
	while(sheets.hasChildNodes()){
		sheets.removeChild(sheets.lastChild);	
	} 
	//ul=eval(response);
	ul=eval('('+response+')');
	//initialisation du tableau des probl�mes
	arrProb = new Array(ul.length);
	idFeuille = 0;
	for(m=0;m<ul.length;m++){
		doc=ul[m].replace(/&| |-/g,"_");
		liste+="<li>";
		liste+="<a id='Feuille_"+m+"' href='#' onclick='ViewSpreadsheet(\""+doc+"-"+m+"\");CreaReport();'>"+ul[m]+"</a>";
		liste+="</li>";
	}
	submitted=doSubmit(); 
	sheets.innerHTML = liste;
	document.documentElement.style.cursor = "auto";
	document.getElementById('table').style.visibility = "visible";
}
function ViewRapport(){
	var url = urlRapport+encodeURIComponent(WorkSheetTitle)+".html";
	window.open(url);
}
function ViewAllRapport(){
	spreadsheet=spreadsheet.replace(/ /g,'_');
	var url = urlRapport+encodeURIComponent(spreadsheet)+".html";
	window.open(url);
}
function CreaReport(){
	submitted=doSubmit();
	messageProgressIndicator('Le rapport est en cours de creation , veuillez patienter...');
	query = new google.visualization.Query(urlSpread+"&gid="+id[1]+'&pub=1');
	//query.setTimeout(1000);
	query.send(handleQueryResponse);  // Send the query with a callback function
}
function CreaAllReport(){
	var query = new google.visualization.Query(urlSpread+"&gid="+idFeuille+'&pub=1');
	// query.setTimeout(1000);
	query.send(handleQueryAllResponse);  // Send the query with a callback function
}
function getSpreadSheet(Name){
	var menu="";
	
	menu+=("<form name='key' style='float:left''><select>");
	for(i=0;i<nbrspreadsheet;i++){
		spread=Name[i].split("*");
		key=spread[1].split("/");
		nameSpread=spread[0].replace(/'/g,' ').replace(/�|�/g,'e');
		menu+=("<option onclick='getWorkSheet(\""+key[5]+"\",\""+nameSpread+"\")'>");
		menu+=(spread[0]);
		menu+=("</option>");
	}
	menu+=("</select></form>");
	document.getElementById('MenuSpreadSheet').innerHTML = menu;
	
}
function getWorkSheet(key,nameSpread){
	submitted=doSubmit(); 
	messageProgressIndicator("La r�cuperation des worksheets est en cours , veuillez patienter...");
	spreadsheet=nameSpread;
	urlSpread=urlSpreadsheet+key;
	urlQuery=urlQueryBase+key;
	params="key="+key;
	AjaxRequest(urlAjax+"index/accueil?key="+key,'ListeFeuilles','');
	document.documentElement.style.cursor = "wait";
}    
function VerifSupCrit(idCrit){
	try {
		var Xpath ="/criteres/critere[@id='"+idCrit+"']";
		var iterator = xmlParam.evaluate(Xpath, xmlParam, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );
		return iterator.iterateNext();		
	} catch(ex2){
		alert("GoogleDocs:VerifSupCrit:"+ex2);
	}
}
function doSubmit() {
	if (! submitted) {
		submitted = true;
		ProgressImg = document.getElementById('inprogress_img');
		document.getElementById("inprogress").style.visibility = "visible";
		document.getElementById("inprogress").style.display = "block";
		setTimeout("ProgressImg.src = ProgressImg.src",100);
		return true;
	}
	else {
		document.getElementById("inprogress").style.visibility = "hidden";
		document.getElementById("inprogress").style.display = "none";
		return false;
	}
}
function ProgressIndicatorStop(result){
	submitted=doSubmit(); 
}
function messageProgressIndicator(msg){
	msgSpan=document.getElementById('inprogressMsg');
	while(msgSpan.hasChildNodes()){
		msgSpan.removeChild(msgSpan.lastChild);	
	} 
	document.getElementById('inprogressMsg').innerHTML = msg;
}
