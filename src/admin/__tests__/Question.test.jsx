import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import { I18nContext } from '../I18nContext';
import Question from '../Question';

const i18n = {};
const defaultContext = { i18n, lang: 'it', setLang: () => {} };

const baseQuestion = {
    id: '1',
    type: 'text',
    slug: 'domanda_uno',
    options: [],
    min: null,
    max: null
};

function renderQuestion(question = baseQuestion, onChange = () => {}) {
    return render(
        <I18nContext.Provider value={defaultContext}>
            <Question question={question} autoFocus={false} onChange={onChange} />
        </I18nContext.Provider>
    );
}

test('mostra il selettore di tipo', () => {
    renderQuestion();
    expect(screen.getByRole('combobox')).toBeInTheDocument();
});

test('onChange viene chiamato al cambio di tipo', () => {
    const onChange = jest.fn();
    renderQuestion(baseQuestion, onChange);
    fireEvent.change(screen.getByRole('combobox'), { target: { value: 'single' } });
    expect(onChange).toHaveBeenCalledWith(expect.objectContaining({ type: 'single' }));
});

test('mostra i campi min/max per tipo scale', () => {
    renderQuestion({ ...baseQuestion, type: 'scale' });
    expect(screen.getByPlaceholderText('min')).toBeInTheDocument();
    expect(screen.getByPlaceholderText('max')).toBeInTheDocument();
});

test('mostra il bottone aggiungi opzione per tipo single', () => {
    renderQuestion({ ...baseQuestion, type: 'single' });
    expect(screen.getByText('+ option')).toBeInTheDocument();
});

