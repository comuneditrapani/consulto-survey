const {restUrl, nonce } = window.ConsultoAPI;

const headers = {
    "X-WP-Nonce": nonce
};

const jsonHeaders = {
    ...headers,
    "Content-Type": "application/json"
};

export async function loadSurvey(postId) {
    const res = await fetch(`${restUrl}/survey/${postId}`, { headers });
    return res.json();
}

export async function saveSurvey(postId, data) {
    const res = await fetch(`${restUrl}/survey/${postId}`, {
        method: "POST",
        headers: jsonHeaders,
        body: JSON.stringify(data)
    });

    return res.json();
}
                          
export async function loadI18n() {
    const res = await fetch(`${restUrl}/i18n`, { headers });
    return res.json();
}

export async function saveI18nSlug(slug, translations) {
    const res = await fetch(`${restUrl}/i18n/${slug}`, {
        method: "PUT",
        headers: jsonHeaders,
        body: JSON.stringify(translations)
    });
    return res.json();
}

export async function deleteI18nSlug(slug) {
    const res = await fetch(`${restUrl}/i18n/${slug}`, {
        method: "DELETE",
        headers
    });
    return res.json();
}

