import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {
            // Instalação como app continua funcionando via manifest; falha no SW não é crítica.
        });
    });
}

// Captura o prompt de instalação do PWA para oferecer um botão próprio em vez de
// depender do usuário encontrar a opção no menu do navegador.
window.deferredPwaPrompt = null;
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    window.deferredPwaPrompt = event;
    window.dispatchEvent(new CustomEvent('pwa-install-available'));
});
window.addEventListener('appinstalled', () => {
    window.deferredPwaPrompt = null;
    window.dispatchEvent(new CustomEvent('pwa-installed'));
});
