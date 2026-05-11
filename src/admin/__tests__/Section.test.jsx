import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import { I18nContext } from '../I18nContext';
import Section from '../Section';

const i18n = {};
const defaultContext = { i18n, lang: 'it', setLang: () => {} };

const baseSection = {
    id: '1',
    slug: 'sezione_uno',
    questions: []
};

function renderSection(section = baseSection, onChange = () => {}) {
    return render(
        <I18nContext.Provider value={defaultContext}>
            <Section section={section} autoFocus={false} onChange={onChange} />
        </I18nContext.Provider>
    );
}

test('mostra il bottone aggiungi domanda', () => {
    renderSection();
    expect(screen.getByText('+ Add question')).toBeInTheDocument();
});

test('aggiungendo una domanda onChange viene chiamato', () => {
    const onChange = jest.fn();
    renderSection(baseSection, onChange);
    fireEvent.click(screen.getByText('+ Add question'));
    expect(onChange).toHaveBeenCalledWith(
        expect.objectContaining({
            questions: expect.arrayContaining([
                expect.objectContaining({ type: 'text' })
            ])
        })
    );
});

test('mostra le domande esistenti', () => {
    const section = {
        ...baseSection,
        questions: [
            { id: '2', type: 'scale', slug: 'domanda_scala', options: [], min: null, max: null }
        ]
    };
    renderSection(section);
    expect(screen.getByPlaceholderText('min')).toBeInTheDocument();
});

