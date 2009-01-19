 
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
        var html = [];
        var r=1;
        var indice1=0; indice2=1;indice3=0;indice4=0;
        html.push("<html>");
        html.push("<body>");
        for(var col = 7; col < data.getNumberOfColumns()-1; col+=2){
        	//vérifie que les colonne sont remplies
        	if(data.getFormattedValue(0, col)!=""){
	             indice1=0; indice2=1;indice3=0;indice4=0;
	             var titreProb = data.getColumnLabel(col);//.replace('V/F', '');
	        	 html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Problème : "+titreProb+"</h4>");
	        	 html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Diagnostic des critères réglementaires posant problèmes :</h4>");
		         html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
		         html.push("<th style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt'>Critère réglementaire </th>");
		       	 html.push("<th style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt'>Mesures et observations </th>");
		       	 html.push("<th style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt'>Préconisations </th>");
		         for (var row = 0; row < data.getNumberOfRows()-3; row++) {
		         	//vérifie s'il faut prendre en compte le critère
		         	var idCrit = data.getFormattedValue(row, 0);
		         	if(!VerifSupCrit(idCrit)){
			         	if(trace)
				         	console.log((row)+','+(col+1)+" "+data.getFormattedValue((row), col+1));
			           if(escapeHtml(data.getFormattedValue(row, col))=="F"){
				         html.push("<tr>");
				         html.push("<td style='font-family:Arial;font-size:10pt'>");
				         html.push(escapeHtml(data.getFormattedValue(row, 2))+" ");
				         html.push("</td>");
				         html.push("<td style='font-family:Arial;font-size:10pt'>");
				         html.push(escapeHtml(data.getFormattedValue(row, col+1))+" ");
				         html.push("</td>");
				         //calcul la solution
						 html.push("<td style='font-family:Arial;font-size:10pt' >");
				         html.push(getSolutions(idCrit));
				         html.push("</td>");
				         html.push("</tr>");
			             if(indice1 < data.getFormattedValue(row, 3))
			             	indice1= data.getFormattedValue(row, 3);
			             if(indice2 < data.getFormattedValue(row, 4))
			             	indice2= data.getFormattedValue(row, 4);
			             if(indice3 < data.getFormattedValue(row, 5))
			             	indice3= data.getFormattedValue(row, 5);
			             if(indice4 < data.getFormattedValue(row, 6))
			             	indice4= data.getFormattedValue(row, 6);
			          }
			        }
		        }
		        html.push("</table>");
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
		        html.push("<td valign='middle' >"+indice1+"</td>");
		        html.push("<td valign='middle' >"+indice2+"</td>");
		        html.push("<td valign='middle' >"+indice3+"</td>");
		        html.push("<td valign='middle' >"+indice4+"</td>");
		        html.push("</tr>");
		        html.push("</table>");
		        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Préconisation(s) d'amélioration répondant à la réglementation :</h4>");
		        html.push("<p style='font-family:Arial;font-size:10pt'>Pour les préconisations usuelles se reporter au tableau ci-dessus. Pour les précisions se reporter au descriptif ci-dessous. </p>")
				html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Préconisation(s) optionnelle(s) d'aménagement :</h4>");
				html.push("<p style='font-family:Arial;font-size:10pt'>Pas de préconisation(s) optionnelle(s). </p>")
			    html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Estimation des coûts de(s) préconisation(s) d'amélioration(s) répondant à la réglementation :</h4>");
				html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>Estimation des coûts de(s) préconisation(s) optionnelle(s) d'amélioration :</h4>");
				html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:10pt'>0 Euro </h4>");
		        html.push("<p style='font-family:Arial;font-size:8pt'>Note : les coûts sont donnés en prix HT (Hors Taxe) et en Euro. Ils sont donnés en fonction des conditions de marché avec une approximation de 15 % et sans tenir compte des éventuels problèmes liés à la structure de bâtiment, aux coûts de démolitions ou d'éventuelles études complémentaires (étude de portance...).</p> ");
			}
	     }
	     html.push("</body></html>");
	    //alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
	    params="html="+ html.join('')+"&file="+cleanAccent(id[0])+".html";
        AjaxRequestPost(urlAjax+"index/creatrepport",params,'','',true);
        l++;
	      
     }
        
        
        
     function getSolutions(idCrit) {
	  try {
		var Xpath ="/solutions/solution[@idcrit='"+idCrit+"']";
		var iterator = xmlSols.evaluate(Xpath, xmlSols, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );

		var precos=[];
		precos.push("<table cellspacing='3' style='width:100%' border='1' style='border-collapse:collapse' >");
       	precos.push("<th style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt'>Préconisations </th>");
       	precos.push("<th style='background-color:#CCCCCC;font-weight:bold;font-family:Arial;font-size:10pt'>Estimations </th>");
		var n;
		while(n = iterator.iterateNext()){
			precos.push("<tr><td>"+n.attributes[1].nodeValue+"</td>");
			if(n.attributes[3].nodeValue!="xxx")
				precos.push("<td style='width:3cm' >"+n.attributes[2].nodeValue+" par "+n.attributes[3].nodeValue+"</td></tr>");
			else
				precos.push("<td style='width:3cm' >"+n.attributes[2].nodeValue+"</td></tr>");			
		}
		precos.push("</table>"); 
	
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

     function CreaReport(){
     	query = new google.visualization.Query(urlSpreadsheet+"&gid="+id[1]);
     	query.setTimeout(1000);
     	query.send(handleQueryResponse);  // Send the query with a callback function
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
  
     