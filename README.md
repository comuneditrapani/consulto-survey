# consulto-survey

Plugin WordPress per la gestione di sondaggi con supporto a **voto preferenziale (ranking / partial ranking)**, progettato per contesti della pubblica amministrazione.

## Cos’è

`consulto-survey` estende le funzionalità standard dei sondaggi WordPress introducendo modalità di voto basate su ordinamenti parziali o completi delle preferenze espresse dagli utenti.

## Funzionalità principali

Il plugin supporta due modalità di voto preferenziale:

* **Ranking completo su insieme piccolo di opzioni**

  L’utente fornisce un ordinamento totale delle alternative disponibili.

  È pensato per sondaggi rivolti a un pubblico ampio, e il numero contenuto di alternative rende semplice l’espressione delle preferenze.

  Questa modalità supporta il processo decisionale degli amministratori fornendo una rappresentazione completa delle preferenze espresse dai cittadini.

* **Selezione + ordinamento parziale (top-k ranking)**

  L’amministratore seleziona un sottoinsieme (anche ampio) di opzioni e ne fornisce un ordinamento.

  Gli elementi non selezionati sono considerati tra loro equivalenti e con priorità inferiore rispetto agli elementi selezionati.

  Questa modalità supporta direttamente il processo decisionale degli amministratori in contesti in cui le alternative sono numerose, consentendo di concentrarsi sulle opzioni ritenute più rilevanti.

Le due modalità sono pensate per fasi diverse del lavoro decisionale: raccolta delle preferenze e successiva valutazione delle alternative.

### Nota sul tipo di voto

Le modalità di voto preferenziale permettono di raccogliere informazioni più ricche rispetto ai sondaggi a scelta singola (first-past-the-post).
In particolare, consentono di distinguere tra preferenze forti e deboli e di ridurre l’effetto di polarizzazione tipico dei sistemi in cui viene scelta una sola opzione.

## Casi d’uso

Il plugin è pensato per scenari in cui:

* è necessario raccogliere preferenze ordinate e non solo scelte binarie
* le opzioni possono essere numerose e non tutte rilevanti per ogni utente
* si vogliono analisi basate su ranking parziali

## Contesto istituzionale

Il progetto si inserisce nell’orientamento della pubblica amministrazione italiana verso l’adozione di software libero e riuso (linee guida AgID).

WordPress viene utilizzato come piattaforma di riferimento per la sua diffusione e accessibilità in ambito PA.

## Limiti attuali

* non include meccanismi di autenticazione o anti-frode
* non garantisce la partecipazione singola per utente
* non include strumenti di analisi dei risultati (export o aggregazione avanzata non ancora implementati)

## Collaborazione e riuso

Il progetto è pensato per il riuso nella pubblica amministrazione e per essere esteso in modo collaborativo.

Se utilizzi il plugin o hai esperienza in contesti simili, puoi contribuire in diversi modi:

* segnalando problemi o comportamenti inattesi
* proponendo miglioramenti funzionali o tecnici
* suggerendo casi d’uso non ancora coperti
* contribuendo direttamente allo sviluppo

Per partecipare è sufficiente un account GitHub, nel caso non lo si abbia già.
