import React, { useRef, useEffect, useState } from "react";
import {loadSurvey, saveSurvey } from "./api";
import Section from "./Section";

export default function App({ postId }) {
    const focusSectionId = useRef(null);
    const [survey, setSurvey] = useState(null);

    useEffect(() => {
        loadSurvey(postId).then(setSurvey);
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
        <div style={{ padding: 20 }}>
            <h1>Consulto Survey Builder</h1>

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
  );
}
