# consulto-survey

Plugin WordPress per la gestione di sondaggi con supporto a **voto preferenziale (ranking / partial ranking)**, progettato per contesti della pubblica amministrazione.

## Cos’è

`consulto-survey` estende le funzionalità standard dei sondaggi WordPress introducendo modalità di voto basate su ordinamenti parziali o completi delle preferenze espresse dagli utenti.

## Funzionalità principali

Oltre ai soliti campi: selezione unica, selezione multipla, testo libero, numero intero compreso fra un minimo ed un massimo,
il plugin supporta due modalità di voto preferenziale:

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
* vedi [issues](../../issues)

## Collaborazione e riuso

Il progetto è pensato per il riuso nella pubblica amministrazione e per essere esteso in modo collaborativo.

Se utilizzi il plugin o hai esperienza in contesti simili, puoi contribuire in diversi modi:

* segnalando problemi o comportamenti inattesi
* proponendo miglioramenti funzionali o tecnici
* suggerendo casi d’uso non ancora coperti
* contribuendo direttamente allo sviluppo

Per partecipare è sufficiente un account GitHub, nel caso non lo si abbia già.

---

# Survey data model (Consulto plugin)

## Overview

A survey is a hierarchical structure composed of:

* sections
* questions
* options (optional, depending on question type)

All elements are identified by:

* a `slug` (unique within the survey context, used for i18n and rendering)
* a runtime `value` only for options (used in responses)

---

## 1. Survey structure

A survey is defined as:

```text
Survey
 └── Sections[]
      └── Questions[]
           └── Options[] (optional)
```

---

## 2. Section

A section groups related questions.

### Fields

* `slug` (string) → unique identifier within the survey
* `questions` (array of Question)

---

## 3. Question

A question defines a single input interaction.

### Fields

* `slug` (string) → unique identifier within the section
* `type` (string) → defines behavior and rendering

### Supported types

* `text` → free text input
* `single` → single choice
* `multiple` → multiple choice
* `scale` → numeric scale (min/max)
* `ranking` → full ranking of options
* `ranking-partial` → partial ranking / selection-based ranking

---

### Optional fields

Depending on type:

* `options` (array of Option) → required for choice-based types
* `min` (int) → for scale questions
* `max` (int) → for scale questions

---

## 4. Option

Represents a selectable value.

### Fields

* `slug` (string) → identifier used for i18n mapping
* `value` (string) → stored response value

---

## 5. slugs, i18n and label resolution

User-facing labels derive from a translation map.

Each element `slug` acts as a key to the translation.

### Label production

Labels are not stored in the survey structure.

They are attached at runtime by resolving each `slug` against the i18n map for the selected language.

The result is a fully labeled survey, that can be fed to the renderer.

### Example:

```text
slug = 'option_bus' ⇒
label = 'en': 'Public transport',
        'it': 'Mezzi pubblici',
        'fr': 'Transports publics',
        'de': 'Öffentliche Verkehrsmittel',
        'nl': 'Openbaar vervoer',
```

### Processing stage

Label resolution happens after loading the raw survey definition and before rendering.

The rendering layer receives only fully labeled data and does not perform any i18n lookup.

### fallback behavior

If a translation is missing for the selected language, the system falls back to English (en).

If English is also missing, the system falls back to the slug.

### Design principle

* Slugs are stable identifiers; labels are language-dependent representations.
* You can alter the translation without affecting any already collected data.
* Please do not alter slugs even if, on second thought, they "feel" wrong.

---

## 6. Constraints

* `slug` is unique within the survey scope
  * it is a structural identifier,
  * it is the key for translation lookup.

* No global uniqueness guarantee is required

* option `value` is unique within question scope.
  * it is the stored value for the response.

---

---

## 7. Data source independence

This model is independent of storage:

The same structure can be provided by:

* hard-coded PHP arrays (current implementation)
* WordPress custom post types
* ACF fields
* external API or JSON

The system MUST normalize all sources into this structure before rendering.

---

## 8. Rendering assumption

Any rendering layer receives a fully normalized survey:

* no DB queries
* no i18n lookup logic
* no structural transformations

Only display logic is allowed.

