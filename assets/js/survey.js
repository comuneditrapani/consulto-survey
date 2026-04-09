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
    const parts = [];
    for (const [q, value] of Object.entries(c.answers)) {

        const label = c.i18n.t(q);
        const displayValue = Array.isArray(value)
              ? value.map(v => c.i18n.t(v)).join(' > ')
              : c.i18n.t(value);

        parts.push(`<p><strong>${label}</strong>: ${displayValue}</p>`);
    }

    div.innerHTML = parts.join('');
};

c.ranking = {
    update: function (list) {
        const q = list.dataset.question;
        const values = Array.from(list.querySelectorAll('li'))
              .map(li => li.dataset.value);

        c.answers[q] = values;
        if(c.debug) console.log('RANKING:', values);
    }
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
            back_to_edit: 'Back to edit'
        },
        es: {
            next: 'Siguiente',
            prev: 'Anterior',
            review: 'Revisar respuestas',
            submit: 'Confirmar y enviar',
            summary_title: 'Resumen',
            back_to_edit: 'Volver a editar'
        },
        fr: {
            next: 'suivant',
            prev: 'précédent',
            review: 'Vérifier vos réponses',
            submit: 'confirmer et envoyer',
            summary_title: 'récaputilatif',
            back_to_edit: 'Retour à la modification'
        },
        de: {
            next: 'Weiter',
            prev: 'Zurück',
            review: 'Antworten prüfen',
            submit: 'Bestätigen und senden',
            summary_title: 'Zusammenfassung',
            back_to_edit: 'Zurück zur Bearbeitung'
        },
        it: {
            next: 'Avanti',
            prev: 'Indietro',
            review: 'Riepilogo',
            submit: 'Conferma e invia',
            summary_title: 'Riepilogo',
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
        document.getElementById('consulto_payload').value =
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

    lists.forEach(list => {
        let dragged = null;
        list.querySelectorAll('li').forEach(item => {
            item.addEventListener('dragstart', () => {
                dragged = item;
            });

            item.addEventListener('dragover', (e) => {
                e.preventDefault();
            });

            item.addEventListener('drop', (event) => {
                if (dragged === item) return;

                const rect = item.getBoundingClientRect();
                const after = (event.clientY - rect.top) > rect.height / 2;

                if (after) {
                    item.after(dragged);
                } else {
                    item.before(dragged);
                }

                c.ranking.update(list);
            });
        });
        c.ranking.update(list);
    });
};


document.addEventListener('DOMContentLoaded', () => {
    c.init.sections();
    c.init.bindInputs();
    c.init.ranking();
    c.init.bindForm();
    c.i18n.apply();
});
