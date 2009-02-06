// ActionScript file
	//pour googlemap sdk
      import com.google.maps.InfoWindowOptions;
      import com.google.maps.LatLng;
      import com.google.maps.Map;
      import com.google.maps.MapEvent;
      import com.google.maps.MapMouseEvent;
      import com.google.maps.controls.ZoomControl;
      import com.google.maps.overlays.Marker;
      import com.google.maps.overlays.MarkerOptions;
      import com.google.maps.styles.FillStyle;
      import com.google.maps.styles.StrokeStyle;
	import com.google.maps.overlays.GroundOverlay;
    import com.google.maps.overlays.GroundOverlayOptions;
    import com.google.maps.MapType;
    import com.google.maps.LatLngBounds;
    import com.google.maps.controls.ZoomControl;
    import com.google.maps.controls.MapTypeControl;
      
    import flash.display.Loader;
    import flash.display.LoaderInfo;
    import flash.net.URLRequest;

      import compo.twEtatDiagListe;
      import compo.twPhotoListe;
      
      import mx.managers.CursorManager;
      import mx.managers.PopUpManager;
      import mx.rpc.events.ResultEvent;

      private var map:Map;
      private var markers:XMLList;



    /*prod
    [Bindable] private var urlExeAjax:String="http://www.onadabase.eu/library/php/ExeAjax.php";
    private var urlAllEtatDiag:String="http://www.onadabase.eu/bdd/carto/allEtatDiag_centre_.xml";
	private var mapKey:String = "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRQPuSe5bSrCkW0z0AK5OduyCmU7hRSB6XyMSlG4GUuaIVi6tnDRGuEsWw";
	*/
	//local
    [Bindable] private var urlExeAjax:String="http://localhost/onadabase/library/php/ExeAjax.php";
	private var mapKey:String = "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRSAqqCYJRNRYB52nvFZykN9ZY0cdhRvfhvUr_7t7Rz5_XNkPGDb_GYlQA";
    private var urlAllEtatDiag:String="http://localhost/onadabase/bdd/carto/allEtatDiag_local2_.xml";
	
	[Bindable]
	private var rsEtatDiag:Object;

	[Embed(source="Gare_de_St_Pierre_des_Corps_2.png")] private var santaWorkshop:Class;

      [Embed(source="A.png")] [Bindable] private var AIcon:Class;
      [Embed(source="B.png")] [Bindable] private var BIcon:Class;
      [Embed(source="C.png")] [Bindable] private var CIcon:Class;
      [Embed(source="D.png")] [Bindable] private var DIcon:Class;
      [Embed(source="E.png")] [Bindable] private var EIcon:Class;
      [Embed(source="jpg.png")] [Bindable] private var PhotoIcon:Class;
      [Embed(source="mpg.png")] [Bindable] private var FilmIcon:Class;
      [Embed(source="mp3.png")] [Bindable] private var SonIcon:Class;
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
            var strHandi:String = "";
            var niv:String = "";
            var titreSelect:String = "";
            //construction de l'identifiant du doc
            //cf. library/php/ExeAjax.php?f=GetEtatDiagListe&id=5610&idDoc=0_audio
            for (var i:int=0; i<allSeries.length; i++) {
                chartTrace.text += "\n" + allSeries[i].id + 
                    " Selected Items: " + allSeries[i].selectedIndices;
				//le type de handicap
                switch (allSeries[i].selectedIndices[0]) {
				    case 0:
				        strHandi = "_moteur";
				        break;
				    case 1:
				        strHandi = "_audio";
				        break;
				    case 2:
				        strHandi = "_cog";
				        break;
				    case 3:
				        strHandi = "_visu";
				        break;
				}				    
                //le niveau seulemet si on a récupéré  l'indice
                if(strHandi!="" && niv==""){
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
					//création du titre de la sélection
					titreSelect = allSeries[i].legendData[0].label;	
                }
            }
            idDoc = niv+strHandi; 
            //exécute la requête
            ShowListeDiag(idSite.text, idRub.text, idDoc, titreSelect);
        }

		public function rhEtatDiag(event:ResultEvent):void {
			rsEtatDiag = event.result;
			if(rsEtatDiag.toString()!=""){
				//mise à jour des icones handicateur
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
				//mise à jour des icones médias
        		imgPhoto.visible=false;
        		imgFilm.visible=false;
        		imgSon.visible=false;
		        for each (var ico:Object in rsEtatDiag.EtatDiag.icones[1].icone)
		        {
		        	//bug dans le cas où il n'y a qu'une icone
			        /*
			        if(ico is String){
				        typeIco = ico.toString();		        	
			        }else{
				        typeIco = ico.id;		        	
			        }
			        */
		        	
		        	if(ico.id=="images")
		        		imgPhoto.visible=true;
		        	if(ico.id=="videos")
		        		imgFilm.visible=true;
		        	if(ico.id=="sons")
		        		imgSon.visible=true;
		        }
		 	}
		}
        
      public function onHolderCreated(event:Event):void {
        map = new Map();
        map.key = mapKey;
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
        getXml();
     }
     
     public function getXml():void {
         var xmlString:URLRequest = new URLRequest(urlAllEtatDiag);
         var xmlLoader:URLLoader = new URLLoader(xmlString);
         xmlLoader.addEventListener("complete", readXml);
    }
