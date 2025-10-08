/**
 * Custom Notification Manager
 * Provides consistent, accessible, and user-friendly notifications
 */
class NotificationManager {
    constructor() {
        this.container = null;
        this.notifications = new Map();
        this.init();
    }

    init() {
        // Create notification container
        this.createContainer();

        // Bind global methods
        window.showNotification = (message, type = 'info', options = {}) => this.show(message, type, options);
        window.showSuccess = (message, options = {}) => this.show(message, 'success', options);
        window.showError = (message, options = {}) => this.show(message, 'error', options);
        window.showWarning = (message, options = {}) => this.show(message, 'warning', options);
        window.showInfo = (message, options = {}) => this.show(message, 'info', options);
    }

    createContainer() {
        // Remove existing container if it exists
        const existing = document.getElementById('notification-container');
        if (existing) {
            existing.remove();
        }

        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.className = 'notification-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            pointer-events: none;
        `;

        document.body.appendChild(this.container);
    }

    show(message, type = 'info', options = {}) {
        const defaults = {
            duration: 5000,
            persistent: false,
            closable: true,
            position: 'top-right',
            icon: this.getIconForType(type),
            actions: []
        };

        const config = { ...defaults, ...options };
        const id = this.generateId();

        const notification = this.createNotificationElement(message, type, config, id);
        this.container.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Auto remove if not persistent
        if (!config.persistent && config.duration > 0) {
            setTimeout(() => {
                this.remove(id);
            }, config.duration);
        }

        // Store notification
        this.notifications.set(id, { element: notification, config });

        return id;
    }

    createNotificationElement(message, type, config, id) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.id = `notification-${id}`;
        notification.style.cssText = `
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 10px;
            padding: 16px 20px;
            border-left: 4px solid ${this.getColorForType(type)};
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: auto;
            position: relative;
            overflow: hidden;
        `;

        notification.innerHTML = `
            <div class="notification-content" style="display: flex; align-items: flex-start; gap: 12px;">
                <div class="notification-icon" style="flex-shrink: 0; margin-top: 2px;">
                    <i class="${config.icon}" style="font-size: 18px; color: ${this.getColorForType(type)};"></i>
                </div>
                <div class="notification-body" style="flex: 1; min-width: 0;">
                    <div class="notification-message" style="margin: 0; font-size: 14px; line-height: 1.4; color: #333;">
                        ${message}
                    </div>
                    ${config.actions.length > 0 ? this.createActions(config.actions) : ''}
                </div>
                ${config.closable ? `
                    <button class="notification-close" style="background: none; border: none; cursor: pointer; padding: 4px; margin-left: 8px; opacity: 0.6; transition: opacity 0.2s;">
                        <i class="fas fa-times" style="font-size: 12px; color: #666;"></i>
                    </button>
                ` : ''}
            </div>
            ${!config.persistent && config.duration > 0 ? `
                <div class="notification-progress" style="position: absolute; bottom: 0; left: 0; height: 3px; background: ${this.getColorForType(type)}; width: 100%; transform-origin: left; transition: transform ${config.duration}ms linear;"></div>
            ` : ''}
        `;

        // Bind close event
        if (config.closable) {
            const closeBtn = notification.querySelector('.notification-close');
            closeBtn.addEventListener('click', () => this.remove(id));
            closeBtn.addEventListener('mouseenter', () => closeBtn.style.opacity = '1');
            closeBtn.addEventListener('mouseleave', () => closeBtn.style.opacity = '0.6');
        }

        // Start progress bar animation
        if (!config.persistent && config.duration > 0) {
            setTimeout(() => {
                const progress = notification.querySelector('.notification-progress');
                if (progress) {
                    progress.style.transform = 'scaleX(0)';
                }
            }, 10);
        }

        return notification;
    }

    createActions(actions) {
        return `
            <div class="notification-actions" style="margin-top: 12px; display: flex; gap: 8px;">
                ${actions.map(action => `
                    <button class="notification-action btn btn-sm ${action.class || 'btn-primary'}" data-action="${action.key}">
                        ${action.label}
                    </button>
                `).join('')}
            </div>
        `;
    }

    remove(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;

        const element = notification.element;
        element.classList.remove('show');
        element.classList.add('hide');

        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
            this.notifications.delete(id);
        }, 300);
    }

    clear() {
        this.notifications.forEach((notification, id) => {
            this.remove(id);
        });
    }

    getIconForType(type) {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-triangle',
            warning: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    getColorForType(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        return colors[type] || colors.info;
    }

    generateId() {
        return 'notification_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    // Utility methods for common use cases
    confirm(message, onConfirm, onCancel = null) {
        return this.show(message, 'warning', {
            persistent: true,
            actions: [
                { key: 'confirm', label: 'Ya', class: 'btn-danger' },
                { key: 'cancel', label: 'Batal', class: 'btn-secondary' }
            ]
        });
    }

    loading(message = 'Memproses...') {
        return this.show(message, 'info', {
            persistent: true,
            icon: 'fas fa-spinner fa-spin',
            closable: false
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.notificationManager = new NotificationManager();
});

// Add CSS styles
const style = document.createElement('style');
style.textContent = `
    .notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification.hide {
        transform: translateX(100%);
        opacity: 0;
    }

    .notification-action {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.2s;
    }

    .notification-action:hover {
        opacity: 0.8;
    }

    .notification-action:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }
`;
document.head.appendChild(style);
