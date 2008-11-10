 
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
        html.push("<html>");
        html.push("<body>");
        for (var row = 0; row < data.getNumberOfRows()-3; row++) {
        	if(escapeHtml(data.getFormattedValue(row, 7))=="F"){
	        html.push("<h3>Problème  "+r+"</h3>");
	        html.push("<h3>Diagnostic des critères réglementaires posant problèmes</h3>");
	        html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
	        	html.push("<th style='background-color:#66CC99;'>Critère réglementaire </th>");
	       	    html.push("<th style='background-color:#66CC99;'>Mesure et observation </th>");
	        	html.push("<tr>");
	        		html.push("<td>");
	        			html.push(escapeHtml(data.getFormattedValue(row, 1))+" ");
	        		html.push("</td>");
	        		html.push("<td>");
	        			html.push(escapeHtml(data.getFormattedValue(row, 8))+" ");
	        		html.push("</td>");
	        	   html.push("</tr>");     	
	        html.push("</table>");
	        html.push("<h3>Diagnostic par type de déficience :</h3>");
	        html.push("<table border='1'>");
	        	html.push("<tr>");
	        		html.push("<td>"); 
	        			html.push("<img src='"+urlImg+"indice1.png'/>");
	        		html.push("</td>");
	        		html.push("<td>"); 
	        			html.push("<img src='"+urlImg+"indice2.png'/>");
	        		html.push("</td>");
	        		html.push("<td>"); 
	        			html.push("<img src='"+urlImg+"indice3.png'/>");
	        		html.push("</td>");
	        		html.push("<td>"); 
	        			html.push("<img src='"+urlImg+"indice4.png'/>");
	        		html.push("</td>");
	        	html.push("</tr>");
	        	html.push("<tr>");
	        		html.push("<td valign='middle' >"+data.getFormattedValue(row, 3)+"</td>");
	        		html.push("<td valign='middle' >"+data.getFormattedValue(row, 4)+"</td>");
	        		html.push("<td valign='middle' >"+data.getFormattedValue(row, 5)+"</td>");
	        		html.push("<td valign='middle' >"+data.getFormattedValue(row, 6)+"</td>");
	        	html.push("</tr>");
	        html.push("</table>");
	        html.push("<h3>Solutions :</h3>");
	        html.push("<h3>Couts :</h3>");
	        html.push("<p>Le coût est donnée HT, il est donnée en fonction des conditions de marché avec une approximation de 15 et sans tenir compte des éventuels problèmes liés à la structure de bâtiment, aux coûts de démolitions ou d'éventuelles études complémentaires (étude de portance...)*euro</p> ");
        	r++;
        	}
	    }
	    html.push("</body></html>");
	    //alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
	    params="html="+ html.join('')+"&file="+id[0]+".html";
        AjaxRequestPost(urlAjax+"index/creatrepport",params,'','',true);
        l++;
        
      }
      function escapeHtml(text) {
        if (text == null)
          return '';

        return text.replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;');
      }

    
     
     function ViewSpeardsheet(feuille){
     	id=feuille.split("-");
     	var url=urlSpreadsheet+'&output=html&gid='+id[1]+'&single=true&widget=true';
     	document.getElementById('ViewSpeardsheet').setAttribute("src",url);
     	WorkSheetTitle=id[0];
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
     	window.open("../"+urlRapport+WorkSheetTitle+".html");
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
    
  
     