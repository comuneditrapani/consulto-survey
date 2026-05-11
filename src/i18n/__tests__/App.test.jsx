import React from 'react';
import { render, screen, waitFor } from '@testing-library/react';
import App from '../App';

beforeEach(() => {
    fetch.mockClear();
});

test('mostra loading durante il fetch iniziale', () => {
    fetch.mockResolvedValue(new Promise(()=>{}));
    render(<App />);
    expect(screen.getByText('loading…')).toBeInTheDocument();
});

test('mostra il titolo dopo il caricamento', async () => {
    fetch.mockResolvedValue({ json: async () => ({}) });
    render(<App />);
    await waitFor(() => {
        expect(screen.getByText('Traduzioni')).toBeInTheDocument();
    });
});

test('mostra gli slug presenti nella mappa', async () => {
    fetch.mockResolvedValue({
        json: async () => ({
            trasporto_pubblico: { it: 'Trasporto pubblico', en: 'Public transport' }
        })
    });
    render(<App />);
    await waitFor(() => {
        expect(screen.getByText('trasporto_pubblico')).toBeInTheDocument();
    });
});

