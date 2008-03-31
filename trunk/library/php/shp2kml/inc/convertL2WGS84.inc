<?php
function ALG0001($phi,$e)
{
	$temp = ( 1 - ( $e * sin( $phi ) ) ) / ( 1 + ( $e * sin( $phi ) ) );

	$L = log ( tan ( (pi()/4) + ($phi/2) ) * pow ($temp, ($e/2) ));

	return $L;
}


function ALG0002($L,$e,$epsilon)
{
	$phi[0] = 2 * atan(exp($L)) - (pi()/2);

	$i=0;
	do
	{
		$i++;
		$temp = ( 1 + ( $e * sin( $phi[$i-1] ) ) ) / ( 1 - ( $e * sin( $phi[$i-1] ) ) );
		$phi[$i] = 2 * atan ( pow ($temp, ($e/2)) * exp ($L) ) - pi()/2;
	}
	while (abs($phi[$i] - $phi[$i - 1]) >= $epsilon);

	return $phi[$i];
}

function ALG0004($X,$Y,$n,$c,$Xs,$Ys,$lambdac,$e,$epsilon)
{
	$R = sqrt( pow(($X - $Xs),2) + pow(($Y - $Ys),2) );
	$gamma = atan(($X - $Xs)/($Ys - $Y));

	$lambda = $lambdac + ($gamma / $n);

	$L = (-1 / $n) * log(abs($R/$c));

	$phi = ALG0002($L,$e,$epsilon);

	$coords['lambda']=$lambda;
	$coords['phi']=$phi;
	
	return $coords;
}

function ALG0009($lambda,$phi,$he,$a,$e)
{
	$N = ALG0021($phi,$a,$e);
	
	$X = ($N + $he) * cos($phi) * cos($lambda);

	$Y = ($N + $he) * cos($phi) * sin($lambda);

	$Z = ($N * (1 - $e*$e)  + $he) * sin ($phi);

	$coords['X']=$X;
	$coords['Y']=$Y;
	$coords['Z']=$Z;
	
	return $coords;
}


function ALG0012($X,$Y,$Z,$a,$e,$epsilon)
{
	$lambda = atan ($Y/$X);

	$P = sqrt($X*$X + $Y*$Y);
	$phi[0] = atan ($Z/ ($P * (1 - ( ($a*$e*$e)/sqrt($X*$X + $Y*$Y + $Z*$Z) ) ) ) );

	$i = 0;
	do
	{
		$i++;
		$temp =  pow((1 - ( $a * $e*$e * cos($phi[$i - 1] )/( $P * sqrt(1 - $e*$e * sin($phi[$i - 1])*sin($phi[$i - 1]))))),-1);
		$phi[$i] = atan( $temp * $Z / $P );
	}
	while (abs($phi[$i] - $phi[$i - 1]) >= $epsilon);

	$phix = $phi[$i];

	$he = ($P/cos($phix)) - ($a/sqrt(1 - $e*$e * sin($phix)*sin($phix)));

	$coords['lambda']=$lambda;
	$coords['phi']=$phix;
	$coords['he']=$he;

	return $coords;
}

function ALG0013($Tx,$Ty,$Tz,$D,$Rx,$Ry,$Rz,$U)
{
	$V['X'] = $Tx + $U['X'] * (1 + $D) + $U['Z'] * $Ry - $U['Y'] * $Rz;
	$V['Y'] = $Ty + $U['Y'] * (1 + $D) + $U['X'] * $Rz - $U['Z'] * $Rx;
	$V['Z'] = $Tz + $U['Z'] * (1 + $D) + $U['Y'] * $Rx - $U['X'] * $Ry;

	return $V;
}

function ALG0019($lambda0,$phi0,$k0,$X0,$Y0,$a,$e)
{
	$lambdac = $lambda0;
	$n = sin($phi0);
	$C = $k0 * ALG0021($phi0,$a,$e) * tan (pi()/2 - $phi0) * exp ( $n * ALG0001($phi0,$e) );
	$Xs = $X0;
	$Ys = $Y0 + $k0 * ALG0021($phi0,$a,$e) * tan (pi()/2 - $phi0) ;

	$tab ['e'] = $e;
	$tab ['n'] = $n;
	$tab ['C'] = $C;
	$tab ['lambdac'] = $lambdac;
	$tab ['Xs'] = $Xs;
	$tab ['Ys'] = $Ys;

	return $tab;

}

