import React, { useRef, useEffect, useState } from "react";
import { loadSurvey, saveSurvey, loadI18n } from "../api";
import { LANGUAGES } from "../config";
import { I18nContext } from "./I18nContext";
import Section from "./Section";

export default function App({ postId }) {
    const focusSectionId = useRef(null);
    const [survey, setSurvey] = useState(null);
    const [i18n, setI18n] = useState({});
    const [lang, setLang] = useState('it');
    
    useEffect(() => {
        loadSurvey(postId).then(setSurvey);
        loadI18n().then(setI18n);
    }, [postId]);

    if(!survey) return <div>loading…</div>;

    const updateSurvey = (patch) => {
        setSurvey({...survey, ...patch});
    };

    const addSection = () => {
        const id = crypto.randomUUID();
        focusSectionId.current = id;

        updateSurvey({
            sections: [
                ...survey.sections,
                {
                    id,
                    slug: "",
                    questions: []
                }
            ]
        });
    };
    
    return (
        <I18nContext.Provider value={{ i18n, lang, setLang }}>
            <div style={{ padding: 20 }}>
                <h1>Consulto Survey Builder</h1>
                <label>
                    Lingua:{' '}
                    <select value={lang} onChange={e => setLang(e.target.value)}>
                        {LANGUAGES.map(l => <option key={l} value={l}>{l}</option>)}
                    </select>
                </label>
                <button type="button" onClick={addSection}>+ Add section</button>

                {survey.sections.map((section, i) => (
                    <Section
                        key={section.id}
                        section={section}
                        autoFocus={focusSectionId.current === section.id}
                        onChange={(updated) => {
                            const sections = [...survey.sections];
                            sections[i] = updated;
                            updateSurvey({ sections });
                        }}
                    />
                ))}

                <button type="button" onClick={() => saveSurvey(postId, survey)}>
                    save
                </button>
                <pre>{JSON.stringify(survey, null, 2)}</pre>
            </div>
        </I18nContext.Provider>
    );
}
