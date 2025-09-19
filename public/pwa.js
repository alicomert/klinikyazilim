// PWA JavaScript - KlinikGo
// Service Worker Registration and Install Prompt

let deferredPrompt;
let installButton;

// Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('SW registered: ', registration);
                
                // Check for updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // New content is available
                            showUpdateNotification();
                        }
                    });
                });
            })
            .catch((registrationError) => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// Install Prompt Handler
window.addEventListener('beforeinstallprompt', (e) => {
    console.log('beforeinstallprompt fired');
    
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    
    // Show install button
    showInstallButton();
});

// Show Install Button
function showInstallButton() {
    // Create install button if it doesn't exist
    if (!document.getElementById('pwa-install-btn')) {
        const installBtn = document.createElement('button');
        installBtn.id = 'pwa-install-btn';
        installBtn.innerHTML = `
            <i class="fas fa-download mr-2"></i>
            Uygulamayı Yükle
        `;
        installBtn.className = 'fixed bottom-20 right-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg transition-all duration-300 z-40 flex items-center md:bottom-4 md:z-50';
        installBtn.style.display = 'flex';
        
        // Add click event
        installBtn.addEventListener('click', installApp);
        
        // Add to body
        document.body.appendChild(installBtn);
        
        // Animate in
        setTimeout(() => {
            installBtn.style.transform = 'translateY(0)';
            installBtn.style.opacity = '1';
        }, 100);
    }
}

// Install App Function
function installApp() {
    const installBtn = document.getElementById('pwa-install-btn');
    
    if (deferredPrompt) {
        // Show the install prompt
        deferredPrompt.prompt();
        
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
                showInstallSuccess();
            } else {
                console.log('User dismissed the install prompt');
            }
            
            // Clear the deferredPrompt
            deferredPrompt = null;
            
            // Hide install button
            if (installBtn) {
                installBtn.style.display = 'none';
            }
        });
    }
}

// Show Install Success Message
function showInstallSuccess() {
    const successMsg = document.createElement('div');
    successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successMsg.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>KlinikGo başarıyla yüklendi!</span>
        </div>
    `;
    
    document.body.appendChild(successMsg);
    
    // Remove after 3 seconds
    setTimeout(() => {
        successMsg.remove();
    }, 3000);
}

// Show Update Notification
function showUpdateNotification() {
    const updateMsg = document.createElement('div');
    updateMsg.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    updateMsg.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-sync-alt mr-2"></i>
                <span>Yeni güncelleme mevcut!</span>
            </div>
            <button onclick="refreshApp()" class="ml-4 bg-white text-blue-600 px-3 py-1 rounded text-sm hover:bg-gray-100">
                Yenile
            </button>
        </div>
    `;
    
    document.body.appendChild(updateMsg);
    
    // Auto remove after 10 seconds
    setTimeout(() => {
        if (updateMsg.parentNode) {
            updateMsg.remove();
        }
    }, 10000);
}

// Refresh App Function
function refreshApp() {
    window.location.reload();
}

// Handle App Installed Event
window.addEventListener('appinstalled', (evt) => {
    console.log('KlinikGo was installed');
    
    // Hide install button if visible
    const installBtn = document.getElementById('pwa-install-btn');
    if (installBtn) {
        installBtn.style.display = 'none';
    }
    
    // Show success message
    showInstallSuccess();
    
    // Track installation (you can add analytics here)
    if (typeof gtag !== 'undefined') {
        gtag('event', 'pwa_install', {
            'event_category': 'PWA',
            'event_label': 'KlinikGo'
        });
    }
});

// Network Status Monitoring
function updateNetworkStatus() {
    const statusIndicator = document.getElementById('network-status');
    
    if (navigator.onLine) {
        if (statusIndicator) {
            statusIndicator.className = 'fixed top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs z-50';
            statusIndicator.innerHTML = '<i class="fas fa-wifi mr-1"></i>Çevrimiçi';
            
            // Hide after 2 seconds
            setTimeout(() => {
                statusIndicator.style.display = 'none';
            }, 2000);
        }
    } else {
        if (!statusIndicator) {
            const indicator = document.createElement('div');
            indicator.id = 'network-status';
            document.body.appendChild(indicator);
        }
        
        const indicator = document.getElementById('network-status');
        indicator.className = 'fixed top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs z-50';
        indicator.innerHTML = '<i class="fas fa-wifi-slash mr-1"></i>Çevrimdışı';
        indicator.style.display = 'block';
    }
}

// Listen for network status changes
window.addEventListener('online', updateNetworkStatus);
window.addEventListener('offline', updateNetworkStatus);

// Initialize network status on load
window.addEventListener('load', updateNetworkStatus);

// PWA Display Mode Detection
function getPWADisplayMode() {
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
    const isFullscreen = window.matchMedia('(display-mode: fullscreen)').matches;
    const isMinimalUI = window.matchMedia('(display-mode: minimal-ui)').matches;
    
    if (isStandalone) {
        return 'standalone';
    } else if (isFullscreen) {
        return 'fullscreen';
    } else if (isMinimalUI) {
        return 'minimal-ui';
    } else {
        return 'browser';
    }
}

// Add PWA class to body based on display mode
window.addEventListener('load', () => {
    const displayMode = getPWADisplayMode();
    document.body.classList.add(`pwa-${displayMode}`);
    
    // Hide install button if already installed
    if (displayMode === 'standalone') {
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'none';
        }
    }
});

// Console welcome message
console.log('%c🏥 KlinikGo PWA', 'color: #0c3779; font-size: 20px; font-weight: bold;');
console.log('%cKlinik Yönetim Sistemi - Progressive Web App', 'color: #666; font-size: 12px;');

// Export functions for global use
window.PWA = {
    install: installApp,
    refresh: refreshApp,
    getDisplayMode: getPWADisplayMode,
    updateNetworkStatus: updateNetworkStatus
};