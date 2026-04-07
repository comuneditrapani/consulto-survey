const msState = {};
let msSections = [];
let msCurrentIndex = 0;
let msLastSectionIndex = 0;

const msLang = 'en'; // oppure 'it', 'es', 'fr', 'de'

const msI18n = {
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
};

const msLabels = {
    Q1: 'You are',
    Q2: 'Age group',
    Q3: 'Use of the historic city center',
    Q4: 'Main mode of transport',
    Q5: 'Quality of public spaces',
    Q6: 'Functionality of urban mobility',
    Q7: 'Priorities',
    Q8: 'Trade-off',
    Q9: 'What works best today',
    Q10: 'What should be improved first'
};

const msValueLabels = {
    Q1: {
        resident: 'Resident',
        visitor: 'Visitor',
        business: 'Business operator'
    },
    Q2: {
        under_18: 'Under 18',
        '18_29': '18–29',
        '30_44': '30–44'
    }
};

function msSubmit(action) {
  document.getElementById('ranking_action').value = action;

  if (action === 'confirm') {
    const items = document.querySelectorAll('#ranking li');
    const order = [...items].map(li => li.dataset.id);
    document.getElementById('ranking_result').value = JSON.stringify(order);
  }
}

function msBindInputs() {
    const elements = document.querySelectorAll('input, textarea, select');

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

            msState[q] = value;

            console.log('STATE:', msState);
        });
    });
}

function msInitSections() {
    msSections = Array.from(document.querySelectorAll('.ms-section'));

    msSections.forEach((sec, i) => {
        sec.style.display = (i === 0) ? 'block' : 'none';
    });
    document.getElementById('ms-summary').style.display = 'none';

    msCurrentIndex = 0;
}

function msInitRanking() {
    const lists = document.querySelectorAll('.ms-ranking');

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

                msUpdateRanking(list);
            });
        });
        msUpdateRanking(list);
    });
}

function msUpdateRanking(list) {
    const q = list.dataset.question;
    const values = Array.from(list.querySelectorAll('li'))
        .map(li => li.dataset.value);

    msState[q] = values;
    console.log('RANKING:', values);
}

function msNext() {
    if (msCurrentIndex >= msSections.length - 1) return;

    msSections[msCurrentIndex].style.display = 'none';
    msCurrentIndex++;
    msLastSectionIndex = msCurrentIndex;
    msSections[msCurrentIndex].style.display = 'block';
}

function msPrev() {
    if (msCurrentIndex <= 0) return;

    msSections[msCurrentIndex].style.display = 'none';
    msCurrentIndex--;
    msSections[msCurrentIndex].style.display = 'block';
}

function msGoToSummary() {
    msSections[msCurrentIndex].style.display = 'none';
    const summary = document.getElementById('ms-summary');
    summary.style.display = 'block';
    msRenderSummary();
}

function msPopulateHiddenInputs() {

    // esempio per ranking (quando ci sarà)
    if (msState['Q7']) {
        document.getElementById('q7_priority_ranking').value =
            Array.isArray(msState['Q7'])
                ? msState['Q7'].join('>')
                : msState['Q7'];
    }

    if (msState['Q7_action']) {
        document.getElementById('q7_action').value =
            msState['Q7_action'];
    }
}

function msRenderSummary() {
    const div = document.getElementById('summary-content');
    const parts = [];
    for (const [q, value] of Object.entries(msState)) {

        const label = msLabels[q] || q;
        let displayValue = value;

        if (Array.isArray(value)) {
            displayValue = value.map(v =>
                (msValueLabels[q] && msValueLabels[q][v]) || v
            ).join(' > ');
        } else {
            displayValue =
                (msValueLabels[q] && msValueLabels[q][value]) || value;
        }

        parts.push(`<p><strong>${label}</strong>: ${displayValue}</p>`);
    }

    div.innerHTML = parts.join('');
}

function msBackFromSummary() {
    // nascondi summary
    document.getElementById('ms-summary').style.display = 'none';
    // torna alla sezione precedente
    msCurrentIndex = msLastSectionIndex;
    msSections[msCurrentIndex].style.display = 'block';
}

function _(key) {
    return msI18n[msLang][key] || key;
}

function msApplyTranslations() {
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.dataset.i18n;
        el.textContent = _(key);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    msBindInputs();
    msInitSections();
    msApplyTranslations();
    msInitRanking();
});

