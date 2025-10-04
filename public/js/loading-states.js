/**
 * Loading States Management for Clinic Management System
 * Handles button loading states and user feedback
 */

class LoadingManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupButtonLoadingStates();
        this.setupGlobalLoadingIndicator();
        this.setupFormSubmissionHandlers();
    }

    /**
     * Setup button loading states for all clickable elements
     */
    setupButtonLoadingStates() {
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button[wire\\:click], a[wire\\:click]');
            if (button && !button.disabled) {
                this.activateButtonLoading(button);
            }
        });

        // Listen for Livewire updates to reset button states
        document.addEventListener('livewire:update', () => {
            this.resetAllButtonStates();
        });
    }

    /**
     * Activate loading state for a specific button
     */
    activateButtonLoading(button) {
        // Store original content
        if (!button.dataset.originalContent) {
            button.dataset.originalContent = button.innerHTML;
        }

        // Add loading classes
        button.classList.add('btn-loading', 'pulse-on-click');
        button.disabled = true;

        // Update button content based on type
        const buttonType = this.getButtonType(button);
        const loadingContent = this.getLoadingContent(buttonType);
        
        button.innerHTML = loadingContent;

        // Add pulse effect
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
    }

    /**
     * Get button type based on classes and content
     */
    getButtonType(button) {
        const classList = button.className.toLowerCase();
        const content = button.textContent.toLowerCase();

        if (classList.includes('bg-blue') || content.includes('kaydet') || content.includes('ekle')) {
            return 'primary';
        } else if (classList.includes('bg-green') || content.includes('onayla')) {
            return 'success';
        } else if (classList.includes('bg-red') || content.includes('sil')) {
            return 'danger';
        } else if (classList.includes('bg-yellow') || content.includes('düzenle')) {
            return 'warning';
        }
        return 'default';
    }

    /**
     * Get loading content based on button type
     */
    getLoadingContent(type) {
        const spinner = '<i class="fas fa-spinner fa-spin mr-2"></i>';
        
        const loadingTexts = {
            primary: `${spinner}Kaydediliyor...`,
            success: `${spinner}Onaylanıyor...`,
            danger: `${spinner}Siliniyor...`,
            warning: `${spinner}Güncelleniyor...`,
            default: `${spinner}Yükleniyor...`
        };

        return loadingTexts[type] || loadingTexts.default;
    }

    /**
     * Reset all button states
     */
    resetAllButtonStates() {
        document.querySelectorAll('.btn-loading').forEach(button => {
            this.resetButtonState(button);
        });
    }

    /**
     * Reset specific button state
     */
    resetButtonState(button) {
        if (button.dataset.originalContent) {
            button.innerHTML = button.dataset.originalContent;
        }
        
        button.classList.remove('btn-loading', 'pulse-on-click');
        button.disabled = false;
        button.style.transform = '';
    }

    /**
     * Setup global loading indicator
     */
    setupGlobalLoadingIndicator() {
        // Show loading for Livewire requests
        document.addEventListener('livewire:load', () => {
            this.showGlobalLoading();
        });

        document.addEventListener('livewire:update', () => {
            this.hideGlobalLoading();
        });

        // Show loading for form submissions
        document.addEventListener('livewire:upload-start', () => {
            this.showGlobalLoading('Dosya yükleniyor...');
        });

        document.addEventListener('livewire:upload-finish', () => {
            this.hideGlobalLoading();
        });

        document.addEventListener('livewire:upload-error', () => {
            this.hideGlobalLoading();
            this.showErrorMessage('Dosya yükleme hatası!');
        });
    }

    /**
     * Show global loading overlay
     */
    showGlobalLoading(message = 'Yükleniyor...') {
        let overlay = document.getElementById('global-loading-overlay');
        
        if (!overlay) {
            overlay = this.createGlobalLoadingOverlay();
            document.body.appendChild(overlay);
        }

        const messageElement = overlay.querySelector('.loading-message');
        if (messageElement) {
            messageElement.textContent = message;
        }

        overlay.classList.add('active');
    }

    /**
     * Hide global loading overlay
     */
    hideGlobalLoading() {
        const overlay = document.getElementById('global-loading-overlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    }

    /**
     * Create global loading overlay element
     */
    createGlobalLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'global-loading-overlay';
        overlay.className = 'loading-overlay';
        
        overlay.innerHTML = `
            <div class="text-center fade-in">
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-100 to-green-100 rounded-full shadow-lg">
                        <div class="text-4xl heartbeat">🏥</div>
                    </div>
                </div>
                
                <div class="relative mb-6">
                    <div class="medical-spinner mx-auto"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-8 h-8 border-2 border-green-300 rounded-full animate-ping"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-2 h-2 bg-blue-600 rounded-full"></div>
                </div>
                
                <div class="text-xl font-semibold text-gray-700 mb-2">
                    <span class="loading-message">Yükleniyor</span>
                    <span class="loading-dots"></span>
                </div>
                
                <div class="flex items-center justify-center text-sm text-gray-500">
                    <span class="stethoscope-loading mr-2">🩺</span>
                    <span>Sistem hazırlanıyor, lütfen bekleyiniz</span>
                </div>
            </div>
        `;

        return overlay;
    }

    /**
     * Setup form submission handlers
     */
    setupFormSubmissionHandlers() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    this.activateButtonLoading(submitButton);
                }
            }
        });
    }

    /**
     * Show error message
     */
    showErrorMessage(message) {
        // Create or update error toast
        let toast = document.getElementById('error-toast');
        
        if (!toast) {
            toast = this.createErrorToast();
            document.body.appendChild(toast);
        }

        const messageElement = toast.querySelector('.toast-message');
        if (messageElement) {
            messageElement.textContent = message;
        }

        toast.classList.add('active');

        // Auto hide after 5 seconds
        setTimeout(() => {
            toast.classList.remove('active');
        }, 5000);
    }

    /**
     * Create error toast element
     */
    createErrorToast() {
        const toast = document.createElement('div');
        toast.id = 'error-toast';
        toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
        
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span class="toast-message">Bir hata oluştu!</span>
                <button onclick="this.parentElement.parentElement.classList.remove('active')" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Add CSS for active state
        const style = document.createElement('style');
        style.textContent = `
            #error-toast.active {
                transform: translateX(0);
            }
        `;
        document.head.appendChild(style);

        return toast;
    }

    /**
     * Add shimmer effect to loading elements
     */
    addShimmerEffect(element) {
        element.classList.add('shimmer');
        
        setTimeout(() => {
            element.classList.remove('shimmer');
        }, 2000);
    }

    /**
     * Create skeleton loading for tables
     */
    createTableSkeleton(tableElement, rows = 5) {
        const tbody = tableElement.querySelector('tbody');
        if (!tbody) return;

        const cols = tableElement.querySelectorAll('thead th').length;
        tbody.innerHTML = '';

        for (let i = 0; i < rows; i++) {
            const row = document.createElement('tr');
            
            for (let j = 0; j < cols; j++) {
                const cell = document.createElement('td');
                cell.className = 'px-6 py-4';
                cell.innerHTML = '<div class="skeleton skeleton-text"></div>';
                row.appendChild(cell);
            }
            
            tbody.appendChild(row);
        }
    }
}

// Initialize loading manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.loadingManager = new LoadingManager();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LoadingManager;
}