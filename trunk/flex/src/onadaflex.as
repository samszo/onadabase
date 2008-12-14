// ActionScript file
      import com.google.maps.Map;
      import com.google.maps.LatLng;
      import com.google.maps.LatLngBounds;
      import com.google.maps.MapEvent;
      import com.google.maps.MapMouseEvent;
      import com.google.maps.MapType;
      import com.google.maps.InfoWindowOptions;
      import com.google.maps.overlays.Marker;
      import com.google.maps.overlays.MarkerOptions;
      import com.google.maps.controls.ZoomControl;
    import com.google.maps.styles.FillStyle;
    import com.google.maps.styles.StrokeStyle;

    import mx.controls.Alert;
    import mx.collections.ArrayCollection;
	import mx.rpc.events.ResultEvent;
	import mx.managers.CursorManager;

	[Bindable]
	private var rsEtatDiag:Object;
	[Bindable]
	private var rsEtatDiagListe:Object;

      private var map:Map;
      private var markers:XMLList;

      [Embed(source="A.png")] [Bindable] private var AIcon:Class;
      [Embed(source="B.png")] [Bindable] private var BIcon:Class;
      [Embed(source="C.png")] [Bindable] private var CIcon:Class;
      [Embed(source="D.png")] [Bindable] private var DIcon:Class;
      [Embed(source="E.png")] [Bindable] private var EIcon:Class;
      [Bindable] private var categories:Object = 
        { "grille_66": {
            "color": 0x990000,
            "icon": AIcon,
            "markers": []}, 
          "grille_55": {
              "color": 0x3366F,
	          "icon": BIcon,
              "markers": []},
          "grille_62": {
              "color": 0xFF33FF,
	          "icon": CIcon,
              "markers": []},
          "grille_61": {
              "color": 0x009933,
	          "icon": CIcon,
              "markers": []},
          "grille_53": {
              "color": 0x669933,
	          "icon": CIcon,
              "markers": []},
          "grille_58": {
              "color": 0x00CCFF,
	          "icon": CIcon,
              "markers": []},
          "grille_35": {
              "color": 0x9b2121,
	          "icon": CIcon,
              "markers": []},
          "grille_57": {
              "color": 0x0ba42f,
	          "icon": CIcon,
              "markers": []},
          "grille_63": {
              "color": 0xf78907,
	          "icon": CIcon,
              "markers": []},
          "grille_64": {
              "color": 0x9b0f7c,
	          "icon": CIcon,
              "markers": []},
          "grille_72": {
              "color": 0x2a09f7,
	          "icon": CIcon,
              "markers": []}
		};

      private var handi:Object = 
        { "A": {
            "color": 0xFF0000,
            "icon": AIcon,
            "markers": []}, 
          "B": {
              "color": 0x0000FF,
	          "icon": BIcon,
              "markers": []},
          "C": {
              "color": 0x0000FF,
	          "icon": CIcon,
              "markers": []},
          "D": {
              "color": 0x0000FF,
	          "icon": DIcon,
              "markers": []},
          "E": {
              "color": 0x0000FF,
	          "icon": EIcon,
              "markers": []}
        };

        private function chartEtatDiagChange(event:Event):void {
            var allSeries:Array = event.currentTarget.series;
            chartTrace.text = "";
            var idDoc:String = "";
            var handi:String = "";
            var niv:String = "";
            //construction de l'identifiant du doc
            //cf. library/php/ExeAjax.php?f=GetEtatDiagListe&id=5610&idDoc=0_audio
            for (var i:int=0; i<allSeries.length; i++) {
                chartTrace.text += "\n" + allSeries[i].id + 
                    " Selected Items: " + allSeries[i].selectedIndices;
				//le type de handicap
                switch (allSeries[i].selectedIndices[0]) {
				    case 0:
				        handi = "_moteur";
				        break;
				    case 1:
				        handi = "_audio";
				        break;
				    case 2:
				        handi = "_cog";
				        break;
				    case 3:
				        handi = "_visu";
				        break;
				}				    
                //le niveau seulemet si on a récupéré  l'indice
                if(handi!="" && niv==""){
	                switch (allSeries[i].id) {
					    case "_onadaflex_ColumnSeries1":
					        niv = "0";
					        break;
					    case "_onadaflex_ColumnSeries2":
					        niv = "1";
					        break;
					    case "_onadaflex_ColumnSeries3":
					        niv = "2";
					        break;
					    case "_onadaflex_ColumnSeries4":
					        niv = "3";
					        break;
					}
                }
            }
            idDoc = niv+handi; 
            //exécute la requête
            showListeDiag(idSite.text, idRub.text, idDoc);
        }

		public function rhEtatDiag(event:ResultEvent):void {
			rsEtatDiag = event.result;
			if(rsEtatDiag.toString()!=""){
				//mise à jour des icones
		        for each (var obs:Object in rsEtatDiag.EtatDiag.Obstacles)
		        {
		        	if(obs.id=="moteur")
		        		imgAlphaMoteur.source=handi[obs.handi].icon;
		        	if(obs.id=="audio")
		        		imgAlphaAudio.source=handi[obs.handi].icon;
		        	if(obs.id=="cognitif")
		        		imgAlphaCog.source=handi[obs.handi].icon;
		        	if(obs.id=="visuel")
		        		imgAlphaVisu.source=handi[obs.handi].icon;
		        }
		 	}
		}

		public function rhEtatDiagListe(event:ResultEvent):void {
			rsEtatDiagListe = event.result;
			if(rsEtatDiagListe.toString()!=""){
				//mise à jour des icones
		        var type:String;
		        type = "grille_";
		 	}
		}

        
      public function onHolderCreated(event:Event):void {
        map = new Map();
//local        map.key = "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRSAqqCYJRNRYB52nvFZykN9ZY0cdhRvfhvUr_7t7Rz5_XNkPGDb_GYlQA";
//prod        map.key = "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRQPuSe5bSrCkW0z0AK5OduyCmU7hRSB6XyMSlG4GUuaIVi6tnDRGuEsWw";
        map.key = "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRSAqqCYJRNRYB52nvFZykN9ZY0cdhRvfhvUr_7t7Rz5_XNkPGDb_GYlQA";
        map.addEventListener(MapEvent.MAP_READY, onMapReady);
        mapHolder.addChild(map);
      }

      public function onHolderResized(event:Event):void {
        map.setSize(new Point(mapHolder.width, mapHolder.height));
      }

      private function onMapReady(event:Event):void {
        map.enableScrollWheelZoom();
        map.enableContinuousZoom();
        map.setCenter(new LatLng(47.12995076, 1.00001335), 7);
        map.addControl(new ZoomControl());
        getXml();
     }
     
     public function getXml():void {
         //var xmlString:URLRequest = new URLRequest("http://www.onadabase.eu/bdd/carto/allEtatDiag_centre_.xml");
         var xmlString:URLRequest = new URLRequest("http://localhost/onadabase/bdd/carto/allEtatDiag_local2_.xml");
          var xmlLoader:URLLoader = new URLLoader(xmlString);
          xmlLoader.addEventListener("complete", readXml);
    }
      
    public function readXml(event:Event):void{
        //récupère les geoloc
        var markersXML:XML = new XML(event.target.data);
        markers = markersXML.CartoDonnee;
        
    }

      public function createMarkerGrille(idGrille:String): void {

        var type:String = "grille_"+idGrille;
        //vérifie s'il faut créer les markers ou les rendre visible/invisible
        if(categories[type].markers.length>0){
			toggleCategory(type);
	    }else{	    
	        //boucle sur les géoloc 
	        for each (var markerXml:XML in markers){
	        	//vérifie si le marker possède la grille
	        	var resultG:XMLList;
            	resultG = markerXml.grilles.grille.(@id == idGrille);
            	if(resultG.length()>0){
            		//contruction du markers
		            var titre:String = markerXml.@titre;
		            var adresse:String = markerXml.@adresse;
		            var latlng:LatLng = new LatLng(markerXml.@lat, markerXml.@lng);
			        var marker:Marker = createMarker(latlng, titre, adresse, type, markerXml);
				    categories[type].markers.push(marker);
			        map.addOverlay(marker);            		
            	}  
	        }
	    }
      }

      public function createMarker(latlng:LatLng, name:String, address:String, type:String, markerXml:XML): Marker {

			//inspiration de http://www.tricedesigns.com/portfolio/googletemps/srcview/
			var markerOptions:MarkerOptions = new MarkerOptions({
                        strokeStyle: new StrokeStyle({color: 0x000000}),
                        fillStyle: new FillStyle({color:categories[type].color, alpha: 0.3}),
                        radius: 12,
                        hasShadow: true
                      })
			//markerOptions = new MarkerOptions({icon: new categories[type].icon, iconOffset: new Point(-16, -32)});

	       	var marker:Marker = new Marker(latlng, markerOptions);
	       	var html:String = "<b>" + name + "</b> <br/>" + address;
	        marker.addEventListener(MapMouseEvent.CLICK, function(e:MapMouseEvent):void {
				marker.openInfoWindow(new InfoWindowOptions({contentHTML:html}));
				showStat(markerXml);
	        });
	        return marker;
     } 

     private function showStat(markerXml:XML):void {
        //affiche la box des stats
        chartBox.visible=true;
        chartBox.width=400;
        chartTitre.text = markerXml.@titre;
        idRub.text = markerXml.@idRub;
        idSite.text = markerXml.@idSite;
        //paramètre la requête pour récupérer le bon fichier xml
		srvEtatDiag.cancel();
		var params:Object = new Object();
		params.f = "GetStatEtatDiag";
		params.id = markerXml.@idRub;
		params.site = markerXml.@idSite;
		trace ("showStat:srvEtatDiag.url="+srvEtatDiag.url+"?f="+params.f+"&id="+params.id+"&site="+params.site);
		srvEtatDiag.send(params);
     }

     private function showListeDiag(idSite:String, idRub:String, idDoc:String):void {
        //affiche la box des stats
        chartBox.visible=true;
        chartBox.width=400;
        //paramètre la requête pour récupérer le bon fichier xml
		srvEtatDiagListe.cancel();
		var params:Object = new Object();
		params.f = "GetFlexEtatDiagListe";
		params.id = idRub;
		params.site = idSite;
		params.idDoc = idDoc;
		
		chartTrace.text += "\n" + srvEtatDiagListe.url+"?f="+params.f+"&id="+params.id+"&site="+params.site+"&idDoc="+params.idDoc;
		srvEtatDiagListe.send(params);
     }

    private function toggleCategory(type:String):void {
       for (var i:Number = 0; i < categories[type].markers.length; i++) {
         var marker:Marker = categories[type].markers[i];
         if (!marker.visible) {
           marker.visible = true;
         } else {
           marker.visible = false;
         }
       } 
		CursorManager.removeBusyCursor();
	}

