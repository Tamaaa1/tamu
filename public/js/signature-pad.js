// Signature Pad Initialization and Management
class SignaturePadManager {
    constructor() {
        this.signaturePad = null;
        this.canvas = null;
        this.initialized = false;
    }

    // Initialize signature pad
    init(canvasElement) {
        if (!canvasElement) {
            console.error('Canvas element not found');
            return null;
        }

        this.canvas = canvasElement;
        this.signaturePad = new SignaturePad(this.canvas, {
            minWidth: 2.5,
            maxWidth: 4,
            penColor: "rgb(0, 0,0)",
            backgroundColor: "rgba(255, 255, 255, 0)"  // Transparent background
        });

        this.setupEventListeners();
        this.resizeCanvas();
        this.initialized = true;

        return this.signaturePad;
    }

    // Setup event listeners
    setupEventListeners() {
        // Clear signature
        const clearBtn = document.getElementById('clearSignature');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clear());
        }

        // Update signature input on drawing
        this.signaturePad.addEventListener('beginStroke', () => {
            document.querySelector('.signature-placeholder').style.display = 'none';
        });

        this.signaturePad.addEventListener('endStroke', () => {
            if (!this.signaturePad.isEmpty()) {
                const signatureData = this.signaturePad.toDataURL();
                document.getElementById('signatureInput').value = signatureData;
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => this.resizeCanvas());
    }

    // Resize canvas for high DPI displays
    resizeCanvas() {
        if (!this.canvas) return;

        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        this.canvas.width = this.canvas.offsetWidth * ratio;
        this.canvas.height = this.canvas.offsetHeight * ratio;
        this.canvas.getContext("2d").scale(ratio, ratio);
        
        if (this.signaturePad) {
            this.signaturePad.clear();
        }
    }

    // Clear signature
    clear() {
        if (this.signaturePad) {
            this.signaturePad.clear();
            document.getElementById('signatureInput').value = '';
            document.querySelector('.signature-placeholder').style.display = 'flex';
        }
    }

    // Check if signature is empty
    isEmpty() {
        return this.signaturePad ? this.signaturePad.isEmpty() : true;
    }

    // Get signature data URL
    toDataURL() {
        return this.signaturePad ? this.signaturePad.toDataURL() : '';
    }

    // Validate signature
    validate() {
        if (this.isEmpty()) {
            return 'Tanda tangan harus diisi';
        }
        return null;
    }
}

// Global signature pad instance
const signaturePadManager = new SignaturePadManager();

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signatureCanvas');
    if (canvas) {
        signaturePadManager.init(canvas);
    }
});

// Export for use in other files
window.SignaturePadManager = SignaturePadManager;
window.signaturePadManager = signaturePadManager;
