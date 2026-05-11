import React, { useEffect, useState, forwardRef } from "react";
import { useI18n } from "./I18nContext";

const SlugSelector = forwardRef(function SlugSelector({ value, onChange }, ref) {
    const { i18n, lang } = useI18n();

    const [query, setQuery] = useState(value || "");
    const [results, setResults] = useState([]);
    const [open, setOpen] = useState(false);

    // traduzione dalla mappa i18n nel Context
    const translation = value ? i18n[value]?.[lang] : null;

    // aggiorna query se value cambia dall'esterno
    useEffect(() => {
        setQuery(value || "");
    }, [value]);

    // fetch autocomplete results
    useEffect(() => {
        if (!query || query.length < 2) {
            setResults([]);
            return;
        }

        const controller = new AbortController();

        const timeout = setTimeout(() => {
            fetch(`${window.ConsultoAPI.restUrl}/autocomplete?q=${query}`, {
                signal: controller.signal
            })
                .then(r => r.json())
                .then(data => {
                    data.sort((a, b) => b.score - a.score);
                    setResults(data);
                })
                .catch(() => {});
        }, 250);

        return () => {
            clearTimeout(timeout);
            controller.abort();
        };
    }, [query]);

    return (
        <div className="slug-selector" style={{ position: "relative" }}>
            <div className="slug-row">
                <input
                    ref={ref}
                    value={query}
                    placeholder="slug"
                    onFocus={() => setOpen(true)}
                    onBlur={() => {
                        setTimeout(() => setOpen(false), 150);
                    }}
                    onChange={(e) => {
                        const val = e.target.value;
                        setQuery(val);
                        onChange(val);
                    }}
                />
                {translation && (
                    <div className="translation">
                        {translation} ({lang})
                    </div>
                )}
                {value && !translation && (
                    <div className="translation" style={{ opacity: 0.6 }}>
                        nessuna traduzione in {lang}
                    </div>
                )}
            </div>
            {open && results.length > 0 && (
                <div className="dropdown">
                    {results.map(item => (
                        <div
                            key={item.slug}
                            style={{ padding: 6, cursor: "pointer" }}
                            onMouseDown={(e) => {
                                e.preventDefault();
                                setQuery(item.slug);
                                setOpen(false);
                                onChange(item.slug);
                            }}
                        >
                            <b>{item.slug}</b> — {i18n[item.slug]?.[lang] || item.label} ({lang})
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
});

export default SlugSelector;
