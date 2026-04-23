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
                          
