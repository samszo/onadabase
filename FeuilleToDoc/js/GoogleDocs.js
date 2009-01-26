 
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
        var r=1;
        var indice1=0; indice2=1;indice3=0;indice4=0;
        var html=[];
        html.push("<html>");
        html.push("<body>");
        for(var col = 7; col < data.getNumberOfColumns()-1; col+=2){
        	//vérifie que les colonne sont remplies
        	if(data.getFormattedValue(0, col)!=""){
	            indice1=0; indice2=0;indice3=0;indice4=0;
	            var titreProb = data.getValue(1,col);
	        	 html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Problème "+data.getFormattedValue(0,col)+": "+titreProb+"</h4>");
	        	 html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Diagnostic des critères réglementaires posant problèmes :</h4>");
		         html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
		         html.push("<tr>");
		         html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Critère réglementaire </td>" );
		       	 html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Mesures et observations </td>" );
		       	 html.push("<td colspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Solutions </td>");
		         html.push("</tr>");
		         html.push("<tr>");
		         html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Préconisations </td>" );
		         html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Estimations </td>" );
		         html.push("</tr>");
		         var nbrCrit=0;
		          for (var row = 0; row < data.getNumberOfRows(); row++) {
		         	//vérifie s'il faut prendre en compte le critère
		         	style="";
		         	var idCrit = data.getFormattedValue(row, 0);
		         	if(!VerifSupCrit(idCrit)){
			          if(trace)
				         	console.log((row)+','+(col)+" "+data.getValue((row), col));
				       rowspan=getSolutions(idCrit,nbre=true);
				       /*if(rowspan==2)
				       	rowspan--; */ 
			           if(escapeHtml(data.getValue(row, col))=="F"){
				         html.push("<tr>");
				         html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
				         html.push(escapeHtml(data.getValue(row, 2))+" ");
				         html.push("</td>");
				         html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
				         html.push(escapeHtml(data.getValue(row, col+1))+" ");
				         html.push("</td>");
				         //calcul la solution
				         html.push(getSolutions(idCrit,nbre=false,rowspan));
				        // html.push("</tr>");
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
		        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Diagnostic par type de déficience :</h4>");
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
		        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Préconisation(s) d'amélioration répondant à la réglementation :</h4>");
		        html.push("<p style='font-family:Arial;font-size:10pt'>Pour les préconisations usuelles se reporter au tableau ci-dessus. Pour les précisions se reporter au descriptif ci-dessous. </p>")
				html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Préconisation(s) optionnelle(s) d'aménagement :</h4>");
				html.push("<p style='font-family:Arial;font-size:10pt'>Pas de préconisation(s) optionnelle(s). </p>")
			    html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Couts solution(s) principale(s) :</h4>");
				html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Couts des préconisation(s) d'amélioration répondant à la réglementation :</h4>");
				html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Couts des préconisation(s) optionnelle(s) d'amélioration :</h4>");
		        html.push("<p style='font-family:Arial;font-size:8pt'>Note : les coûts sont donnés en prix HT (Hors Taxe) et en Euro. Ils sont donnés en fonction des conditions de marché avec une approximation de 15 % et sans tenir compte des éventuels problèmes liés à la structure de bâtiment, aux coûts de démolitions ou d'éventuelles études complémentaires (étude de portance...).</p> ");
			}
	     }
	     html.push("</body></html>");
	    //alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
	    params="html="+ html.join('')+"&file="+cleanAccent(id[0])+".html";
        AjaxRequestPost(urlAjax+"index/creatrepport",params,'','',true);
        l++;
	      
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
        var r=1;
        var indice1=0; indice2=1;indice3=0;indice4=0;
        var html=[];
        html.push("<html>");
        html.push("<body>");
        for(var col = 7; col < data.getNumberOfColumns()-1; col+=2){
        	//vérifie que les colonne sont remplies
        	if(data.getFormattedValue(0, col)!=""){
	            indice1=0; indice2=0;indice3=0;indice4=0;
	            var titreProb = data.getValue(1,col);
	            var numProb = data.getFormattedValue(0,col);
	            //vérifie si la feuille est bien formattée
	            if(isNaN(numProb)){
	                label= data.getColumnLabel(col);
	                numProbStr= label.replace('V/F','').replace(/([aA-zZ]|é|,)*/g,'').replace('mixte','');
	                numProb=parseInt(numProbStr);
	                titreProbStr= label.replace('V/F','').replace('mixte','').split(/[1-9]/g);;
	                titreProb=titreProbStr[1];
	            }
		            //vérifie si le tableau des problèmes existe
		            if(!arrProb[numProb])//{
		            	arrProb[numProb]= new Array([],0,0,0,0,titreProb,0);
			          for (var row = 0; row < data.getNumberOfRows()-3; row++) {
			         	//vérifie s'il faut prendre en compte le critère
			         	style="";
			         	if(escapeHtml(data.getValue(row, col))=="F"){
				         	var idCrit = data.getFormattedValue(row, 0);
				         	if(!VerifSupCrit(idCrit)){
					        	if(trace)
						        	console.log((row)+','+(col)+" "+data.getValue((row), col));
						        //récupère la ligne de problème au tableau
						        var ligneProb = getLigneProb(data,idCrit,row,col);
						        if(ligneProb!=","){
							        //ajoute la ligne de problème au tableau
							        arrProb[numProb][0].push(ligneProb);
							        //incrémente le nbre de critère
							        arrProb[numProb][6]++;
							        if(numProb==1)
							        console.log(numProb+'/'+  arrProb[numProb][6]+"/"+idCrit);
						            //vérifie l'indice le plus élévé
						             if(arrProb[numProb][1] < data.getValue(row, 3))
						             	arrProb[numProb][1]= data.getValue(row, 3);
						             if(arrProb[numProb][2] < data.getValue(row, 4))
						             	arrProb[numProb][2]= data.getValue(row, 4);
						             if(arrProb[numProb][3] < data.getValue(row, 5))
						             	arrProb[numProb][3]= data.getValue(row, 5);
						             if(arrProb[numProb][4] < data.getValue(row, 6))
						             	arrProb[numProb][4]= data.getValue(row, 6);
						        }
					        }
				      }
			        }
				//}
			}
	     }
	     var arr = arrProb;
	     
	     //vérifie s'il reste des feuilles à traiter
	     if(idFeuille<ul.length){
	     	//met à jour le libelle de la feuille
          	//alert("Feuille "+ul[idFeuille]+" traitée");
	     	idFeuille ++;
			CreaAllReport();
	     }else{
	     	//boucle sur les problèmes
	     	console.log(arrProb[1]);
	     	for(numProb in arrProb){
   	        //for(i=0;i<arrProb.length;i++){
   	        	var Prob = arrProb[numProb];
		     	html.push(getEnteteProb(numProb,Prob[5],Prob[0]));
		     	html.push(getResumeProb(Prob[6],Prob[1],Prob[2],Prob[3],Prob[4]));
		    }
		    
		    html.push("</body></html>");	     
		    //alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
		    var fic = "Total.html";
		    params="html="+ html.join('')+"&file="+fic;
          	alert("Le traitement est terminé envoyez la création du rapport");
	        AjaxRequestPost(urlAjax+"index/creatrepport",params,"",'',true);
	     }
        l++;
	      
     }


     function getLigneProb(data,idCrit,row,col) {
	  try {
        var html=[];
	       var rowspan=getSolutions(idCrit,true);
	        	html.push("<tr>");
	        	html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
	        	html.push(escapeHtml(data.getValue(row, 2))+" ");
	        	html.push("</td>");
	        	html.push("<td rowspan='"+rowspan+"' style='font-family:Arial;font-size:10pt;'>");
	        	html.push(escapeHtml(data.getValue(row, col+1))+" ");
	        	html.push("</td>");
	        	//calcul la solution
	        	html.push(getSolutions(idCrit,nbre=false,rowspan));
			return html.join('');		
	  } catch(ex2){alert("GoogleDocs:getLigneProb:"+ex2);}
     }


     function getEnteteProb(numProb,titreProb,lignesProb) {
	  try {
        var html=[];
       	 html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Problème "+numProb+": "+titreProb+"</h4>");
       	 html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Diagnostic des critères réglementaires posant problèmes :</h4>");
         html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
         html.push("<tr>");
         html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Critère réglementaire </td>" );
       	 html.push("<td rowspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Mesures et observations </td>" );
       	 html.push("<td colspan='2' style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Solutions </td>");
         html.push("</tr>");
         html.push("<tr>");
         html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Préconisations </td>" );
         html.push("<td  style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt;text-align:center;'> Estimations </td>" );
         html.push("</tr>");
		html.push(lignesProb);
        html.push("</table>");
		return html.join('');		

	  } catch(ex2){alert("GoogleDocs:getResumeProb:"+ex2);}
     }

        
     function getResumeProb(nbrCrit,indice1,indice2,indice3,indice4) {
	  try {
        var html=[];
        var style;
        if(nbrCrit<=5)
		  style="text-align:center;font-family:Arial;font-size:10pt;background:#FF8C00";
		if(nbrCrit>5)
		  style="text-align:center;font-family:Arial;font-size:10pt;background:#FF0000;color:#FFF;";
		if(nbrCrit>10)
		   style="text-align:center;font-family:Arial;font-size:10pt;background:#000;color:#FFF;";    
        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Diagnostic par type de déficience :</h4>");
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
        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Préconisation(s) d'amélioration répondant à la réglementation :</h4>");
        html.push("<p style='font-family:Arial;font-size:10pt'>Pour les préconisations usuelles se reporter au tableau ci-dessus. Pour les précisions se reporter au descriptif ci-dessous. </p>")
		html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Préconisation(s) optionnelle(s) d'aménagement :</h4>");
		html.push("<p style='font-family:Arial;font-size:10pt'>Pas de préconisation(s) optionnelle(s). </p>")
	    html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Couts solution(s) principale(s) :</h4>");
		html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Couts des préconisation(s) d'amélioration répondant à la réglementation :</h4>");
		html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Couts des préconisation(s) optionnelle(s) d'amélioration :</h4>");
        html.push("<p style='font-family:Arial;font-size:8pt'>Note : les coûts sont donnés en prix HT (Hors Taxe) et en Euro. Ils sont donnés en fonction des conditions de marché avec une approximation de 15 % et sans tenir compte des éventuels problèmes liés à la structure de bâtiment, aux coûts de démolitions ou d'éventuelles études complémentaires (étude de portance...).</p> ");
		
		return html.join('');		

	  } catch(ex2){alert("GoogleDocs:getResumeProb:"+ex2);}
     }
        
     function getSolutions(idCrit,nbre,k) {
	  try {
		var Xpath ="/solutions/solution[@idcrit='"+idCrit+"']";
			
		var iterator = xmlSols.evaluate(Xpath, xmlSols, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );

		var precos=[];
		var n;
		var i=0;
		while(n = iterator.iterateNext()){
			if(i!=0){
				precos.push("</tr>");
				precos.push("<tr>");
			}
			precos.push("<td>"+n.attributes[1].nodeValue+"</td>");
			if(n.attributes[3].nodeValue!="xxx")
				precos.push("<td style='width:3cm' >"+n.attributes[2].nodeValue+" par "+n.attributes[3].nodeValue+"</td></tr>");
			else
				precos.push("<td style='width:3cm' >"+n.attributes[2].nodeValue+"</td>");	
			precos.push("</tr>");	
			i++;
		}
		if(nbre==true){
			return i;
		}
			return precos.join('');		

	  } catch(ex2){alert("GoogleDocs:getSolutions:"+ex2);}
     }

     function escapeHtml(text) {
        if (text == null)
          return '';

        return text.replace(/&/g, '&amp;')
          .replace(/</g, 'inféieur;')
          .replace(/>/g, 'supérieur;')
          .replace(/"/g, '\"');
     }

     function cleanAccent(text) {
        if (text == null)
          return '';

        return text.replace(/é/g, 'e')
          .replace(/è/g, 'e');
     }
    
     
     function ViewSpeardsheet(feuille){
     	id=feuille.split("-");
     	var url=urlSpreadsheet+'&output=html&gid='+id[1]+'&single=true&widget=true';
     	document.getElementById('ViewSpeardsheet').setAttribute("src",url);
     	WorkSheetTitle=cleanAccent(id[0]);
     }
     function ListeFeuilles(response){ 
     	       var liste="";
     	        sheets=document.getElementById('ListeFeuilles');
     	        while(sheets.hasChildNodes()){
     	        	sheets.removeChild(sheets.lastChild);
     	        	
     	        } 
     	        ul=eval('('+response+')');
     	        
				//initialisation du tableau des problème
				arrProb = new Array(ul.length);
     	        idFeuille = 0;
     	        
     	        for(m=0;m<ul.length;m++){
     	        	doc=ul[m].replace(/&| |-/g,"_");
	     			liste+="<li>";
	     			liste+="<a id='Feuille_"+m+"' href='#' onclick='ViewSpeardsheet(\""+doc+"-"+m+"\");CreaReport();'>"+ul[m]+"</a>";
	     			liste+="</li>";
     	        }
     		    sheets.innerHTML = liste;
     		    document.documentElement.style.cursor = "auto";
     		    document.getElementById('table').style.visibility = "visible"
     }
     function ViewRapport(){
     	var url = urlRapport+encodeURIComponent(WorkSheetTitle)+".html";
     	window.open(url);
     }
     function ViewAllRapport(){
     	var url = urlRapport+"Total.html";
     	window.open(url);
     }

     function CreaReport(){
     	query = new google.visualization.Query(urlSpreadsheet+"&gid="+id[1]);
     	query.setTimeout(1000);
     	query.send(handleQueryResponse);  // Send the query with a callback function
     }

     function CreaAllReport(){
     	query = new google.visualization.Query(urlSpreadsheet+"&gid="+idFeuille);
     	query.setTimeout(1000);
     	query.send(handleQueryAllResponse);  // Send the query with a callback function
     }
     
     function getSpreadSheet(Name){
     	var menu="";
     	var S;
     	menu+=("<form name='key' style='float:left''><select>");
     	for(i=0;i<nbrspeardsheet;i++){
     		speard=Name[i].split("*");
     		key=speard[1].split("/");
     		menu+=("<option onclick='getWorkSheet(\""+key[5]+"\")'>");
     		menu+=(speard[0]);
     		menu+=("</option>");
     	}
     	menu+=("</select></form>");
     	document.getElementById('MenuSpeardSheet').innerHTML = menu;
    }
    function getWorkSheet(key){
    	urlSpreadsheet=urlSpreadsheet+key;
    	params="key="+key;
    	AjaxRequest(urlAjax+"index/accueil?key="+key,'ListeFeuilles','');
    	document.documentElement.style.cursor = "wait";
    }
    
    function VerifSupCrit(idCrit){
	  try {
		var Xpath ="/criteres/critere[@id='"+idCrit+"']";
		var iterator = xmlParam.evaluate(Xpath, xmlParam, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );
		return iterator.iterateNext();		
			
	  } catch(ex2){alert("GoogleDocs:VerifSupCrit:"+ex2);}
	}
  
     