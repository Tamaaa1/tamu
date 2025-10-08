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
