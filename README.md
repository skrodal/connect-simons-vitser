# Simons Vitser

**Har du hørt om grisen som hadde svin på skogen?** 

Det beste fra Simon's vitsekolleksjon - nå APIfisert til glede for mennesker i alle aldre :-)

Simons Vitser er et særdeles enkelt demo-API for å demonstrere UNINETT Connect Tjenesteplattform API Gatekeeper.

###For å bruke selv: 

Dersom du ønsker å sette opp APIet på egen server må du ha tilgang til nettopp det: en egen server. Dump `index.php` ett eller annet sted på serveren din og gjør så følgende:

1. *Registrer nytt API i Connect Dashboard - https://dashboard.feideconnect.no/*
2. *Legg til følgende scopes ('gk_simons-vitser' vil byttes ut med 'gk_ditt_api_navn')*: 
	
	Velg selv hvilke som skal ha auto-accept/moderate.

	- Familievennlig gk_simons-vitser_0
	- 9-årsgrense	 gk_simons-vitser_9
	- 15-årsgrense	 gk_simons-vitser_15
	- 18-årsgrense	 gk_simons-vitser_18 

3. *URL til ditt API vil bli noe sånn*:

	`https://{api-navn-du-valgte}.gk.feideconnect.no/{path-på-api-endpoint-du-valgte}`

###Litt teknisk info: 

$_SERVER er en array som bl.a. inneholder headere og autentiserings-info 
som er svært nyttig å benytte seg av.

Dersom forespørsel kommer via Connect API Gatekeeper kan vi bl.a. se disse 
headerene om klient og bruker:

      "HTTP_X_FEIDECONNECT_CLIENTID": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
      "HTTP_X_FEIDECONNECT_SCOPES": "0,9,15,18",
      "HTTP_X_FEIDECONNECT_USERID": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
      "HTTP_X_FEIDECONNECT_USERID_SEC": "feide:bør.børson@olderdalen.no",

I tillegg følger autentiseringsinfo fra Connect som vi kan bruke for å i det hele tatt tillate tilgang:

      "PHP_AUTH_USER": "feideconnect",
      "PHP_AUTH_PW": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",

USER/PW for ditt API finner du i Connect Dashboard under meny "Trust".

#### Scopes:

Spesielt scopes og brukerinfo over er byttig for å styre tilgang. Legg merke til at api-navn i scopet (i eks gk_simons-vitser_15) droppes i headere og kun siste del (15) sendes med.

En klient kan ha tilgang til 1-til-mange scopes. Med kun 'basic' scope vil APIet levere kun familevennlige vitser som default.
