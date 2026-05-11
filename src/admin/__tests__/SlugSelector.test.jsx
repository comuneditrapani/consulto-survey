import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { I18nContext } from '../I18nContext';
import SlugSelector from '../SlugSelector';

const i18n = {
    trasporto_pubblico: {
        it: 'Trasporto pubblico',
        en: 'Public transport'
    }
};

function renderWithContext(value, lang = 'it', onChange= ()=>{}) {
    return render(
        <I18nContext.Provider value={{ i18n, lang, setLang: () => {} }}>
            <SlugSelector value={value} onChange={onChange} />
        </I18nContext.Provider>
    );
}

test('mostra la traduzione nella lingua corrente', () => {
    renderWithContext('trasporto_pubblico', 'it');
    expect(screen.getByText(/Trasporto pubblico/)).toBeInTheDocument();
});

test('mostra messaggio quando la traduzione manca', () => {
    renderWithContext('trasporto_pubblico', 'pl');
    expect(screen.getByText(/nessuna traduzione in pl/)).toBeInTheDocument();
});

test('mostra messaggio quando lo slug è sconosciuto', () => {
    renderWithContext('slug_inesistente', 'it');
    expect(screen.getByText(/nessuna traduzione in it/)).toBeInTheDocument();
});

beforeEach( () => {
    fetch.mockClear();
});

test('onChange viene chiamato quando si digita', () => {
    const onChange = jest.fn();
    renderWithContext('', 'it', onChange);
    const input = screen.getByPlaceholderText('slug');
    fireEvent.change(input, { target: { value: 'trasporto' } });
    expect(onChange).toHaveBeenCalledWith('trasporto');
});

test('il dropdown appare con i risultati dell\'autocomplete', async () => {
    fetch.mockResolvedValueOnce({
        json: async () => [
            { slug: 'trasporto_pubblico', label: 'Trasporto pubblico', lang: 'it', score: 80 }
        ]
    });

    renderWithContext('', 'it');
    const input = screen.getByPlaceholderText('slug');
    fireEvent.focus(input);
    fireEvent.change(input, { target: { value: 'trasporto' } });

    await waitFor(() => {
        expect(screen.getByText(/trasporto_pubblico/)).toBeInTheDocument();
    });
});

test('selezionando un risultato onChange viene chiamato con lo slug', async () => {
    fetch.mockResolvedValueOnce({
        json: async () => [
            { slug: 'trasporto_pubblico', label: 'Trasporto pubblico', lang: 'it', score: 80 }
        ]
    });

    const onChange = jest.fn();
    renderWithContext('', 'it', onChange);
    const input = screen.getByPlaceholderText('slug');
    fireEvent.focus(input);
    fireEvent.change(input, { target: { value: 'trasporto' } });

    await waitFor(() => {
        expect(screen.getByText(/trasporto_pubblico/)).toBeInTheDocument();
    });

    fireEvent.mouseDown(screen.getByText(/trasporto_pubblico/));
    expect(onChange).toHaveBeenCalledWith('trasporto_pubblico');
});

test('chiama autocomplete con il restUrl dal Context', async () => {
    fetch.mockResolvedValueOnce({
        json: async () => []
    });

    renderWithContext('', 'it');
    const input = screen.getByPlaceholderText('slug');
    fireEvent.focus(input);
    fireEvent.change(input, { target: { value: 'trasporto' } });

    await waitFor(() => {
        expect(fetch).toHaveBeenCalledWith(
            'http://localhost/wp-json/consulto/v1/autocomplete?q=trasporto',
            expect.anything()
        );
    });
});

