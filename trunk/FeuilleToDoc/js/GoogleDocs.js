 
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
	        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:11px'>Probl�me  "+r+"</h4>");
	        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:11px'>Diagnostic des crit�res r�glementaires posant probl�mes</h4>");
	        html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
	        	html.push("<th style='background-color:#66CC99;font-family:Arial;font-size:11px'>Crit�re r�glementaire </th>");
	       	    html.push("<th style='background-color:#66CC99;font-family:Arial;font-size:11px'>Mesure et observation </th>");
	        	html.push("<tr>");
	        		html.push("<td style='font-family:Arial;font-size:11px'>");
	        			html.push(escapeHtml(data.getFormattedValue(row, 1))+" ");
	        		html.push("</td>");
	        		html.push("<td style='font-family:Arial;font-size:11px'>");
	        			html.push(escapeHtml(data.getFormattedValue(row, 8))+" ");
	        		html.push("</td>");
	        	   html.push("</tr>");     	
	        html.push("</table>");
	        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:11px'>Diagnostic par type de d�ficience :</h4>");
	        html.push("<table cellspacing='10' border='2' style='border-collapse:collapse' >");
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
	        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size:11px'>Solutions :</h4>");
	        html.push("<p style='font-weight:bold;font-family:Arial;font-size:11px'> Pour les solutions se reporter </p>")
	        html.push("<h4 style='font-weight:bold;font-family:Arial;font-size11px'>Couts :</h4>");
	        html.push("<p style='font-family:Arial;font-size:11px'>Les co�ts sont donn�s en prix HT(Hors Taxe) et en Euro. Ils sont donn�s en fonction des conditions de march� avec une approximation de 15 % et sans tenir compte des �ventuels probl�mes li�s � la structure de b�timent, aux co�ts de d�molitions ou d'�ventuelles �tudes compl�mentaires (�tude de portance...)</p> ");
        	r++;
        	}
	    }
	    html.push("</body></html>");
	    //alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
	    params="html="+ html.join('')+"&file="+id[0]+".html";
        AjaxRequestPost(urlAjax+"index/creatrepport",params,'','',true);
        //AjaxRequest(urlAjax+"index/uploadtogoogledoc?path="+"../repports/Escalier_m�canique.doc&file=Escalier_m�canique.doc",'','');
        l++;
        
      }      function escapeHtml(text) {
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
     	var url = "../"+urlRapport+WorkSheetTitle+".html";
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
    
  
     