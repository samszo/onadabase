package
{
	import com.google.maps.overlays.GroundOverlay;
    import com.google.maps.LatLng;
    import com.google.maps.LatLngBounds;
	
	public class Plans
	{
		public function Plans()
		{
		}

[Embed(source="/plans/7041_47431_17-12-08_14-14-27.png")] public var plan_0:Class;
[Embed(source="/plans/7055_47452_17-12-08_15-18-44.png")] public var plan_1:Class;
[Embed(source="/plans/Gare_de_Blois_2.png")] public var plan_2:Class;
[Embed(source="/plans/7105_47646_19-12-08_12-42-27.png")] public var plan_3:Class;
[Embed(source="/plans/7096_47628_19-12-08_11-05-17.png")] public var plan_4:Class;
[Embed(source="/plans/7103_47642_19-12-08_12-13-42.png")] public var plan_5:Class;
[Embed(source="/plans/Gare_de_Amboise_2.png")] public var plan_6:Class;
[Embed(source="/plans/7083_47592_18-12-08_12-10-42.png")] public var plan_7:Class;
[Embed(source="/plans/7091_47618_18-12-08_17-23-15.png")] public var plan_8:Class;
[Embed(source="/plans/7090_47616_18-12-08_17-21-49.png")] public var plan_9:Class;
[Embed(source="/plans/7043_47434_17-12-08_14-32-45.png")] public var plan_10:Class;
[Embed(source="/plans/Gare_de_Beaugency_2.png")] public var plan_11:Class;
[Embed(source="/plans/7039_47428_17-12-08_11-41-23.png")] public var plan_12:Class;
[Embed(source="/plans/7045_47437_17-12-08_14-38-51.png")] public var plan_13:Class;
[Embed(source="/plans/7093_47622_18-12-08_17-48-53.png")] public var plan_14:Class;
[Embed(source="/plans/7094_47624_18-12-08_18-01-19.png")] public var plan_15:Class;
//[Embed(source="/plans/7098_47632_19-12-08_11-25-30.png")] public var plan_6066:Class;
[Embed(source="/plans/7047_47440_17-12-08_14-41-50.png")] public var plan_16:Class;
[Embed(source="/plans/7099_47634_19-12-08_11-50-13.png")] public var plan_17:Class;
[Embed(source="/plans/7101_47638_19-12-08_12-02-28.png")] public var plan_18:Class;
[Embed(source="/plans/Gare_de_Courville_sur_Eure_2.png")] public var plan_19:Class;
[Embed(source="/plans/Gare_de_Epernon_2.png")] public var plan_20:Class;
[Embed(source="/plans/7104_47644_19-12-08_12-28-32.png")] public var plan_21:Class;
[Embed(source="/plans/Gare_de_Chartres_2.png")] public var plan_22:Class;
[Embed(source="/plans/Gare_de_St_Pierre_des_Corps_2.png")] public var plan_23:Class;
[Embed(source="/plans/Gare_de_Joue_les_Tours_2.png")] public var plan_24:Class;
[Embed(source="/plans/Gare_de_Jouy_2.png")] public var plan_25:Class;
[Embed(source="/plans/7049_47443_17-12-08_14-45-07.png")] public var plan_26:Class;
[Embed(source="/plans/Gare_de_la_Loupe_2.png")] public var plan_27:Class;
[Embed(source="/plans/7053_47449_17-12-08_15-12-23.png")] public var plan_28:Class;
[Embed(source="/plans/Gare_de_Loches_2.png")] public var plan_29:Class;
[Embed(source="/plans/Gare_de_Maintenon_2.png")] public var plan_30:Class;
[Embed(source="/plans/Gare_de_Mer_2.png")] public var plan_31:Class;
[Embed(source="/plans/Gare_de_Meung_sur_Loire_2.png")] public var plan_32:Class;
[Embed(source="/plans/7107_47650_19-12-08_13-03-40.png")] public var plan_33:Class;
[Embed(source="/plans/7057_47455_17-12-08_15-26-23.png")] public var plan_34:Class;
[Embed(source="/plans/Gare_de_Nogent_le_Rotrou_2.png")] public var plan_35:Class;
[Embed(source="/plans/7059_47458_18-12-08_15-55-28.png")] public var plan_36:Class;
[Embed(source="/plans/Gare_de_Onzain_2.png")] public var plan_37:Class;
[Embed(source="/plans/Gare_de_Orleans_2.png")] public var plan_38:Class;
[Embed(source="/plans/7085_47596_18-12-08_12-23-07.png")] public var plan_39:Class;
[Embed(source="/plans/7087_47601_18-12-08_14-16-31.png")] public var plan_40:Class;
[Embed(source="/plans/7062_47463_17-12-08_15-40-12.png")] public var plan_41:Class;
[Embed(source="/plans/Gare_de_St_Piat_2.png")] public var plan_42:Class;
[Embed(source="/plans/7064_47467_17-12-08_15-54-35.png")] public var plan_43:Class;
[Embed(source="/plans/Gare_de_Tours_2-2.png")] public var plan_44:Class;
[Embed(source="/plans/Gare_de_Tours_2.png")] public var plan_45:Class;
[Embed(source="/plans/7110_47656_19-12-08_17-20-29.png")] public var plan_46:Class;
[Embed(source="/plans/Gare_de_Vendome_TER_2.png")] public var plan_47:Class;
[Embed(source="/plans/Gare_de_Vendome_TGV_RDC_2.png")] public var plan_48:Class;
[Embed(source="/plans/Gare_de_Vendome_TGV_R1_2.png")] public var plan_49:Class;
[Embed(source="/plans/7066_47470_17-12-08_15-59-09.png")] public var plan_50:Class;
[Embed(source="/plans/gare_de_Villette_St_Prest_2.png")] public var plan_51:Class;

public function getPlan(nom:String):GroundOverlay{
		   	var groundOverlay:GroundOverlay;
			switch (nom) {
case 'plan_6015':
					groundOverlay = new GroundOverlay(
		                new plan_0,
		                new LatLngBounds(new LatLng(48.38308129111574,0.8480491366530174), new LatLng(48.38383763440669,0.8493341923430172)));
					 break;
case 'plan_6195':
					groundOverlay = new GroundOverlay(
		                new plan_1,
		                new LatLngBounds(new LatLng(47.64455879025939,1.404997275276167), new LatLng(47.64640113398664,1.407582830656281)));
					 break;
case 'plan_6246':
					groundOverlay = new GroundOverlay(
		                new plan_2,
		                new LatLngBounds(new LatLng(47.58442523835277,1.322918487915099), new LatLng(47.58627973271243,1.324797763837897)));
					 break;
case 'plan_6457':
					groundOverlay = new GroundOverlay(
		                new plan_3,
		                new LatLngBounds(new LatLng(47.33844432591863,0.6526902076884227), new LatLng(47.33910846140172,0.6533925789695019)));
					 break;
case 'plan_6472':
					groundOverlay = new GroundOverlay(
		                new plan_3,
		                new LatLngBounds(new LatLng(47.33844432591863,0.6526902076884227), new LatLng(47.33910846140172,0.6533925789695019)));
					 break;
case 'plan_6472':
					groundOverlay = new GroundOverlay(
		                new plan_3,
		                new LatLngBounds(new LatLng(47.33844432591863,0.6526902076884227), new LatLng(47.33910846140172,0.6533925789695019)));
					 break;
case 'plan_6472':
					groundOverlay = new GroundOverlay(
		                new plan_3,
		                new LatLngBounds(new LatLng(47.33844432591863,0.6526902076884227), new LatLng(47.33910846140172,0.6533925789695019)));
					 break;
case 'plan_6495':
					groundOverlay = new GroundOverlay(
		                new plan_4,
		                new LatLngBounds(new LatLng(47.26240341034737,0.8336013124536436), new LatLng(47.26265185456641,0.834429629312845)));
					 break;
case 'plan_6533':
					groundOverlay = new GroundOverlay(
		                new plan_5,
		                new LatLngBounds(new LatLng(47.28401476164848,0.7813491366924671), new LatLng(47.28454671310663,0.7820635412332848)));
					 break;
case 'plan_2725':
					groundOverlay = new GroundOverlay(
		                new plan_6,
		                new LatLngBounds(new LatLng(47.42111482085733,0.9802159614848662), new LatLng(47.42193435120101,0.9818073971650704)));
					 break;
case 'plan_2727':
					groundOverlay = new GroundOverlay(
		                new plan_6,
		                new LatLngBounds(new LatLng(47.42111482085733,0.9802159614848662), new LatLng(47.42193435120101,0.9818073971650704)));
					 break;
case 'plan_6594':
					groundOverlay = new GroundOverlay(
		                new plan_6,
		                new LatLngBounds(new LatLng(47.42111482085733,0.9802159614848662), new LatLng(47.42193435120101,0.9818073971650704)));
					 break;
case 'plan_2662':
					groundOverlay = new GroundOverlay(
		                new plan_6,
		                new LatLngBounds(new LatLng(47.42111482085733,0.9802159614848662), new LatLng(47.42193435120101,0.9818073971650704)));
					 break;
case 'plan_6051':
					groundOverlay = new GroundOverlay(
		                new plan_7,
		                new LatLngBounds(new LatLng(48.43782939017291,1.387574536757896), new LatLng(48.43872292582429,1.390809964127371)));
					 break;
case 'plan_6059':
					groundOverlay = new GroundOverlay(
		                new plan_8,
		                new LatLngBounds(new LatLng(47.20904135599627,0.9460182724657995), new LatLng(47.20937310155811,0.9462761228876747)));
					 break;
case 'plan_6058':
					groundOverlay = new GroundOverlay(
		                new plan_9,
		                new LatLngBounds(new LatLng(47.21365569379175,0.9413148703894033), new LatLng(47.21403335000656,0.9418768831400134)));
					 break;
case 'plan_6017':
					groundOverlay = new GroundOverlay(
		                new plan_10,
		                new LatLngBounds(new LatLng(47.81157202941269,1.667900438166643), new LatLng(47.81240590324604,1.669017228604074)));
					 break;
case 'plan_3209':
					groundOverlay = new GroundOverlay(
		                new plan_11,
		                new LatLngBounds(new LatLng(47.77784753983255,1.62519609575898), new LatLng(47.77880315260683,1.62631887753065)));
					 break;
case 'plan_3186':
					groundOverlay = new GroundOverlay(
		                new plan_11,
		                new LatLngBounds(new LatLng(47.77784753983255,1.62519609575898), new LatLng(47.77880315260683,1.62631887753065)));
					 break;
case 'plan_3187':
					groundOverlay = new GroundOverlay(
		                new plan_11,
		                new LatLngBounds(new LatLng(47.77784753983255,1.62519609575898), new LatLng(47.77880315260683,1.62631887753065)));
					 break;
case 'plan_3450':
					groundOverlay = new GroundOverlay(
		                new plan_2,
		                new LatLngBounds(new LatLng(47.58442523835277,1.322918487915099), new LatLng(47.58627973271243,1.324797763837897)));
					 break;
case 'plan_6013':
					groundOverlay = new GroundOverlay(
		                new plan_12,
		                new LatLngBounds(new LatLng(48.4323671214258,0.8914723255213314), new LatLng(48.43341352912997,0.8938942659075654)));
					 break;
case 'plan_6019':
					groundOverlay = new GroundOverlay(
		                new plan_13,
		                new LatLngBounds(new LatLng(47.87680385341671,1.781607105301376), new LatLng(47.87814071205318,1.784185682797528)));
					 break;
case 'plan_6061':
					groundOverlay = new GroundOverlay(
		                new plan_14,
		                new LatLngBounds(new LatLng(47.18159016501799,0.9648057569724011), new LatLng(47.18223028859649,0.9656644593479998)));
					 break;
case 'plan_6062':
					groundOverlay = new GroundOverlay(
		                new plan_15,
		                new LatLngBounds(new LatLng(47.18220513505061,0.9676059555884756), new LatLng(47.18256355519461,0.9684052847395102)));
					 break;
case 'plan_6021':
					groundOverlay = new GroundOverlay(
		                new plan_16,
		                new LatLngBounds(new LatLng(47.51957463282492,1.246783496506836), new LatLng(47.52067677274997,1.248330189955885)));
					 break;
case 'plan_6015':
					groundOverlay = new GroundOverlay(
		                new plan_0,
		                new LatLngBounds(new LatLng(48.38308129111574,0.8480491366530174), new LatLng(48.38383763440669,0.8493341923430172)));
					 break;
case 'plan_6067':
					groundOverlay = new GroundOverlay(
		                new plan_17,
		                new LatLngBounds(new LatLng(47.26791324143794,0.8378843121467803), new LatLng(47.26801764628449,0.8383902610604146)));
					 break;
case 'plan_6064':
					groundOverlay = new GroundOverlay(
		                new plan_4,
		                new LatLngBounds(new LatLng(47.26240341034737,0.8336013124536436), new LatLng(47.26265185456641,0.834429629312845)));
					 break;
case 'plan_6069':
					groundOverlay = new GroundOverlay(
		                new plan_18,
		                new LatLngBounds(new LatLng(47.23739049363253,0.8670045221957858), new LatLng(47.23805726606867,0.868305503727057)));
					 break;
case 'plan_2503':
					groundOverlay = new GroundOverlay(
		                new plan_19,
		                new LatLngBounds(new LatLng(48.45071755334812,1.234358025081316), new LatLng(48.4516673837535,1.236008081953486)));
					 break;
case 'plan_2477':
					groundOverlay = new GroundOverlay(
		                new plan_19,
		                new LatLngBounds(new LatLng(48.45071755334812,1.234358025081316), new LatLng(48.4516673837535,1.236008081953486)));
					 break;
case 'plan_2478':
					groundOverlay = new GroundOverlay(
		                new plan_19,
		                new LatLngBounds(new LatLng(48.45071755334812,1.234358025081316), new LatLng(48.4516673837535,1.236008081953486)));
					 break;
case 'plan_2284':
					groundOverlay = new GroundOverlay(
		                new plan_20,
		                new LatLngBounds(new LatLng(48.60474011510925,1.680759511797069), new LatLng(48.60597520226411,1.682076705556492)));
					 break;
case 'plan_6072':
					groundOverlay = new GroundOverlay(
		                new plan_21,
		                new LatLngBounds(new LatLng(47.28747020238335,0.8141979981096624), new LatLng(47.28782839251627,0.8147914085904091)));
					 break;
case 'plan_6071':
					groundOverlay = new GroundOverlay(
		                new plan_5,
		                new LatLngBounds(new LatLng(47.28401476164848,0.7813491366924671), new LatLng(47.28454671310663,0.7820635412332848)));
					 break;
case 'plan_2376':
					groundOverlay = new GroundOverlay(
		                new plan_22,
		                new LatLngBounds(new LatLng(48.4474771615339,1.480081324961554), new LatLng(48.44885026223105,1.481831932733946)));
					 break;
case 'plan_2367':
					groundOverlay = new GroundOverlay(
		                new plan_22,
		                new LatLngBounds(new LatLng(48.4474771615339,1.480081324961554), new LatLng(48.44885026223105,1.481831932733946)));
					 break;
case 'plan_2359':
					groundOverlay = new GroundOverlay(
		                new plan_22,
		                new LatLngBounds(new LatLng(48.4474771615339,1.480081324961554), new LatLng(48.44885026223105,1.481831932733946)));
					 break;
case 'plan_2907':
					groundOverlay = new GroundOverlay(
		                new plan_23,
		                new LatLngBounds(new LatLng(47.38516253377669,0.7226803418767407), new LatLng(47.38681890806328,0.7271985843819221)));
					 break;
case 'plan_2889':
					groundOverlay = new GroundOverlay(
		                new plan_23,
		                new LatLngBounds(new LatLng(47.38516253377669,0.7226803418767407), new LatLng(47.38681890806328,0.7271985843819221)));
					 break;
case 'plan_2734':
					groundOverlay = new GroundOverlay(
		                new plan_24,
		                new LatLngBounds(new LatLng(47.35398844643157,0.6667994510317367), new LatLng(47.35436833309684,0.6681061551951086)));
					 break;
case 'plan_2219':
					groundOverlay = new GroundOverlay(
		                new plan_25,
		                new LatLngBounds(new LatLng(48.50990403315119,1.557302906921802), new LatLng(48.51028549113384,1.557909920056304)));
					 break;
case 'plan_2204':
					groundOverlay = new GroundOverlay(
		                new plan_25,
		                new LatLngBounds(new LatLng(48.50988800681145,1.557278478908052), new LatLng(48.51028629391151,1.557938426868697)));
					 break;
case 'plan_6023':
					groundOverlay = new GroundOverlay(
		                new plan_26,
		                new LatLngBounds(new LatLng(47.89262887391367,1.826059737657692), new LatLng(47.89327754552865,1.827589169077891)));
					 break;
case 'plan_2433':
					groundOverlay = new GroundOverlay(
		                new plan_27,
		                new LatLngBounds(new LatLng(48.4734571841697,1.009854451554914), new LatLng(48.474717233337,1.01229988034659)));
					 break;
case 'plan_2410':
					groundOverlay = new GroundOverlay(
		                new plan_27,
		                new LatLngBounds(new LatLng(48.4734571841697,1.009854451554914), new LatLng(48.474717233337,1.01229988034659)));
					 break;
case 'plan_2411':
					groundOverlay = new GroundOverlay(
		                new plan_27,
		                new LatLngBounds(new LatLng(48.4734571841697,1.009854451554914), new LatLng(48.474717233337,1.01229988034659)));
					 break;
case 'plan_6027':
					groundOverlay = new GroundOverlay(
		                new plan_28,
		                new LatLngBounds(new LatLng(47.44965626428826,1.048834461168043), new LatLng(47.45082648657903,1.051256294727608)));
					 break;
case 'plan_2608':
					groundOverlay = new GroundOverlay(
		                new plan_29,
		                new LatLngBounds(new LatLng(47.12964937410879,0.9997725005194126), new LatLng(47.13199078896262,1.000992329057194)));
					 break;
case 'plan_2097':
					groundOverlay = new GroundOverlay(
		                new plan_30,
		                new LatLngBounds(new LatLng(48.58446165350303,1.590804631046768), new LatLng(48.58688088138365,1.59255388868915)));
					 break;
case 'plan_2084':
					groundOverlay = new GroundOverlay(
		                new plan_30,
		                new LatLngBounds(new LatLng(48.58446165350303,1.590804631046768), new LatLng(48.58688088138365,1.59255388868915)));
					 break;
case 'plan_3089':
					groundOverlay = new GroundOverlay(
		                new plan_31,
		                new LatLngBounds(new LatLng(47.70543833262428,1.505678465760082), new LatLng(47.70646573880406,1.507408930925452)));
					 break;
case 'plan_3037':
					groundOverlay = new GroundOverlay(
		                new plan_31,
		                new LatLngBounds(new LatLng(47.70537650000147,1.505602107635828), new LatLng(47.70656454699518,1.507650860650169)));
					 break;
case 'plan_3120':
					groundOverlay = new GroundOverlay(
		                new plan_32,
		                new LatLngBounds(new LatLng(47.82922017475927,1.690537073518815), new LatLng(47.83061585122481,1.692968499036904)));
					 break;
case 'plan_3121':
					groundOverlay = new GroundOverlay(
		                new plan_32,
		                new LatLngBounds(new LatLng(47.82922017475927,1.690537073518815), new LatLng(47.83061585122481,1.692968499036904)));
					 break;
case 'plan_6076':
					groundOverlay = new GroundOverlay(
		                new plan_33,
		                new LatLngBounds(new LatLng(47.29151207516452,0.7207882951591159), new LatLng(47.29204838618288,0.7216229186126326)));
					 break;
case 'plan_6075':
					groundOverlay = new GroundOverlay(
		                new plan_33,
		                new LatLngBounds(new LatLng(47.29151207516452,0.7207882951591159), new LatLng(47.29204838618288,0.7216229186126326)));
					 break;
case 'plan_6031':
					groundOverlay = new GroundOverlay(
		                new plan_34,
		                new LatLngBounds(new LatLng(47.39089234424507,0.8165444550919981), new LatLng(47.39195240529024,0.8177116585386437)));
					 break;
case 'plan_5512':
					groundOverlay = new GroundOverlay(
		                new plan_35,
		                new LatLngBounds(new LatLng(48.32517245202665,0.8093119312552338), new LatLng(48.32629823481284,0.8110165477302048)));
					 break;
case 'plan_5511':
					groundOverlay = new GroundOverlay(
		                new plan_35,
		                new LatLngBounds(new LatLng(48.32517245202665,0.8093119312552338), new LatLng(48.32629823481284,0.8110165477302048)));
					 break;
case 'plan_5944':
					groundOverlay = new GroundOverlay(
		                new plan_35,
		                new LatLngBounds(new LatLng(48.32517245202665,0.8093119312552338), new LatLng(48.32629823481284,0.8110165477302048)));
					 break;
case 'plan_5510':
					groundOverlay = new GroundOverlay(
		                new plan_35,
		                new LatLngBounds(new LatLng(48.32517245202665,0.8093119312552338), new LatLng(48.32629823481284,0.8110165477302048)));
					 break;
case 'plan_6033':
					groundOverlay = new GroundOverlay(
		                new plan_36,
		                new LatLngBounds(new LatLng(47.40944819694087,0.8948451105438362), new LatLng(47.4100899129705,0.897207307969353)));
					 break;
case 'plan_3529':
					groundOverlay = new GroundOverlay(
		                new plan_37,
		                new LatLngBounds(new LatLng(47.49144953819061,1.184733467794359), new LatLng(47.49231148804532,1.186862696743508)));
					 break;
case 'plan_3462':
					groundOverlay = new GroundOverlay(
		                new plan_37,
		                new LatLngBounds(new LatLng(47.49144953819061,1.184733467794359), new LatLng(47.49231148804532,1.186862696743508)));
					 break;
case 'plan_3245':
					groundOverlay = new GroundOverlay(
		                new plan_38,
		                new LatLngBounds(new LatLng(47.9073825800248,1.904124433121151), new LatLng(47.90936171866068,1.905634893172422)));
					 break;
case 'plan_6053':
					groundOverlay = new GroundOverlay(
		                new plan_39,
		                new LatLngBounds(new LatLng(48.47729517736106,1.140010426759194), new LatLng(48.47860354612837,1.142296422961163)));
					 break;
case 'plan_6055':
					groundOverlay = new GroundOverlay(
		                new plan_40,
		                new LatLngBounds(new LatLng(48.4473349190946,1.331390608204544), new LatLng(48.44797021296303,1.333010048220189)));
					 break;
case 'plan_6036':
					groundOverlay = new GroundOverlay(
		                new plan_41,
		                new LatLngBounds(new LatLng(47.86256415770597,1.748887756723635), new LatLng(47.86364571021544,1.750826940807541)));
					 break;
case 'plan_2200':
					groundOverlay = new GroundOverlay(
		                new plan_42,
		                new LatLngBounds(new LatLng(48.54272759322452,1.590013266444165), new LatLng(48.54453242490229,1.590901529475995)));
					 break;
case 'plan_2158':
					groundOverlay = new GroundOverlay(
		                new plan_42,
		                new LatLngBounds(new LatLng(48.54272759322452,1.590013266444165), new LatLng(48.54453242490229,1.590901529475995)));
					 break;
case 'plan_2159':
					groundOverlay = new GroundOverlay(
		                new plan_42,
		                new LatLngBounds(new LatLng(48.54272759322452,1.590013266444165), new LatLng(48.54453242490229,1.590901529475995)));
					 break;
case 'plan_6038':
					groundOverlay = new GroundOverlay(
		                new plan_43,
		                new LatLngBounds(new LatLng(47.67979057674727,1.45317335928728), new LatLng(47.68147327056917,1.455883611001365)));
					 break;
case 'plan_2883':
					groundOverlay = new GroundOverlay(
		                new plan_44,
		                new LatLngBounds(new LatLng(47.38789190283644,0.6924604976295901), new LatLng(47.39051716048903,0.6957447667653725)));
					 break;
case 'plan_2875':
					groundOverlay = new GroundOverlay(
		                new plan_44,
		                new LatLngBounds(new LatLng(47.38789190283644,0.6924604976295901), new LatLng(47.39051716048903,0.6957447667653725)));
					 break;
case 'plan_2778':
					groundOverlay = new GroundOverlay(
		                new plan_45,
		                new LatLngBounds(new LatLng(47.38820108648448,0.6924823259288218), new LatLng(47.39044726689901,0.6955784174564864)));
					 break;
case 'plan_6079':
					groundOverlay = new GroundOverlay(
		                new plan_46,
		                new LatLngBounds(new LatLng(47.29096367217972,0.7344087512626238), new LatLng(47.29187329375431,0.7355295633914347)));
					 break;
case 'plan_6078':
					groundOverlay = new GroundOverlay(
		                new plan_46,
		                new LatLngBounds(new LatLng(47.29096367217972,0.7344087512626238), new LatLng(47.29187329375431,0.7355295633914347)));
					 break;
case 'plan_3347':
					groundOverlay = new GroundOverlay(
		                new plan_47,
		                new LatLngBounds(new LatLng(47.80154395246365,1.066530836738612), new LatLng(47.80260896683594,1.069233264143985)));
					 break;
case 'plan_3346':
					groundOverlay = new GroundOverlay(
		                new plan_47,
		                new LatLngBounds(new LatLng(47.80154395246365,1.066530836738612), new LatLng(47.80260896683594,1.069233264143985)));
					 break;
case 'plan_3602':
					groundOverlay = new GroundOverlay(
		                new plan_48,
		                new LatLngBounds(new LatLng(47.82080593635406,1.020275108592526), new LatLng(47.82266034739796,1.02305420170359)));
					 break;
case 'plan_3534':
					groundOverlay = new GroundOverlay(
		                new plan_48,
		                new LatLngBounds(new LatLng(47.82080593635406,1.020275108592526), new LatLng(47.82266034739796,1.02305420170359)));
					 break;
case 'plan_3614':
					groundOverlay = new GroundOverlay(
		                new plan_49,
		                new LatLngBounds(new LatLng(47.82095553482863,1.019803788501856), new LatLng(47.82317776467416,1.021426298728249)));
					 break;
case 'plan_3537':
					groundOverlay = new GroundOverlay(
		                new plan_49,
		                new LatLngBounds(new LatLng(47.82095553482863,1.019803788501856), new LatLng(47.82317776467416,1.021426298728249)));
					 break;
case 'plan_6040':
					groundOverlay = new GroundOverlay(
		                new plan_50,
		                new LatLngBounds(new LatLng(47.47515183403376,1.122851202054473), new LatLng(47.47611020339182,1.12439873942532)));
					 break;
case 'plan_1953':
					groundOverlay = new GroundOverlay(
		                new plan_51,
		                new LatLngBounds(new LatLng(48.48165558210019,1.522389124688884), new LatLng(48.4827400278142,1.524369358526914)));
					 break;
			}
			return groundOverlay;


		}

	}
}