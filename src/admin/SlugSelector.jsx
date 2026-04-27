import React, { useEffect, useState } from "react";

export default function SlugSelector({ value, onChange }) {
    const [query, setQuery] = useState(value || "");
    const [results, setResults] = useState([]);
    const isKnownSlug = results.some((r) => r.slug === query);
    const isNewSlug = query && !isKnownSlug;

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

            {/* input */}
            <input
                value={query}
                placeholder="slug"
                onChange={(e) => {
                    setQuery(e.target.value);
                    onChange(e.target.value);
                }}
            />

            {/* results */}
            {results.length > 0 && (
                <div style={{ border: "1px solid #ccc", marginTop: 4 }}>
                    {results.map((r, i) => (
                        <div
                            key={i}
                            style={{ padding: 4, cursor: "pointer" }}
                            onClick={() => {
                                setQuery(r.slug);
                                onChange(r.slug);
                                setResults([]);
                            }}
                        >
                            {r.slug}
                        </div>
                    ))}
                </div>
            )}

            {/* create new hint */}
            {isNewSlug && (
                <div style={{ marginTop: 4, fontSize: 12, opacity: 0.7 }}>
                    new slug: <b>{query}</b>
                </div>
             )}
            
        </div>
    );
}
