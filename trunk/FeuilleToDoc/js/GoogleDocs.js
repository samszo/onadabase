 
    function load(){
    	
    	google.load("visualization", "1");
    	//google.setOnLoadCallback(initialize); // Set callback to run when API is loaded
    }
    function initialize() { 
    
    	    //recupperation de toutes les feuilles de calcul 
    	    for(i=0;i<nbrspeardsheet;i++){
    	    //for(i=0;i<1;i++){
    	    	//query="query"+i;
        		//query = new google.visualization.Query(urlSpreadsheet+"&gid="+i);
        		//query.send(handleQueryResponse);  // Send the query with a callback function
        		ListeFeuilles(Name[i],i);
        		
    	    	
    	    }
    	 }
    
     function teste(response){
     	//verfier si la feuille existe , on compere la signature des feuilles
        for(k=0;k<signature.length;k++){
        	if(signature[k]==response.xa){
        		in_array=true;
        		
        	}
        } 
      	if(!in_array){
      		signature[j]=response.xa;
      		result[l]=response;
      		ListeFeuilles(response,l);
       		handleQueryResponse(result[l])
      	    j++;
      	}
      	in_array=false;
      	l++;
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
	        html.push("<h3>Probl�me  "+r+"</h3>");
	        html.push("<h3>Diagnostic des crit�res r�glementaires posant probl�mes</h3>");
	        html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
	        	html.push("<th>Crit�re r�glementaire</th>");
	       	    html.push("<th>Mesure et observation</th>");
	        	html.push("<tr>");
	        		html.push("<td>");
	        			html.push(escapeHtml(data.getFormattedValue(row, 1)));
	        		html.push("</td>");
	        		html.push("<td>");
	        			html.push(escapeHtml(data.getFormattedValue(row, 8)));
	        		html.push("</td>");
	        	   html.push("</tr>");     	
	        html.push("</table>");
	        html.push("<h3>Diagnostic par type de d�ficience :</h3>");
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
	        html.push("<p>Le co�t est donn�e HT, il est donn�e en fonction des conditions de march� avec une approximation de 15 et sans tenir compte des �ventuels probl�mes li�s � la structure de b�timent, aux co�ts de d�molitions ou d'�ventuelles �tudes compl�mentaires (�tude de portance...)*euro</p> ");
        	r++;
        	}
	    }
	    html.push("</body></html>");
	    //alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
	    params="html="+ html.join('')+"&file="+id[0]+".doc";
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

     function CreatLien(response){
     	 //file=eval('('+response+')');
     	 ul+="<li><a href='"+file.PATH+"'>"+file.File+"</a></li>";
     	 document.getElementById('ListeRapports').innerHTML = ul;
     }
     
     function ViewSpeardsheet(feuille){
     	id=feuille.split("-");
     	var url=urlSpreadsheet+'&output=html&gid='+id[1]+'&single=true&widget=true';
     	document.getElementById('ViewSpeardsheet').setAttribute("src",url);
     	WorkSheetTitle=id[1];
     	WorkSheetTitle=id[0];
		CreaReport();
     }
     function ListeFeuilles(response){ 
     	       var ul="";
     	        sheets=document.getElementById('ListeFeuilles');
     	        while(sheets.hasChildNodes()){
     	        	sheets.removeChild(sheets.lastChild);
     	        	
     	        } 
     	        ul=eval('('+response+')');
	     	        for(m=0;m<ul.length;m++){
	     	        	doc=ul[m].replace(/&| |-/g,"_");
		     			liste+="<li>";
		     			liste+="<a id='Feuille_"+m+"' href='#' onclick='ViewSpeardsheet(\""+doc+"-"+m+"\");'>"+ul[m]+"</a>";
		     			liste+="</li>";
	     	        }
     		    sheets.innerHTML = liste;
     		    document.getElementById('table').style.visibility = "visible"
     }
     function ViewRapport(){
     	//console.log("c/wamp/www/onadabase/FeuilleToDoc/"+urlRapport+id[0]+".doc")
     	window.open("../"+urlRapport+id[0]+".doc");
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
     		//console.log(key[5]);
     		menu+=("<option onclick='getWorkSheet(\""+key[5]+"\")'>");
     		menu+=(speard[0]);
     		menu+=("</option>");
     	}
     	menu+=("</select></form>");
     	//console.log(menu);
     	document.getElementById('MenuSpeardSheet').innerHTML = menu;
    }
    function getWorkSheet(key){
    	urlSpreadsheet=urlSpreadsheet+key;
    	params="key="+key;
    	AjaxRequest(urlAjax+"index/accueil?key="+key,'ListeFeuilles','');
    }
    
  
     