import React from "react";
import { createRoot } from "react-dom/client";
import App from "./App";

const el = document.getElementById("consulto-survey-root");

if (el) {
    const postId = el.dataset.postId;
    const root = createRoot(el);
    root.render(<App postId={postId}/>);
}

