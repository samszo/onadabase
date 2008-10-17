 
    function load(){
    	
    	google.load("visualization", "1");
    	google.setOnLoadCallback(initialize); // Set callback to run when API is loaded
    }
    function initialize() {
    	    
    	    for(i=0;i<4;i++){
    	    	query="query";
    	    	query="query"+i;
        		query = new google.visualization.Query("http://spreadsheets.google.com/pub?key=p9ISv2bT_pub5hD88wuZIRw&gid="+i);
        		query.send(teste);  // Send the query with a callback function
    	    }
    }
    
      function teste(response){
      	
      	result[j]=response;
      	j++;
      	
      }
      // Query response handler function.
      function handleQueryResponse(response) {
       
        if (response.isError()) {
          alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
          return;
        }

        var data = response.getDataTable(); 
        var html = [];
        html.push("<html>");
        html.push("<body>");
        html.push("<h3>Probl�me: ESPACE INTERIEURS </h3>");
        html.push("<h3>Diagnostic des crit�res r�glementaires posant probl�mes</h3>");
        html.push("<table id='Probl' cellspacing='10' border='1' style='border-collapse:collapse' > ");
        	html.push("<th>Crit�re r�glementaire</th>");
       	    html.push("<th>Mesure et observation</th>");
        	for (var row = 0; row < data.getNumberOfRows()-1; row++) {
        		if(escapeHtml(data.getFormattedValue(row, 7))=="f"){
        			html.push("<tr>");
        				html.push("<td>");
        					html.push(escapeHtml(data.getFormattedValue(row, 1)));
        				html.push("</td>");
        				html.push("<td>");html.push("</td>");
        	    	html.push("</tr>");
        		}
        	}
        html.push("</table>");
        html.push("<h3>Diagnostic par type de d�ficience :</h3>");
        html.push("<table border='1'>");
        	html.push("<tr>");
        		html.push("<td>"); 
        			html.push("<img src='../images/indice1.png'/>");
        		html.push("</td>");
        		html.push("<td>"); 
        			html.push("<img src='../images/indice2.png'/>");
        		html.push("</td>");
        		html.push("<td>"); 
        			html.push("<img src='../images/indice3.png'/>");
        		html.push("</td>");
        		html.push("<td>"); 
        			html.push("<img src='../images/indice4.png'/>");
        		html.push("</td>");
        	html.push("</tr>");
        	html.push("<tr>");
        		html.push("<td valign='middle' >0</td>");
        		html.push("<td valign='middle' >0</td>");
        		html.push("<td valign='middle' >0</td>");
        		html.push("<td valign='middle' >0</td>");
        	html.push("</tr>");
        html.push("</table>");
        html.push("<h3>Solutions :</h3>");
        html.push("<h3>Couts :</h3>");
        html.push("<p>Le co�t est donn�e HT, il est donn�e en fonction des conditions de march� avec une approximation de 15 et sans tenir compte des �ventuels probl�mes li�s � la structure de b�timent, aux co�ts de d�molitions ou d'�ventuelles �tudes compl�mentaires (�tude de portance...)*euro</p> ");
        AjaxRequest(urlAjax+"php/CreatRepport.php?f=CreatRepport&html="+ html.join('')+"&file=EspaceEnterieur.doc",'CreatLien' ,'');
        //document.getElementById('repport').innerHTML = html.join('');
        
      }
      function escapeHtml(text) {
        if (text == null)
          return '';

        return text.replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;');
      }

     function CreatLien(result){
     	 
     	  lien=result.split("*");
     	  a="<a href="+lien[0]+">"+lien[1]+"</a>";
          document.getElementById('repport').innerHTML =a;
     }
     
     function ViewSpeardsheet(url){
     	document.getElementById('ViewSpeardsheet').setAttribute("src",url);
     }
