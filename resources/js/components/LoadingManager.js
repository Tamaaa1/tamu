/**
 * Loading Manager Component
 * Provides consistent loading indicators and states across the application
 */
class LoadingManager {
    constructor() {
        this.loadings = new Map();
        this.init();
    }

    init() {
        // Bind global methods
        window.showLoading = (target, message = 'Memuat...', type = 'spinner') => this.show(target, message, type);
        window.hideLoading = (target) => this.hide(target);
        window.showButtonLoading = (button, message = 'Menyimpan...') => this.showButtonLoading(button, message);
        window.hideButtonLoading = (button, originalText = null) => this.hideButtonLoading(button, originalText);
        window.showOverlayLoading = (container, message = 'Memproses...') => this.showOverlayLoading(container, message);
        window.hideOverlayLoading = (container) => this.hideOverlayLoading(container);
    }

    show(target, message = 'Memuat...', type = 'spinner') {
        const id = this.generateId();
        const element = this.createLoadingElement(message, type);
        const targetElement = this.getTargetElement(target);

        if (!targetElement) return null;

        // Position the loading element
        this.positionLoadingElement(element, targetElement, type);

        // Add to target
        targetElement.appendChild(element);
        targetElement.classList.add('loading-active');

        // Store reference
        this.loadings.set(id, {
            element,
            target: targetElement,
            type,
            originalDisplay: window.getComputedStyle(targetElement).display
        });

        return id;
    }

