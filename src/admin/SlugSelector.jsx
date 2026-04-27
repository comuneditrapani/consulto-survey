import React, { useEffect, useState } from "react";

export default function SlugSelector({ value, onChange }) {

    const [query, setQuery] = useState(value || "");
    const [results, setResults] = useState([]);
    const [selected, setSelected] = useState(null);
    const [open, setOpen] = useState(false);

    // --- fetch autocomplete results ---
    useEffect(() => {
        if (!query || query.length < 2) {
            setResults([]);
            return;
        }

        const controller = new AbortController();

        const timeout = setTimeout(() => {
            fetch(`/wp-json/consulto/v1/autocomplete?q=${query}`)
                .then(r => r.json())
                .then(data => {
                    // optional: ensure sorted by score
                    data.sort((a, b) => b.score - a.score);
                    setResults(data);
                })
                .catch(() => {});
        }, 250); // debounce

        return () => {
            clearTimeout(timeout);
            controller.abort();
        };

    }, [query]);

    // --- resolve initial value (important) ---
    useEffect(() => {
        if (!value) return;

        fetch(`/wp-json/consulto/v1/autocomplete?q=${value}`)
            .then(r => r.json())
            .then(data => {
                const match = data.find(r => r.slug === value);
                if (match) {
                    setSelected(match);
                    setQuery(match.slug);
                }
            })
            .catch(() => {});
    }, [value]);

    // --- display label ---
    const label = selected?.label || "";

    return (
        <div className="slug-selector" style={{ position: "relative" }}>
            <div className="slug-row">
                <input
                    value={query}
                    placeholder="slug"
                    onFocus={() => setOpen(true)}
                    onBlur={() => {
                        setTimeout(() => setOpen(false), 150);
                    }}
                    onChange={(e) => {
                        const val = e.target.value;

                        setQuery(val);
                        setSelected(null);     // user is editing again
                        onChange(val);         // propagate raw slug
                    }}
                />
                {selected && (
                    <div className="translation">
                        {selected.label} ({selected.lang})
                    </div>
                )}
                {value && !selected && (
                    <div className="translation"
                         style={{ opacity: 0.6 }}>
                        resolving…
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
                                setSelected(item);
                                setQuery(item.slug);
                                setOpen(false);
                                onChange(item.slug);
                            }}
                        >
                            <b>{item.slug}</b> — {item.label} ({item.lang})
                        </div>
                    ))}
                </div>
            )}

        </div>
    );
}

{/*
import React, { useEffect, useState } from "react";

export default function SlugSelector({ value, onChange, currentLang, fetchResults }) {
    const [query, setQuery] = useState(value || "");
    const [results, setResults] = useState([]);
    const [open, setOpen] = useState(false);
    const isKnownSlug = results.some((r) => r.slug === query);
    const isNewSlug = query && !isKnownSlug;
    const label = selected?.label || "";

    function getLabel(item, lang) {
        if (!item) return "";
        return (
            item.translations?.[lang] ||
                item.label ||
                item.slug
        );
    }

    useEffect(() => {
        if (!query || query.length < 2) return;

        const controller = new AbortController();

        const timeout = setTimeout(() => {
            fetch(`/wp-json/consulto/v1/autocomplete?q=${query}`, {
                signal: controller.signal,
            })
                .then((r) => r.json())
                .then(setResults)
                .catch(() => {});
        }, 250); // debounce

        return () => {
            clearTimeout(timeout);
            controller.abort();
        };
    }, [query]);

    return (
        <div style={{ marginTop: 8 }}>

            <input
                value={
                    selected
                        ? `${selected.slug} — ${label}`
                        : query
                }
                onChange={(e) => {
                    setQuery(e.target.value);
                    setSelected(null); // 👈 user is editing again
                    onChange(e.target.value);
                }}
            />
            {open && (
                <div classname="dropdown">
                    {results.map(item => (
                        <div
                            key={item.slug}
                            onClick={() => {
                                setSelected(item);
                                setQuery(item.slug);
                                onChange(item.slug);
                            }}
                        >
                            <b>{item.slug}</b> — {item.label} ({item.lang})
                        </div>
                    ))}
                </div>
            )}

            {isNewSlug && (
                <div style={{ marginTop: 4, fontSize: 12, opacity: 0.7 }}>
                    new slug: <b>{query}</b>
                </div>
             )}
            
        </div>
    );
}
*/}
