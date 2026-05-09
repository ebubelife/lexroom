// PWA Install Prompt Handler
(function() {
    let deferredPrompt;
    let installButton;

    // Check if already installed
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        console.log('PWA is already installed');
        return;
    }

    // Listen for beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('beforeinstallprompt fired');
        e.preventDefault();
        deferredPrompt = e;
        showInstallPromotion();
    });

    // Show install promotion banner
    function showInstallPromotion() {
        // Check if user dismissed before
        if (localStorage.getItem('pwa-install-dismissed') === 'true') {
            return;
        }

        // Create install banner
        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.innerHTML = `
            <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; max-width: 90%; width: 400px;">
                <div style="background: linear-gradient(135deg, #0D1B2A 0%, #1a2332 100%); border: 2px solid #C9A84C; border-radius: 16px; padding: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); animation: slideUp 0.4s ease-out;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <svg width="48" height="56" viewBox="0 0 48 56" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink: 0;">
                            <line x1="24" y1="6" x2="24" y2="50" stroke="#C9A84C" stroke-width="2" stroke-linecap="round"/>
                            <line x1="12" y1="50" x2="36" y2="50" stroke="#C9A84C" stroke-width="2" stroke-linecap="round"/>
                            <line x1="4" y1="17" x2="44" y2="17" stroke="#C9A84C" stroke-width="1.6" stroke-linecap="round"/>
                            <line x1="4" y1="17" x2="3" y2="31" stroke="#C9A84C" stroke-width="1.1" stroke-linecap="round" opacity="0.7"/>
                            <line x1="44" y1="17" x2="43" y2="27" stroke="#C9A84C" stroke-width="1.1" stroke-linecap="round" opacity="0.7"/>
                            <path d="M0 33 Q3 40 6 33" stroke="#C9A84C" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                            <path d="M39 29 Q43 36 47 29" stroke="#C9A84C" stroke-width="1.6" fill="none" stroke-linecap="round" opacity="0.85"/>
                            <circle cx="24" cy="6" r="2.5" fill="#C9A84C"/>
                        </svg>
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #C9A84C; font-family: 'Instrument Serif', serif;">Install FirstMediator</h3>
                            <p style="margin: 0; font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.4;">Get quick access from your home screen</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <button id="pwa-install-btn" style="flex: 1; padding: 10px; background: #C9A84C; color: #0D1B2A; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: opacity 0.2s;">
                            Install
                        </button>
                        <button id="pwa-dismiss-btn" style="padding: 10px 20px; background: transparent; color: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                            Not Now
                        </button>
                    </div>
                </div>
            </div>
            <style>
                @keyframes slideUp {
                    from {
                        transform: translateX(-50%) translateY(100px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(-50%) translateY(0);
                        opacity: 1;
                    }
                }
                #pwa-install-btn:hover {
                    opacity: 0.9;
                }
                #pwa-dismiss-btn:hover {
                    background: rgba(255,255,255,0.05);
                    color: rgba(255,255,255,0.9);
                }
            </style>
        `;

        document.body.appendChild(banner);

        // Install button click
        document.getElementById('pwa-install-btn').addEventListener('click', async () => {
            if (!deferredPrompt) return;

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            console.log(`User response: ${outcome}`);
            
            if (outcome === 'accepted') {
                console.log('PWA installed');
            }
            
            deferredPrompt = null;
            banner.remove();
        });

        // Dismiss button click
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            localStorage.setItem('pwa-install-dismissed', 'true');
            banner.remove();
        });
    }

    // Listen for successful installation
    window.addEventListener('appinstalled', () => {
        console.log('PWA was installed');
        deferredPrompt = null;
        
        // Show success toast
        if (typeof showToast === 'function') {
            showToast('FirstMediator installed successfully!', 'success');
        }
    });

    // iOS Safari install instructions
    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }

    function isInStandaloneMode() {
        return ('standalone' in window.navigator) && (window.navigator.standalone);
    }

    if (isIOS() && !isInStandaloneMode()) {
        // Show iOS install instructions after a delay
        setTimeout(() => {
            if (localStorage.getItem('ios-install-dismissed') === 'true') {
                return;
            }

            const iosBanner = document.createElement('div');
            iosBanner.innerHTML = `
                <div style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; max-width: 90%; width: 400px;">
                    <div style="background: linear-gradient(135deg, #0D1B2A 0%, #1a2332 100%); border: 2px solid #C9A84C; border-radius: 16px; padding: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #C9A84C; font-family: 'Instrument Serif', serif;">Install FirstMediator</h3>
                            <button id="ios-dismiss-btn" style="background: none; border: none; color: rgba(255,255,255,0.5); font-size: 24px; cursor: pointer; padding: 0; line-height: 1;">&times;</button>
                        </div>
                        <p style="margin: 0 0 10px 0; font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.5;">
                            Tap <svg style="display: inline; width: 16px; height: 16px; vertical-align: middle;" fill="currentColor" viewBox="0 0 24 24"><path d="M16 5l-1.42 1.42-1.59-1.59V16h-1.98V4.83L9.42 6.42 8 5l4-4 4 4zm4 5v11c0 1.1-.9 2-2 2H6c-1.11 0-2-.9-2-2V10c0-1.11.89-2 2-2h3v2H6v11h12V10h-3V8h3c1.1 0 2 .89 2 2z"/></svg> then "Add to Home Screen"
                        </p>
                    </div>
                </div>
            `;
            document.body.appendChild(iosBanner);

            document.getElementById('ios-dismiss-btn').addEventListener('click', () => {
                localStorage.setItem('ios-install-dismissed', 'true');
                iosBanner.remove();
            });
        }, 3000);
    }
})();
