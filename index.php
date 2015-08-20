
<?php

/**
 * Simons Vitser
 *
 * Et enkelt demo-API for å demonstrere UNINETT Connect Tjenesteplattform.
 *
 * @author Simon Skrødal
 * @since August 2015
 */ 

/*
		# Simons Vitser
		Det beste fra Simon's vitsekolleksjon!

		Simons Vitser er et særdeles enkelt demo-API for å demonstrere UNINETT Connect Tjenesteplattform API Gatekeeper.

		###For å bruke selv: 

		1. Registrer nytt API i Connect Dashboard - https://dashboard.feideconnect.no/
		2. Legg til følgende scopes ('gk_simons-vitser' vil byttes ut med 'gk_ditt_api_navn'). Velg selv hvilke som skal ha auto-accept/moderate.

			- Familievennlig (dette er basic tilgang, og ikke et scope egentlig)
			- 9-årsgrense	gk_simons-vitser_9
			- 15-årsgrense	gk_simons-vitser_15
			- 18-årsgrense	gk_simons-vitser_18 

		3. URL til APIet ditt vil bli noe sånn: https://{api-navn-du-valgte}.gk.feideconnect.no/{path-på-api-endpoint-du-valgte}

		$_SERVER er en array som bl.a. inneholder headere og autentiserings-info 
		som er svært nyttig å benytte seg av.
		Dersom forespørsel kommer via Connect API Gatekeeper kan vi bl.a. se disse 
		headerene om klient og bruker:

		      "HTTP_X_FEIDECONNECT_CLIENTID": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
		      "HTTP_X_FEIDECONNECT_SCOPES": "9,15,18",
		      "HTTP_X_FEIDECONNECT_USERID": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
		      "HTTP_X_FEIDECONNECT_USERID_SEC": "feide:simon@uninett.no",

		Spesielt scopes og brukerinfo over er byttig for å styre tilgang. Legg merke til at api-navn i scopet (i eks gk_simons-vitser_15) droppes i headere og kun siste del (15) sendes med.

		En klient kan ha tilgang til 0-mange scopes. Klienten kan også bestemme seg for redusere/øke scopes i sin request basert på hvilken bruker som er logget på. 

		I tillegg følger autentiseringsinfo fra Connect som vi kan bruke for å i det hele tatt tillate tilgang:

		      "PHP_AUTH_USER": "feideconnect",
		      "PHP_AUTH_PW": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",

		USER/PW for ditt API finner du i Connect Dashboard under meny "Trust"
*/

// Forventer følgende bruker/passord fra Connect Gatekeeper - lagre i en config på et trygt sted:
$apiUser = "feideconnect";
$apiPass = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";	// Denne må du redigere, selvsagt...


// Vi snakker JSON
header('content-type: application/json; charset=utf-8');
// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: HEAD, GET, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, X-Requested-With, Origin, Accept, Content-Type");
header("Access-Control-Expose-Headers: Authorization, X-Requested-With, Origin, Accept, Content-Type");

// Connect kjører først en OPTIONS request med Access-Control headers. Godta dette.
if( strcmp($_SERVER['REQUEST_METHOD'], "OPTIONS") == 0) {
	exit(json_encode("Hei Connect GK! CORS er OK! Hilsen Simon's Vitser :-)"));
}


// 1. 	La oss først sjekke om forespørsel kommer fra Connect GK med riktig bruker/pass
// 		Dersom PHP_AUTH_USER ikke er satt i det hele tatt kan vi like gjerne stoppe her
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    exit(json_encode(array("status" => false, "message" => "HTTP/1.0 401 Unauthorized: Access requires Connect API GK Credentials")));
}
 
// 2.	Sjekk at bruker/passord stemmer overens
if( ( strcmp($_SERVER['PHP_AUTH_USER'], $apiUser) !== 0 ) || ( strcmp($_SERVER['PHP_AUTH_PW'], $apiPass) !== 0 ) ) {
	exit(json_encode(array("status" => false, "message" => "HTTP/1.0 401 Unauthorized: Invalid Credentials - " . $apiUser . " :: " . $apiPass)));
}


// 3. 	Vi er inne! 
//		Vitse-APIet er definert i Dashboard med følgende scopes:
//			- A (alle har tilgang)	: 	gk_simons-vitser
//			- 9-årsgrense ('9')		:	gk_simons-vitser_9
//			- 15-årsgrense ('15')	:	gk_simons-vitser_15
//			- 18-årsgrense ('18')	:	gk_simons-vitser_18
//
//		Scopes som kommer inn i header dropper prefiks 'gk_simons-vitser' og vi ender opp med '9', '15', '18'

// La meg dra ut mine beste vitser:
$vitser = array(
	// Familievennlig
	array(
			"[A]: Hørt om laksen som mista strømmen?",
			"[A]: Har dere hørt om torsken som var lei sei?",
			"[A]: Har du hørt om han som hadde bakteriefobi? Han kokte isbitene før han puttet dem i drikken sin :D",
		),
	// Aldersgrense 9 år
	array(
			"[9]: Det er bedre med 10 fulger på taket enn ei ugle i potetmosen.",
			"[9]: Hørt om kokken som var så dårlig i fotball? Sleivspark hver eneste gang.",
			"[9]: Hørt om elektrikeren som reiste til syden for å koble av...?",
			"[9]: Har du hørt om kona som vekket mannen midt på natten? Han hadde jo glemt å ta sovemedisinen sin...",
		),
	// Aldersgrense 15 år
	array(
			"[15]: Har du hørt om grisen som hadde svin på skogen?",
			"[15]: Visste du at Trine Hattestad er veldig spydig?",
			"[15]: Har du hørt om mannen som var så fornøyd med at svigermor bodde kun et steinkast unna? - Før eller siden må han jo treffe...",
			"[15]: Har du hørt om når Jesus var med i svømmekonkurranse? - Han vant på 'walk-over'.",
		),
	// Aldersgrense 18 år
	array(
			"[18]: Har du hørt om kannibalen som dreit ut broren sin?",
			"[18]: Pappa, hva er en transvestitt? - Spør tante Erik...",
		),
	);


// 4. 	Sjekk hvilke scopes (aldersgrense) klienten har tilgang til (ingen eller flere av 9, 15, 18). 
//		Dersom ett eller flere scopes, konverter den komma-separerte String'en til en array:
$aldersgrenser = empty($_SERVER['HTTP_X_FEIDECONNECT_SCOPES']) ? array() : explode(',', $_SERVER['HTTP_X_FEIDECONNECT_SCOPES']);


// 5. 	Start jobben med å finne en passende vits iht. godkjente aldersgrenser
//		Først, tilfeldig aldersnivå (nivå 1 i $vitser-array): 
$vitsescope = rand(0, sizeof($aldersgrenser));
//		Så, velg en konkret vits innenfor denne aldersgrensen $vitser[x][rand]:
$vits = $vitser[ $vitsescope ][ rand(0, sizeof($vitser[$vitsescope])-1 )];

// 6. Returner vår tilfeldig valgte vits og ferdig.
http_response_code(200);
exit( 	json_encode( 
			array(
			  "status" 	=> 	true, 
			  "vits" 	=> 	$vits
			)
		)
	);


