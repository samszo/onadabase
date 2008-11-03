 
    function load(){
    	
    	google.load("visualization", "1");
    	//google.setOnLoadCallback(initialize); // Set callback to run when API is loaded
    }
    function initialize() { 
    
    	    //recupperation de toutes les feuilles de calcul 
    	    for(i=0;i<nbrworksheet;i++){
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
        for (var row = 7; row < data.getNumberOfRows()-3; row++) {
        	if(escapeHtml(data.getFormattedValue(row, 7))=="F"){
	        html.push("<h3>Problème  "+r+"</h3>");
	        html.push("<h3>Diagnostic des critères réglementaires posant problèmes</h3>");
	        html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
	        	html.push("<th>Critère réglementaire</th>");
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
	    //alert("GoogleDocs:handleQueryResponse:id_feuil="+id_feuil);
	    params="f=CreatRepport&html="+ html.join('')+"&file="+encodeURI(Name[id_feuil]+".doc");
        AjaxRequestPost(urlAjax+"php/CreatRepport.php",params,'','',false);
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
     
     function ViewSpeardsheet(f){
     	//id=f.split("_");
     	var url=urlSpreadsheet+'&output=html&gid='+f+'&single=true&widget=true';
     	document.getElementById('ViewSpeardsheet').setAttribute("src",url);
     	CreaReport(f);
     	id_feuil=f;
     }
     function ListeFeuilles(response){   
     	        ul=eval('('+response+')');
	     	        for(m=0;m<ul.length;m++){
		     			liste+="<li>";
		     			liste+="<a id='Feuille_"+m+"' href='#' onclick='ViewSpeardsheet("+m+");'>"+ul[m]+"</a>";
		     			liste+="</li>";
	     	        }
     		    document.getElementById('ListeFeuilles').innerHTML = liste;
     		    document.getElementById('table').style.visibility = "visible"
     }
     function ViewRapport(response){
     	window.open(urlRapport+response.replace(" ","_")+".doc");
     }

     function CreaReport(i){
     	query = new google.visualization.Query(urlSpreadsheet+"&gid="+i);
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
     		console.log(key[5]);
     		menu+=("<option onclick='getWorkSheet(\""+key[5]+"\")'>");
     		menu+=(speard[0]);
     		menu+=("</option>");
     	}
     	menu+=("</select></form>");
     	console.log(menu);
     	document.getElementById('MenuSpeardSheet').innerHTML = menu;
    }
    function getWorkSheet(key){
    	urlSpreadsheet=urlSpreadsheet+key;
    	params="key="+key;
    	AjaxRequest(urlAjax+"index/accueil?key="+key,'ListeFeuilles','');
    }
    
   
     