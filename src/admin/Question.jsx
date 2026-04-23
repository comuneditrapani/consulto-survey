import React from "react";

export function OptionsEditor({ question, onChange }) {
    const update = (patch) => {
        onChange({ ...question, ...patch });
    };

    const addOption = () => {
        update({
            options: [
                ...question.options,
                { id: crypto.randomUUID(), slug: "" }
            ]
        });
    };

    return (
        <div style={{ marginTop: 10 }}>
            <button type="button" onClick={addOption}>
                + option
            </button>
            {question.options.map((opt, i) => (
                <div key={opt.id} style={{ display: "flex", gap: 8 }}>
                    <input
                        value={opt.slug}
                        placeholder="option_slug"
                        onChange={(e) => {
                            const options = [...question.options];
                            options[i] = { ...opt, slug: e.target.value };
                            update({ options });
                        }}
                    />
                    <button
                        type="button"
                        onClick={() => {
                            const options = question.options.filter((_, idx) => idx !== i);
                            update({ options });
                        }}
                    >
                        -
                    </button>
                </div>
            ))}
        </div>
    );
}

function renderByType(question, update) {
    switch (question.type) {
    case "text":
        return null;
    case "scale":
        return (
            <React.Fragment>
                <input
                    placeholder="min"
                    value={question.min ?? ""}
                    onChange={(e) => update({ min: e.target.value })}
                />
                <input
                    placeholder="max"
                    value={question.max ?? ""}
                    onChange={(e) => update({ max: e.target.value })}
                />
            </React.Fragment>
        );
    case "single":
    case "multiple":
        return <OptionsEditor question={question} onChange={update} />;
    case "ranking":
    case "ranking-partial":
        return <OptionsEditor question={question} onChange={update} />;
    default:
        return null;
    }
}

export default function Question({ question, onChange }) {
    const update = (patch) => {
        onChange({ ...question, ...patch });
    };

    return (
        <div style={{ marginLeft: 20, marginTop: 10 }}>
            <select
                value={question.type}
                onChange={(e) => update({ type: e.target.value })}
            >
                <option value="text">text</option>
                <option value="single">single</option>
                <option value="multiple">multiple</option>
                <option value="scale">scale</option>
                <option value="ranking">ranking</option>
                <option value="ranking-partial">ranking-partial</option>
            </select>
            <input
                placeholder="Question slug"
                value={question.slug}
                onChange={(e) => update({ slug: e.target.value})}
            />
            <div style={{ marginTop: 8 }}>
                {renderByType(question, update)}
            </div>
        </div>
    );
}
