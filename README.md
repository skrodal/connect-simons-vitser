# Simons Vitser
Det beste fra Simon's vitsekolleksjon!

Simons Vitser er et særdeles enkelt demo-API for å demonstrere UNINETT Connect Tjenesteplattform API Gatekeeper.

###For å bruke selv: 

1. Registrer nytt API i Connect Dashboard - https://dashboard.feideconnect.no/
2. Legg til følgende scopes ('gk_simons-vitser' vil byttes ut med 'gk_ditt_api_navn'). Velg selv hvilke som skal ha auto-accept/moderate.

	- Familievennlig (dette er basic scope som alltid er tilstede)
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

En klient kan ha tilgang til 1-mange scopes. Klienten kan også bestemme seg for redusere/øke scopes i sin request basert på hvilken bruker som er logget på. 

I tillegg følger autentiseringsinfo fra Connect som vi kan bruke for å i det hele tatt tillate tilgang:

      "PHP_AUTH_USER": "feideconnect",
      "PHP_AUTH_PW": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",

USER/PW for ditt API finner du i Connect Dashboard under meny "trust"