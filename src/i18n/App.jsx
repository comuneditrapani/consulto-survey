import React, { useEffect, useState } from "react";
import { loadI18n, saveI18nSlug, deleteI18nSlug } from "../api";
import { LANGUAGES } from "../config";

export default function App() {
    const [i18n, setI18n] = useState(null);
    const [workLang, setWorkLang] = useState('it');
    const [refLang, setRefLang] = useState('en');
    const [onlyMissing, setOnlyMissing] = useState(false);

    useEffect(() => {
        loadI18n().then(setI18n);
    }, []);

    if (!i18n) return <div>loading…</div>;

    const slugs = Object.keys(i18n).filter(slug => {
        if (!onlyMissing) return true;
        return !i18n[slug][workLang];
    });

    const handleChange = (slug, value) => {
        setI18n({
            ...i18n,
            [slug]: {
                ...i18n[slug],
                [workLang]: value
            }
        });
    };

    const handleBlur = (slug) => {
        saveI18nSlug(slug, i18n[slug]);
    };

    const handleDelete = (slug) => {
        if (!confirm(`Eliminare lo slug "${slug}"?`)) return;
        deleteI18nSlug(slug).then(() => {
            const updated = { ...i18n };
            delete updated[slug];
            setI18n(updated);
        });
    };

    return (
        <div style={{ padding: 20 }}>
            <h1>Traduzioni</h1>

            <div style={{ marginBottom: 16, display: 'flex', gap: 16, alignItems: 'center' }}>
                <label>
                    Lingua di lavoro:{' '}
                    <select value={workLang} onChange={e => setWorkLang(e.target.value)}>
                        {LANGUAGES.map(l => <option key={l} value={l}>{l}</option>)}
                    </select>
                </label>
                <label>
                    Riferimento:{' '}
                    <select value={refLang} onChange={e => setRefLang(e.target.value)}>
                        {LANGUAGES.map(l => <option key={l} value={l}>{l}</option>)}
                    </select>
                </label>
                <label>
                    <input
                        type="checkbox"
                        checked={onlyMissing}
                        onChange={e => setOnlyMissing(e.target.checked)}
                    />{' '}
                    Solo slug incompleti
                </label>
            </div>

            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                <thead>
                    <tr>
                        <th style={th}>slug</th>
                        <th style={th}>{refLang}</th>
                        <th style={th}>{workLang}</th>
                        <th style={th}></th>
                    </tr>
                </thead>
                <tbody>
                    {slugs.map(slug => (
                        <tr key={slug}>
                            <td style={td}><code>{slug}</code></td>
                            <td style={td}>
                                {refLang !== workLang
                                    ? (i18n[slug][refLang] || <em style={{ opacity: 0.4 }}>—</em>)
                                    : null}
                            </td>
                            <td style={td}>
                                <input
                                    style={{ width: '100%' }}
                                    value={i18n[slug][workLang] || ''}
                                    onChange={e => handleChange(slug, e.target.value)}
                                    onBlur={() => handleBlur(slug)}
                                />
                            </td>
                            <td style={td}>
                                <button
                                    type="button"
                                    onClick={() => handleDelete(slug)}
                                >
                                    ✕
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

const th = { textAlign: 'left', borderBottom: '2px solid #ccc', padding: '4px 8px' };
const td = { padding: '4px 8px', borderBottom: '1px solid #eee' };
