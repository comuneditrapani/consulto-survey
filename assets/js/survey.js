window.consulto = window.consulto || {}; // potrebbe venire dal PHP

const c = window.consulto;
// il PHP popola la struttura c.config, che definisce il questionario.

c.debug = true;  // logging sviluppo

c.state = {};    // variabili di stato interno
c.answers = {};  // le risposte, da restituire
c.ui = {};       // funzioni interfaccia utente
c.ranking = {};  // la porzione per il ranking
c.i18n = {};     // internazionalizzazione
c.init = {};     // inizializzazione

// variabili di stato
c.state.currentIndex = 0;
c.state.lastSectionIndex = 0;
c.state.sections = [];

// le risposte c.answers vengono popolate poco a poco
// avranno la forma slug=>value

// le funzioni di gestione interfaccia utente
c.ui.next = function () {
    if (c.state.currentIndex >= c.state.sections.length - 1) return;

    c.state.sections[c.state.currentIndex].style.display = 'none';
    c.state.currentIndex++;
    c.state.lastSectionIndex = c.state.currentIndex;
    c.state.sections[c.state.currentIndex].style.display = 'block';
};

c.ui.prev = function () {
    if (c.state.currentIndex <= 0) return;

    c.state.sections[c.state.currentIndex].style.display = 'none';
    c.state.currentIndex--;
    c.state.sections[c.state.currentIndex].style.display = 'block';
};

c.ui.gotoSummary = function () {
    c.state.sections[c.state.currentIndex].style.display = 'none';
    document.getElementById('consulto-summary').style.display = 'block';
    c.ui.renderSummary();
};

c.ui.backFromSummary = function () {
    document.getElementById('consulto-summary').style.display = 'none';
    c.state.currentIndex = c.state.lastSectionIndex;
    c.state.sections[c.state.currentIndex].style.display = 'block';
};

c.ui.renderSummary = function () {
    const div = document.getElementById('summary-content');
    div.innerHTML = '';

    for (const [q, value] of Object.entries(c.answers)) {

        if(q == c.ranking.question && !c.ranking.enabled)
            continue;

        const p = document.createElement('p');

        const strong = document.createElement('strong');
        strong.textContent = c.i18n.t(q);

        const displayValue = Array.isArray(value)
            ? value.map(v => c.i18n.t(v)).join(' > ')
            : c.i18n.t(value);

        p.append(strong, ': ', displayValue);

        div.appendChild(p);
    }
};

c.ranking.question = null; // il nome del ranking
c.ranking.enabled = false; // se il ranking è confermato
c.ranking.update = function (list) {
    const q = list.dataset.question;
    const values = Array.from(list.querySelectorAll('li'))
          .map(li => li.dataset.value);
    c.answers[q] = values;
};

c.i18n = {
    lang: 'it',
    dict: {
        en: {
            next: 'Next',
            prev: 'Back',
            review: 'Review',
            submit: 'Submit',
            summary_title: 'Summary',
            ranking_selection: 'Selected priorities',
            ranking_pool: 'Available options',
            ranking_is_valid: 'Ranking confirmed',
            back_to_edit: 'Back to edit'
        },
        es: {
            next: 'Siguiente',
            prev: 'Anterior',
            review: 'Revisar respuestas',
            submit: 'Confirmar y enviar',
            summary_title: 'Resumen',
            top_selection: 'Prioridades seleccionadas',
            ranking_pool: 'Opciones disponibles',
            ranking_is_valid: 'Clasificación confirmada',
            back_to_edit: 'Volver a editar'
        },
        fr: {
            next: 'suivant',
            prev: 'précédent',
            review: 'Vérifier vos réponses',
            submit: 'confirmer et envoyer',
            summary_title: 'récaputilatif',
            ranking_selection: 'Priorités sélectionnées',
            ranking_pool: 'Options disponibles',
            ranking_is_valid: 'Classement confirmé',
            back_to_edit: 'Retour à la modification'
        },
        de: {
            next: 'Weiter',
            prev: 'Zurück',
            review: 'Antworten prüfen',
            submit: 'Bestätigen und senden',
            summary_title: 'Zusammenfassung',
            ranking_selection: 'Ausgewählte Prioritäten',
            ranking_pool: 'Verfügbare Optionen',
            ranking_is_valid: 'Ranking bestätigt',
            back_to_edit: 'Zurück zur Bearbeitung'
        },
        it: {
            next: 'Avanti',
            prev: 'Indietro',
            review: 'Riepilogo',
            submit: 'Conferma e invia',
            summary_title: 'Riepilogo',
            ranking_selection: 'Priorità selezionate',
            ranking_pool: 'Opzioni disponibili',
            ranking_is_valid: 'Ordine confermato',
            back_to_edit: 'Torna al formulario'
        }
    },
    apply: function () {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.dataset.i18n;
            el.textContent = this.t(key);
        });
    },
    t: function (key) {
        return (this.dict[this.lang] && this.dict[this.lang][key]) || key;
    }
};