/*
	public function getPlan():void{
        var testLoader:Loader = new Loader();
        var urlRequest:URLRequest = new URLRequest("http://www.onadabase.eu/centre/spip/IMG/png/Gare_de_St_Pierre_des_Corps_2.png");
        testLoader.contentLoaderInfo.addEventListener(Event.COMPLETE, function(e:Event):void {
            var groundOverlay:GroundOverlay = new GroundOverlay(
                testLoader,
                new LatLngBounds(new LatLng(47.12995076, 1.00001335), new LatLng(48, 2)));
            map.addOverlay(groundOverlay);
        });
        testLoader.load(urlRequest);  		
	}      
*/

    private function getPlan():void {
        map.setCenter(new LatLng(40.740, -74.18), 12, MapType.NORMAL_MAP_TYPE);
        map.addControl(new ZoomControl());
        map.addControl(new MapTypeControl());
        /*
        var testLoader:Loader = new Loader();
        var urlRequest:URLRequest = new URLRequest("http://www.onadabase.eu/centre/spip/IMG/png/Gare_de_St_Pierre_des_Corps_2.png");
        testLoader.contentLoaderInfo.addEventListener(Event.COMPLETE, function(e:Event):void {
            var groundOverlay:GroundOverlay = new GroundOverlay(
                testLoader,
                new LatLngBounds(new LatLng(40.716216,-74.213393), new LatLng(40.765641,-74.139235)));
            map.addOverlay(groundOverlay);
        });
        testLoader.load(urlRequest);
        */
        var groundOverlay:GroundOverlay = new GroundOverlay(
                new santaWorkshop(),
                new LatLngBounds(new LatLng(40.716216,-74.213393), new LatLng(40.765641,-74.139235)));
            map.addOverlay(groundOverlay);
          
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

     private function ShowListeDiag(idSite:String, idRub:String, idDoc:String, titreSelect:String):void {

        // Create a non-modal TitleWindow container.
        var wListe:twEtatDiagListe = twEtatDiagListe(
            PopUpManager.createPopUp(this, twEtatDiagListe, false));
            
		var params:Object = new Object();
		params.f = "GetFlexEtatDiagListe";
		params.id = idRub;
		params.site = idSite;
		params.idDoc = idDoc;

		titreSelect = chartTitre.text+" : "+titreSelect;

		wListe.useHttpService(urlExeAjax,params,titreSelect);

        PopUpManager.centerPopUp(wListe);
		
		chartTrace.text += "\nShowListeDiag" +urlExeAjax+"?f="+params.f+"&id="+params.id+"&site="+params.site+"&idDoc="+params.idDoc;
     }

     private function ShowListePhoto():void {
        // Create a non-modal TitleWindow container.
        var wPhotoListe:twPhotoListe = twPhotoListe(
            PopUpManager.createPopUp(this, twPhotoListe, false));

        PopUpManager.centerPopUp(wPhotoListe);

     }

     private function ShowListeFilm():void {

     }
     private function ShowListeSon():void {

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

