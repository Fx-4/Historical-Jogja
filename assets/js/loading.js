// loading.js
class LoadingAnimation {
    constructor() {
        this.loadingContainer = document.querySelector('.my-loading-overlay');
        if (!this.loadingContainer) {
            this.loadingContainer = this.createLoadingElement();
        }
    }

    createLoadingElement() {
        // Create loading overlay if it doesn't exist
        const loadingHTML = `
            <div class="my-loading-overlay">
                <!-- Your existing loading HTML structure -->
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', loadingHTML);
        return document.querySelector('.my-loading-overlay');
    }

    show() {
        this.loadingContainer.classList.add('active');
    }

    hide() {
        this.loadingContainer.classList.remove('active');
    }
}

// Create global instance
window.loadingAnimation = new LoadingAnimation();

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    window.loadingAnimation.hide();
});