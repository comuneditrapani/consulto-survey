import {
    loadSurvey,
    saveSurvey,
    loadI18n,
    saveI18nSlug,
    deleteI18nSlug,
} from '../api';

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Configura fetch per restituire una risposta JSON fittizia.
 * Restituisce il mock per eventuali asserzioni aggiuntive.
 */
function mockFetchJson(body) {
    fetch.mockResolvedValueOnce({
        json: () => Promise.resolve(body),
    });
    return fetch;
}

const { restUrl, nonce } = window.ConsultoAPI;

// ---------------------------------------------------------------------------
// Costanti attese — specchio di api.js
// ---------------------------------------------------------------------------

const HEADERS_BASE = { 'X-WP-Nonce': nonce };
const HEADERS_JSON = { ...HEADERS_BASE, 'Content-Type': 'application/json' };

// ---------------------------------------------------------------------------

beforeEach(() => {
    fetch.mockClear();
});

// ===========================================================================
// loadSurvey
// ===========================================================================

describe('loadSurvey', () => {

    it('chiama GET /survey/:id con il nonce corretto', async () => {
        mockFetchJson({ title: '', sections: [] });

        await loadSurvey(42);

        expect(fetch).toHaveBeenCalledWith(
            `${restUrl}/survey/42`,
            { headers: HEADERS_BASE }
        );
    });

    it('restituisce il JSON della risposta', async () => {
        const survey = { title: 'Test', sections: [{ id: 1 }] };
        mockFetchJson(survey);

        const result = await loadSurvey(42);

        expect(result).toEqual(survey);
    });

    it('restituisce { title: "", sections: [] } quando il server non ha dati', async () => {
        mockFetchJson({ title: '', sections: [] });

        const result = await loadSurvey(99);

        expect(result).toEqual({ title: '', sections: [] });
    });

    it('usa il postId nella URL', async () => {
        mockFetchJson({});

        await loadSurvey(7);

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/survey/7'),
            expect.anything()
        );
    });
});

// ===========================================================================
// saveSurvey
// ===========================================================================

describe('saveSurvey', () => {

    it('chiama POST /survey/:id con Content-Type JSON e nonce', async () => {
        mockFetchJson({ ok: true });
        const data = { title: 'Sondaggio', sections: [] };

        await saveSurvey(42, data);

        expect(fetch).toHaveBeenCalledWith(
            `${restUrl}/survey/42`,
            expect.objectContaining({
                method: 'POST',
                headers: HEADERS_JSON,
            })
        );
    });

    it('serializza il payload come JSON nel body', async () => {
        mockFetchJson({ ok: true });
        const data = { title: 'Sondaggio', sections: [] };

        await saveSurvey(42, data);

        const [, options] = fetch.mock.calls[0];
        expect(options.body).toBe(JSON.stringify(data));
    });

    it('restituisce { ok: true } in caso di successo', async () => {
        mockFetchJson({ ok: true });

        const result = await saveSurvey(42, {});

        expect(result).toEqual({ ok: true });
    });

    it('trasmette dati con sezioni e domande', async () => {
        mockFetchJson({ ok: true });
        const data = {
            sections: [
                { id: 1, slug: 'sec_a', questions: [{ id: 10, slug: 'q_eta', type: 'scale', min: 0, max: 100 }] }
            ]
        };

        await saveSurvey(5, data);

        const [, options] = fetch.mock.calls[0];
        expect(JSON.parse(options.body)).toEqual(data);
    });
});

// ===========================================================================
// loadI18n
// ===========================================================================

describe('loadI18n', () => {

    it('chiama GET /i18n con il nonce corretto', async () => {
        mockFetchJson({});

        await loadI18n();

        expect(fetch).toHaveBeenCalledWith(
            `${restUrl}/i18n`,
            { headers: HEADERS_BASE }
        );
    });

    it('restituisce la mappa i18n come oggetto', async () => {
        const map = {
            option_bus: { it: 'Mezzi pubblici', en: 'Public transport' },
            option_bike: { it: 'Bicicletta', en: 'Bicycle' },
        };
        mockFetchJson(map);

        const result = await loadI18n();

        expect(result).toEqual(map);
    });

    it('restituisce un oggetto vuoto se non ci sono traduzioni', async () => {
        mockFetchJson({});

        const result = await loadI18n();

        expect(result).toEqual({});
    });
});

