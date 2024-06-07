(() => {
    const rootElement = document.documentElement;
    const KEY_NAME = 'sn-scheme';
    let currentTheme = '';

    const setTheme = (themeName) => {
        rootElement.classList.remove('SnTheme-darck');
        rootElement.classList.remove('SnTheme-light');
        currentTheme = themeName;

        // Set current theme name
        if (themeName === 'light') {
            rootElement.classList.add('SnTheme-light');
        } else if (themeName === 'darck') {
            rootElement.classList.add('SnTheme-darck');
        } else if (themeName === 'system') {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                rootElement.classList.add('SnTheme-darck');
                currentTheme = 'darck';
            } else {
                rootElement.classList.add('SnTheme-light');
                currentTheme = 'light';
            }
        } else {
            rootElement.classList.add('SnTheme-' + themeName);
            currentTheme = themeName;
        }

        // Save in storage
        sessionStorage.setItem(KEY_NAME, currentTheme);
    }

    // ==============================================================
    // Load saved scheme
    const snSchemeSaved = sessionStorage.getItem(KEY_NAME);
    if (snSchemeSaved) {
        setTheme(snSchemeSaved);
    } else {
        if (rootElement.classList.contains('SnTheme-light')) {
            setTheme('light');
        } else if (rootElement.classList.contains('SnTheme-darck')) {
            setTheme('darck');
        } else {
            setTheme('system');
        }
    }

    // ==============================================================
    // Render In Dom
    document.addEventListener("DOMContentLoaded", () => {
        const checkElement = document.getElementById('snTheme');
        if(checkElement){
            checkElement.checked = currentTheme === 'darck';

            checkElement.addEventListener('change', () => {
                setTheme(checkElement.checked ? 'darck' : 'light');
            });
        }
    });
})();
