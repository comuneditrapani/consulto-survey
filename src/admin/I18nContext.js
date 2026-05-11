import { createContext, useContext } from "react";

export const I18nContext = createContext({
    i18n: {},
    lang: 'it',
    setLang: () => {}
});

export function useI18n() {
    return useContext(I18nContext);
}

