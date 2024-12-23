class GalleryManager {
    constructor() {
        this.initializeElements();
        this.initializeEventListeners();
        this.currentImageIndex = 0;
        this.images = [];
    }

    initializeElements() {
        this.elements = {
            galleryModal: document.getElementById('galleryModal'),
            modalContent: document.querySelector('.modal-content'),
            modalGalleryGrid: document.querySelector('.modal-gallery-grid'),
            modalClose: document.querySelector('.modal-close'),
            imageModal: document.getElementById('imageModal'),
            modalImage: document.getElementById('modalImage'),
            modalCaption: document.getElementById('modalCaption'),
            modalBuilding: document.getElementById('modalBuilding'),
            searchInput: document.getElementById('searchInput'),
            categoryFilter: document.getElementById('categoryFilter'),
            sortFilter: document.getElementById('sortFilter')
        };
    }

    initializeEventListeners() {
        // Modal close events
        if (this.elements.modalClose) {
            this.elements.modalClose.addEventListener('click', () => this.closeModal());
        }

        // Close modal when clicking outside
        if (this.elements.galleryModal) {
            this.elements.galleryModal.addEventListener('click', (e) => {
                if (e.target === this.elements.galleryModal) {
                    this.closeModal();
                }
            });
        }

        // Keyboard events
        document.addEventListener('keydown', (e) => {
            if (!this.elements.galleryModal.classList.contains('active')) return;
            
            switch(e.key) {
                case 'Escape':
                    this.closeModal();
                    break;
                case 'ArrowLeft':
                    this.previousImage();
                    break;
                case 'ArrowRight':
                    this.nextImage();
                    break;
            }
        });

        // Filter events
        const filters = [this.elements.categoryFilter, this.elements.sortFilter];
        filters.forEach(filter => {
            if (filter) {
                filter.addEventListener('change', () => this.applyFilters());
            }
        });
    }

    async viewGallery(buildingId) {
        if (!buildingId) return;

        try {
            // Show loading state
            this.showLoading();

            const response = await fetch(`get-building-gallery.php?id=${buildingId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Invalid response type - expected JSON");
            }

            const data = await response.json();
            
            if (!data || !Array.isArray(data.images)) {
                throw new Error("Invalid data format received");
            }

            this.images = data.images;
            this.renderGallery();
            this.openModal();

        } catch (error) {
            console.error('Error loading gallery:', error);
            this.showError('Failed to load gallery images. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    showLoading() {
        if (this.elements.modalGalleryGrid) {
            this.elements.modalGalleryGrid.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Loading gallery...</p>
                </div>
            `;
        }
    }

    hideLoading() {
        const spinner = this.elements.modalGalleryGrid?.querySelector('.loading-spinner');
        if (spinner) {
            spinner.remove();
        }
    }

    showError(message) {
        if (this.elements.modalGalleryGrid) {
            this.elements.modalGalleryGrid.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>${message}</p>
                </div>
            `;
        }
    }

    renderGallery() {
        if (!this.elements.modalGalleryGrid || !this.images.length) return;

        this.elements.modalGalleryGrid.innerHTML = this.images.map((image, index) => `
            <div class="modal-gallery-item">
                <img src="../uploads/gallery/${image.image_path}" 
                     alt="${image.caption || ''}"
                     loading="lazy"
                     onclick="gallery.viewImage(${index})">
                ${image.caption ? `
                    <div class="image-caption">${image.caption}</div>
                ` : ''}
            </div>
        `).join('');
    }

    viewImage(index) {
        if (!this.images[index]) return;

        const image = this.images[index];
        this.currentImageIndex = index;

        if (this.elements.modalImage) {
            this.elements.modalImage.src = `../uploads/gallery/${image.image_path}`;
            this.elements.modalImage.alt = image.caption || '';
        }

        if (this.elements.modalCaption) {
            this.elements.modalCaption.textContent = image.caption || '';
        }

        if (this.elements.imageModal) {
            this.elements.imageModal.classList.add('active');
        }
    }

    nextImage() {
        this.viewImage((this.currentImageIndex + 1) % this.images.length);
    }

    previousImage() {
        this.viewImage((this.currentImageIndex - 1 + this.images.length) % this.images.length);
    }

    openModal() {
        if (this.elements.galleryModal) {
            this.elements.galleryModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal() {
        if (this.elements.galleryModal) {
            this.elements.galleryModal.classList.remove('active');
            document.body.style.overflow = '';
        }
        if (this.elements.imageModal) {
            this.elements.imageModal.classList.remove('active');
        }
    }

    applyFilters() {
        const filters = {
            category: this.elements.categoryFilter?.value || '',
            sort: this.elements.sortFilter?.value || '',
            search: this.elements.searchInput?.value || ''
        };

        const queryString = Object.entries(filters)
            .filter(([_, value]) => value)
            .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
            .join('&');

        window.location.href = `gallery.php?${queryString}`;
    }
}

// Initialize gallery and make it globally available
const gallery = new GalleryManager();

// Global function for use in HTML
window.viewGallery = (buildingId) => gallery.viewGallery(buildingId);

// Initialize lazy loading when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize image loading states
    document.querySelectorAll('.gallery-card-image img').forEach(img => {
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', () => img.classList.add('loaded'));
        }
    });
});