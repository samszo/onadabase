$(document).ready(function() { //Waiting for the load of the page
	//Le principe c'est de faire en sorte que l'application reste fonctionnelle au cas où le javascript est désactivé
	//C'est pour ça qu'on change le comportement des applications lors du chargement du javascript
	$('#submitButton').attr("type","button");
	$('#submitButton').attr("onclick","submitForm();");
});

function switchDiv()
{
	
	if($('#link').html()=='-')
	{
		$('#details').slideUp("slow");
		$('#link').html('+');
	}else
	{
		$('#details').slideDown('slow');
		$('#link').html('-');
	}
	
}

function submitForm()
{
	$('#loading').css("display","block");
	$('#myForm').submit();
}