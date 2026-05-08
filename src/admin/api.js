const {restUrl, nonce } = window.ConsultoAPI;

export async function loadSurvey(postId) {
    const res = await fetch(`${restUrl}/survey/${postId}`, {
        headers: {
            "X-WP-Nonce": nonce
        }
    });
    return res.json();
}

export async function saveSurvey(postId, data) {
    const res = await fetch(`${restUrl}/survey/${postId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify(data)
    });

    return res.json();
}
                          
export async function loadI18n() {
    const res = await fetch(`${restUrl}/i18n`, {
        headers: {
            "X-WP-Nonce": nonce
        }
    });
    return res.json();
}

export async function saveI18nSlug(slug, translations) {
    const res = await fetch(`${restUrl}/i18n/${slug}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": nonce
        },
        body: JSON.stringify(translations)
    });
    return res.json();
}

export async function deleteI18nSlug(slug) {
    const res = await fetch(`${restUrl}/i18n/${slug}`, {
        method: "DELETE",
        headers: {
            "X-WP-Nonce": nonce
        }
    });
    return res.json();
}

