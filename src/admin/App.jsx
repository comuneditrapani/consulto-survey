import React, { useEffect, useState } from "react";
import {loadSurvey, saveSurvey } from "./api";
import Section from "./Section";

export default function App({ postId }) {
    console.log("render");
    useEffect(() => {
        const handler = () => {
            console.log("PAGE RELOADING");
        };
        window.addEventListener("beforeunload", handler);
        return () => {
            window.removeEventListener("beforeunload", handler);
        };
    }, []);    

    const [survey, setSurvey] = useState(null);

    useEffect(() => {
        loadSurvey(postId).then(setSurvey);
    }, [postId]);

    if(!survey) return <div>loading…</div>;

    const updateSurvey = (patch) => {
        setSurvey({...survey, ...patch});
    };

    const addSection = () => {
        updateSurvey({
            sections: [
                ...survey.sections,
                {
                    id: crypto.randomUUID(),
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
