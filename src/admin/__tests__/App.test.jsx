import React from 'react';
import { render, screen, waitFor } from '@testing-library/react';
import App from '../App';

beforeEach(() => {
    fetch.mockClear();
});

test('mostra loading durante il fetch iniziale', () => {
    fetch.mockResolvedValue(new Promise(()=>{}));
    render(<App postId="1" />);
    expect(screen.getByText('loading…')).toBeInTheDocument();
});

test('mostra il survey builder dopo il caricamento', async () => {
    fetch.mockResolvedValue({ json: async () => ({ sections: [] }) });
    render(<App postId="1" />);
    await waitFor(() => {
        expect(screen.getByText('Consulto Survey Builder')).toBeInTheDocument();
    });
});