    hide(target) {
        const targetElement = this.getTargetElement(target);
        if (!targetElement) return;

        // Find and remove loading elements
        const loadingElements = targetElement.querySelectorAll('.loading-indicator, .loading-overlay');
        loadingElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });

        targetElement.classList.remove('loading-active');

        // Remove from map
        this.loadings.forEach((loading, id) => {
            if (loading.target === targetElement) {
                this.loadings.delete(id);
            }
        });
    }

    showButtonLoading(button, message = 'Menyimpan...') {
        const btn = this.getTargetElement(button);
        if (!btn) return null;

        const id = this.generateId();
        const originalText = btn.innerHTML;
        const originalDisabled = btn.disabled;

        // Update button content
        btn.innerHTML = `
            <span class="loading-spinner" style="display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: white; animation: spin 1s ease-in-out infinite; margin-right: 8px;"></span>
            ${message}
        `;

        btn.disabled = true;
        btn.classList.add('btn-loading');

        // Store reference
        this.loadings.set(id, {
            element: btn,
            target: btn,
            type: 'button',
            originalText,
            originalDisabled
        });

        return id;
    }

    hideButtonLoading(button, originalText = null) {
        const btn = this.getTargetElement(button);
        if (!btn) return;

        // Find loading reference
        let loadingRef = null;
        this.loadings.forEach((loading, id) => {
            if (loading.target === btn && loading.type === 'button') {
                loadingRef = loading;
                this.loadings.delete(id);
            }
        });

        if (loadingRef) {
            btn.innerHTML = originalText || loadingRef.originalText;
            btn.disabled = loadingRef.originalDisabled || false;
            btn.classList.remove('btn-loading');
        }
    }

    showOverlayLoading(container, message = 'Memproses...') {
        const targetElement = this.getTargetElement(container);
        if (!targetElement) return null;

        const id = this.generateId();
        const overlay = this.createOverlayElement(message);

        // Position overlay
        overlay.style.position = 'absolute';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.zIndex = '10';

        // Make container relatively positioned if not already
        const currentPosition = window.getComputedStyle(targetElement).position;
        if (currentPosition === 'static') {
            targetElement.style.position = 'relative';
        }

        targetElement.appendChild(overlay);
        targetElement.classList.add('loading-overlay-active');

        // Store reference
        this.loadings.set(id, {
            element: overlay,
            target: targetElement,
            type: 'overlay',
            originalPosition: currentPosition
        });

        return id;
    }

    hideOverlayLoading(container) {
        const targetElement = this.getTargetElement(container);
        if (!targetElement) return;

        // Find and remove overlay
        const overlay = targetElement.querySelector('.loading-overlay');
        if (overlay) {
            overlay.parentNode.removeChild(overlay);
        }

        targetElement.classList.remove('loading-overlay-active');

        // Restore original position if needed
        this.loadings.forEach((loading, id) => {
            if (loading.target === targetElement && loading.type === 'overlay') {
                if (loading.originalPosition === 'static') {
                    targetElement.style.position = '';
                }
                this.loadings.delete(id);
            }
        });
    }

    createLoadingElement(message, type) {
        const element = document.createElement('div');
        element.className = 'loading-indicator';

        switch (type) {
            case 'spinner':
                element.innerHTML = `
                    <div class="loading-spinner-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
                        <div class="loading-spinner" style="width: 40px; height: 40px; border: 4px solid rgba(0,123,255,0.1); border-radius: 50%; border-top-color: #007bff; animation: spin 1s linear infinite; margin-bottom: 12px;"></div>
                        <div class="loading-message" style="color: #666; font-size: 14px;">${message}</div>
                    </div>
                `;
                break;

            case 'dots':
                element.innerHTML = `
                    <div class="loading-dots" style="display: flex; align-items: center; justify-content: center; padding: 20px;">
                        <div class="dot" style="width: 8px; height: 8px; border-radius: 50%; background: #007bff; margin: 0 4px; animation: pulse 1.4s ease-in-out infinite both;"></div>
                        <div class="dot" style="width: 8px; height: 8px; border-radius: 50%; background: #007bff; margin: 0 4px; animation: pulse 1.4s ease-in-out 0.2s infinite both;"></div>
                        <div class="dot" style="width: 8px; height: 8px; border-radius: 50%; background: #007bff; margin: 0 4px; animation: pulse 1.4s ease-in-out 0.4s infinite both;"></div>
                        <span style="margin-left: 12px; color: #666; font-size: 14px;">${message}</span>
                    </div>
                `;
                break;

            case 'skeleton':
                element.innerHTML = `
                    <div class="skeleton-loader" style="padding: 20px;">
                        <div class="skeleton-line" style="height: 20px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 4px; margin-bottom: 12px;"></div>
                        <div class="skeleton-line" style="height: 16px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 4px; width: 80%; margin-bottom: 8px;"></div>
                        <div class="skeleton-line" style="height: 16px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 4px; width: 60%;"></div>
                    </div>
                `;
                break;
        }

        return element;
    }

    createOverlayElement(message) {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="overlay-content" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
                <div class="loading-spinner" style="width: 48px; height: 48px; border: 4px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: white; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div>
                <div class="loading-message" style="font-size: 16px; font-weight: 500;">${message}</div>
            </div>
        `;

        // Style the overlay
        overlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        `;

        return overlay;
    }

    positionLoadingElement(element, target, type) {
        const targetRect = target.getBoundingClientRect();
        const styles = window.getComputedStyle(target);

        switch (type) {
            case 'spinner':
            case 'dots':
                element.style.cssText += `
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 5;
                `;
                break;

            case 'skeleton':
                element.style.cssText += `
                    width: 100%;
                    position: relative;
                `;
                break;
        }
    }

    getTargetElement(target) {
        if (typeof target === 'string') {
            return document.querySelector(target);
        } else if (target instanceof HTMLElement) {
            return target;
        } else if (target instanceof jQuery && target.length > 0) {
            return target[0];
        }
        return null;
    }

    generateId() {
        return 'loading_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.loadingManager = new LoadingManager();
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }

    @keyframes skeleton-loading {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .loading-active {
        position: relative !important;
    }

    .loading-overlay-active {
        position: relative !important;
    }

    .btn-loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-loading:hover {
        transform: none !important;
    }
`;
document.head.appendChild(style);