function ALG0021($phi,$a,$e)
{
	$N = $a/sqrt( 1 - $e * $e * sin($phi) * sin($phi) );
	return $N;

}

function ALG0054($lambda0,$phi0,$X0,$Y0,$phi1,$phi2,$a,$e)
{
	$lambdac = $lambda0;
	$n = ( (log( (ALG0021($phi2,$a,$e)*cos($phi2))/(ALG0021($phi1,$a,$e)*cos($phi1)) ))/(ALG0001($phi1,$e) - ALG0001($phi2,$e) ));
	$C = ((ALG0021($phi1,$a,$e)* cos($phi1))/$n) * exp($n * ALG0001($phi1,$e));


	if ($phi0 == (pi()/2))
	{
		$Xs = $X0;
		$Ys = $Y0;
	}
	else
	{
			echo ('coucou');
		$Xs = $X0;
		$Ys = $Y0 + $C * exp(-1 * $n * ALG0001($phi0,$e));
	}

	$tab ['e'] = $e;
	$tab ['n'] = $n;
	$tab ['C'] = $C;
	$tab ['lambdac'] = $lambdac;
	$tab ['Xs'] = $Xs;
	$tab ['Ys'] = $Ys;

	return $tab;

}

function Lambert2WGS84($orig,$X,$Y)
{
	$epsilon = 0.00000000001;

	switch ($orig)
	{
		case 'LII' :
			$n = 0.7289686274;
			$c = 11745793.39;
			$Xs = 600000;
			$Ys = 6199695.768;
			$lambdac = 0.04079234433; // pour greenwich

			$e = 0.08248325676; //(première excentricité de l’ellipsoïde Clarke 1880 français)

			$he = 100;
			$a = 6378249.2;

			$Tx = -168;
			$Ty = -60;
			$Tz = +320;
			$D  = 0;
			$Rx = $Ry = $Rz = 0;
		break;
		case 'LIIe' :
			$n = 0.7289686274;
			$c = 11745793.39;
			$Xs = 600000;
			$Ys = 8199695.768;
			$lambdac = 0.04079234433; // pour greenwich

			$e = 0.08248325676; //(première excentricité de l’ellipsoïde Clarke 1880 français)

			$he = 100;
			$a = 6378249.2;

			$Tx = -168;
			$Ty = -60;
			$Tz = +320;
			$D  = 0;
			$Rx = $Ry = $Rz = 0;
		break;
		case 'L93' :
			$n = 0.7256077650;
			$c = 11745255.426;
			$Xs = 700000;
			$Ys = 12655612.050;
			$lambdac = 0.04079234433; // pour greenwich

			$e = 0.08248325676; //(première excentricité de l’ellipsoïde Clarke 1880 français)

			$he = 100;
			$a = 6378249.2;

			$Tx = -168;
			$Ty = -60;
			$Tz = +320;
			$D  = 0;
			$Rx = $Ry = $Rz = 0;
		break;
	}

	$coords = ALG0004($X,$Y,$n,$c,$Xs,$Ys,$lambdac,$e,$epsilon);

	$coords = ALG0009($coords['lambda'],$coords['phi'],$he,$a,$e);
	
	$coords = ALG0013($Tx,$Ty,$Tz,$D,$Rx,$Ry,$Rz,$coords);
	
	$a = 6378137.0;	// ellipsoïdes WGS84
	$f = 1/298.257223563;
	$b = $a*(1-$f);
	$e = sqrt(($a*$a - $b*$b)/($a*$a));

	$X = $coords['X'];
	$Y = $coords['Y'];
	$Z = $coords['Z'];
	$coords = ALG0012($X,$Y,$Z,$a,$e,$epsilon);
	
	$xy['long']  = rad2deg($coords['lambda']);
	$xy['lat']   = rad2deg($coords['phi']);
	return $xy;
}
//
//http://www.ign.fr/telechargement/MPro/geodesie/CIRCE/transfo.pdf
//http://www.ign.fr/telechargement/MPro/geodesie/CIRCE/NTG_80.pdf
//http://www.ign.fr/telechargement/MPro/geodesie/CIRCE/NTG_71.pdf
//
//print_r(Lambert2WGS84('LIIe',591647.56,2426659.65));
?>