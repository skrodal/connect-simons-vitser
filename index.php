
<?php

/**
 * Simons Vitser
 *
 * Et enkelt demo-API for å demonstrere Dataporten Tjenesteplattform.
 *
 * @author Simon Skrødal
 * @since August 2015
 */ 

// Forventer følgende bruker/passord fra Dataporten Gatekeeper - lagre i en config på et trygt sted:
$apiUser = "dataporten";
$apiPass = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";	// Denne må du redigere, selvsagt...

// Vi snakker JSON
header("content-type: application/json; charset=utf-8");
// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: HEAD, GET, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, X-Requested-With, Origin, Accept, Content-Type");
header("Access-Control-Expose-Headers: Authorization, X-Requested-With, Origin, Accept, Content-Type");

// Connect kjører først en OPTIONS request med Access-Control headers. Godta dette.
if( strcmp($_SERVER["REQUEST_METHOD"], "OPTIONS") == 0) {
	exit(json_encode("Hei Dataporten GK! CORS er OK! Hilsen Simon's Vitser :-)"));
}


// 1. 	La oss først sjekke om forespørsel kommer fra Dataporten GK med riktig bruker/pass
// 		Dersom PHP_AUTH_USER ikke er satt i det hele tatt kan vi like gjerne stoppe her
if (!isset($_SERVER["PHP_AUTH_USER"])) {
    exit(json_encode(array("status" => false, "message" => "HTTP/1.0 401 Unauthorized: Access requires Dataporten API GK Credentials")));
}
 
// 2.	Sjekk at bruker/passord stemmer overens
if( ( strcmp($_SERVER['PHP_AUTH_USER'], $apiUser) !== 0 ) || ( strcmp($_SERVER["PHP_AUTH_PW"], $apiPass) !== 0 ) ) {
	exit(json_encode(array("status" => false, "message" => "HTTP/1.0 401 Unauthorized: Invalid Credentials - " . $apiUser . " :: " . $apiPass)));
}


// 3. 	Vi er inne! 
//		Vitse-APIet er definert i Dashboard med følgende scopes:
//			- Basic					: 	Basic scope følger ikke med som en egen verdi via Connect Gatekeeper
//			- Familievennlig ('0')	: 	gk_simons-vitser_0
//			- 9-årsgrense ('9')		:	gk_simons-vitser_9
//			- 15-årsgrense ('15')	:	gk_simons-vitser_15
//			- 18-årsgrense ('18')	:	gk_simons-vitser_18
//
//		Scopes som kommer inn i header dropper prefiks 'gk_simons-vitser' og vi ender opp med '0', '9', '15', '18'

// La meg dra ut noen av mine beste vitser. 
// Du kan gjerne legge til flere, men vær obs på at jeg har lagt lista rimelig høyt ;-)
$vitser = array(
	// Familievennlig
	"0" => array(
			"Hørt om laksen som mista strømmen?",
			"Har dere hørt om torsken som var lei sei?",
			"Har du hørt om han som hadde bakteriefobi? Han kokte isbitene før han puttet dem i drikken sin :D"
		),
	// Aldersgrense 9 år
	"9" => array(
			"Det er bedre med 10 fulger på taket enn ei ugle i potetmosen.",
			"Hørt om kokken som var så dårlig i fotball? Sleivspark hver eneste gang.",
			"Hørt om elektrikeren som reiste til syden for å koble av...?",
			"Har du hørt om kona som vekket mannen midt på natten? Han hadde jo glemt å ta sovemedisinen sin...",
			"Har du hørt om elektrikeren som aldri fikk noe gjort? Det ble så mye Ohm og men..."
		),
	// Aldersgrense 15 år
	"15" => array(
			"Har du hørt om grisen som hadde svin på skogen?",
			"Visste du at Trine Hattestad er veldig spydig?",
			"Har du hørt om mannen som var så fornøyd med at svigermor bodde kun et steinkast unna? - Før eller siden må han jo treffe...",
			"Har du hørt om når Jesus var med i svømmekonkurranse? - Han vant på 'walk-over'."
		),
	// Aldersgrense 18 år
	"18" => array(
			"Har du hørt om kannibalen som dreit ut broren sin?",
			"Pappa, hva er en transvestitt? - Spør tante Erik..."
		),
	);


// 4. 	Sjekk hvilke scopes (aldersgrense) klienten har tilgang til (ingen eller flere av 0, 9, 15, 18). 
// 		- Dersom ingen scopes utover "basic" vil HTTP_X_FEIDECONNECT_SCOPES være tom - gi da default "0" (familievennlig)
//		- Dersom ett eller flere scopes, konverter den komma-separerte String'en til en array:
$aldersgrenser = empty($_SERVER["HTTP_X_FEIDECONNECT_SCOPES"]) ? array("0") : explode(',', $_SERVER["HTTP_X_FEIDECONNECT_SCOPES"]);

// 5. 	Start jobben med å finne en passende vits iht. godkjente aldersgrenser (scopes)
//		- Først, velg et tilfeldig aldersnivå ut av de scopes klienten har tilgang til  (nivå 1 i $vitser-array): 
$vitsescope = $aldersgrenser[array_rand($aldersgrenser,1)];
//		Så, velg en konkret vits innenfor denne aldersgrensen $vitser[scope][random]:
$vits = $vitser[ $vitsescope ][ rand(0, sizeof($vitser[$vitsescope])-1 )];

// 6. Returner vår tilfeldig valgte vits og ferdig.
http_response_code(200);
exit( 	json_encode( 
			array(
			  "status" 	=> 	true, 
			  "vits" 	=> 	$vits,
			  "scope"	=>  $vitsescope // '0' || '9' || '15' || '18'
			)
		)
	);