// ===========================================================================
// saveI18nSlug
// ===========================================================================

describe('saveI18nSlug', () => {

    it('chiama PUT /i18n/:slug con Content-Type JSON e nonce', async () => {
        mockFetchJson({ ok: true });

        await saveI18nSlug('option_bus', { it: 'Mezzi pubblici', en: 'Public transport' });

        expect(fetch).toHaveBeenCalledWith(
            `${restUrl}/i18n/option_bus`,
            expect.objectContaining({
                method: 'PUT',
                headers: HEADERS_JSON,
            })
        );
    });

    it('serializza le traduzioni come JSON nel body', async () => {
        mockFetchJson({ ok: true });
        const translations = { it: 'Bicicletta', en: 'Bicycle' };

        await saveI18nSlug('option_bike', translations);

        const [, options] = fetch.mock.calls[0];
        expect(JSON.parse(options.body)).toEqual(translations);
    });

    it('usa lo slug nella URL', async () => {
        mockFetchJson({ ok: true });

        await saveI18nSlug('option_car', {});

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/i18n/option_car'),
            expect.anything()
        );
    });

    it('restituisce { ok: true } in caso di successo', async () => {
        mockFetchJson({ ok: true });

        const result = await saveI18nSlug('option_bus', { it: 'Mezzi pubblici' });

        expect(result).toEqual({ ok: true });
    });

    it('sovrascrive tutte le traduzioni dello slug (non merge parziale)', async () => {
        mockFetchJson({ ok: true });
        // Il server fa $map[$slug] = $incoming (rimpiazzo totale, non merge).
        // Il client deve mandare l'oggetto traduzioni completo.
        const fullTranslations = { it: 'Treno', en: 'Train', fr: 'Train', de: 'Zug' };

        await saveI18nSlug('option_train', fullTranslations);

        const [, options] = fetch.mock.calls[0];
        expect(JSON.parse(options.body)).toEqual(fullTranslations);
    });
});

// ===========================================================================
// deleteI18nSlug
// ===========================================================================

describe('deleteI18nSlug', () => {

    it('chiama DELETE /i18n/:slug con il nonce (senza Content-Type)', async () => {
        mockFetchJson({ ok: true });

        await deleteI18nSlug('option_bus');

        expect(fetch).toHaveBeenCalledWith(
            `${restUrl}/i18n/option_bus`,
            { method: 'DELETE', headers: HEADERS_BASE }
        );
    });

    it('non invia Content-Type perché non c\'è body', async () => {
        mockFetchJson({ ok: true });

        await deleteI18nSlug('option_bus');

        const [, options] = fetch.mock.calls[0];
        expect(options.headers).not.toHaveProperty('Content-Type');
    });

    it('usa lo slug nella URL', async () => {
        mockFetchJson({ ok: true });

        await deleteI18nSlug('option_bike');

        expect(fetch).toHaveBeenCalledWith(
            expect.stringContaining('/i18n/option_bike'),
            expect.anything()
        );
    });

    it('restituisce { ok: true } in caso di successo', async () => {
        mockFetchJson({ ok: true });

        const result = await deleteI18nSlug('option_bus');

        expect(result).toEqual({ ok: true });
    });

    it('restituisce un WP_Error se lo slug non esiste', async () => {
        // Il server risponde con { code, message, data: { status: 404 } }
        const wpError = { code: 'not_found', message: 'Slug not found', data: { status: 404 } };
        mockFetchJson(wpError);

        const result = await deleteI18nSlug('slug_inesistente');

        expect(result).toHaveProperty('code', 'not_found');
        expect(result.data).toHaveProperty('status', 404);
    });
});