c.init.bindForm = function () {
    const form = document.getElementById('consulto-form');

    if (!form) return;

    form.addEventListener('submit', function () {
        if(!c.ranking.enabled && c.ranking.question in c.answers)
            delete c.answers[c.ranking.question];
        document.getElementById('consulto-payload').value =
            JSON.stringify(c.answers);
    });
};

c.init.bindInputs = function () {
    const elements = document.querySelectorAll('.consulto-section input, .consulto-section textarea, .consulto-section select');

    elements.forEach(el => {
        const q = el.dataset.question;
        if (!q) return;

        el.addEventListener('change', () => {
            let value;

            if (el.type === 'checkbox') {
                const checked = document.querySelectorAll(
                    `[data-question="${q}"]:checked`
                );
                value = Array.from(checked).map(e => e.value);
            } else {
                value = el.value;
            }

            c.answers[q] = value;

            if(c.debug) console.log('STATE:', c.answers);
        });
    });
};

c.init.sections = function () {
    c.state.sections = Array.from(document.querySelectorAll('.consulto-section'));

    c.state.sections.forEach((sec, i) => {
        sec.style.display = (i === 0) ? 'block' : 'none';
    });
    document.getElementById('consulto-summary').style.display = 'none';

    c.state.currentIndex = 0;
};

c.init.ranking = function() {
    const lists = document.querySelectorAll('.consulto-ranking');

    lists.forEach(function(el) {
        // le variabili c.ranking.enabled e c.ranking.question
        // limitano la funzionalità ad un unico consulto-ranking
        const q = el.dataset.question;
        c.ranking.enabled = false;
        c.ranking.question = q;

        new Sortable(el, {
            animation: 150,
            ghostClass: 'dragging',
            onSort: function() {
                c.ranking.enabled = true;
                c.ranking.update(el);
                document.getElementById(q + "-enabled").
                    checked = true;
            }
        });
        const cb = document.getElementById(q+"-enabled");
        console.log(q);
        console.log(cb);
        if (cb) {
            cb.addEventListener('change', () => {
                c.ranking.enabled = cb.checked;
            });
        }

        c.ranking.update(el);
    });
    const pools = document.querySelectorAll('.consulto-ranking-widget');
    pools.forEach(function(el) {
        const selected = el.querySelector('.consulto-selected');
        const pool = el.querySelector('.consulto-pool');

        // lista sinistra (ranking)
        new Sortable(selected, {
            group: {
                name: 'ranking',
                pull: true,
                put: true
            },
            animation: 150,
            sort: true,
            onSort: function() {
                c.ranking.update(selected);
            }
        });

        // lista destra (pool)
        new Sortable(pool, {
            group: {
                name: 'ranking',
                pull: true,
                put: true
            },
            animation: 150,
            sort: false        // non serve ordinare il pool
        });

        const filterInput = document.querySelector('.consulto-pool-filter');

        filterInput.addEventListener('input', function () {
            const value = this.value.toLowerCase();
            const items = pool.querySelectorAll('li');

            items.forEach(li => {
                const text = li.textContent.toLowerCase();

                if (text.includes(value)) {
                    li.style.display = '';
                } else {
                    li.style.display = 'none';
                }
            });
        });

    });
};


document.addEventListener('DOMContentLoaded', () => {
    c.init.sections();
    c.init.bindInputs();
    c.init.ranking();
    c.init.bindForm();
    c.i18n.apply();
});
