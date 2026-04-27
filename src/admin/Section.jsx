import React, { useRef, useEffect } from "react";
import SlugSelector from "./SlugSelector";
import Question from "./Question";

export default function Section({ section, autoFocus, onChange }) {
    const focusQuestionId = useRef(null);
    const update = (patch) => {
        onChange({ ...section, ...patch });
    };

    const addQuestion = () => {
        const id = crypto.randomUUID();
        focusQuestionId.current = id;
        update({
            questions: [
                ...section.questions,
                {
                    id,
                    type: "text",
                    slug: "",
                    options: [],
                    min: null,
                    max: null
                }
            ]
        });
    };

    const slugRef = useRef(null);
    useEffect(() => {
        if(autoFocus && slugRef.current) {
            slugRef.current.focus();
        }
    }, [autoFocus]);

    return (
        <div style={{ border: "1px solid #ccc", padding: 10, marginTop: 10 }}>
            <strong>Section</strong>

            <SlugSelector
                ref= {slugRef}
                value={section.slug}
                onChange={(slug) => update({ slug })}
            />

            <br />

            <button type="button" onClick={addQuestion}>+ Add question</button>

            {section.questions.map((q, i) => (
                <Question
                    key={q.id}
                    question={q}
                    autoFocus={focusQuestionId.current === q.id}
                    onChange={(updated) => {
                        const questions = [...section.questions];
                        questions[i] = updated;
                        update({ questions });
                    }}
                />
            ))}
        </div>
    );
}
