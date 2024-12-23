// gallery-utils.js
class GalleryUtils {
    static formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    static truncateText(text, length = 100) {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
    }

    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    static buildQueryString(params) {
        return Object.entries(params)
            .filter(([_, value]) => value)
            .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
            .join('&');
    }
}

// Class untuk manajemen Modal
// modal-manager.js
class ModalManager {
    constructor() {
        this.activeModal = null;
        this.setupKeyboardListener();
    }

    open(modalElement) {
        if (!modalElement) return;
        
        this.activeModal = modalElement;
        modalElement.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Focus trap
        const focusableElements = modalElement.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length) {
            focusableElements[0].focus();
        }
    }

    close(modalElement) {
        if (!modalElement) return;
        
        modalElement.classList.remove('active');
        document.body.style.overflow = '';
        this.activeModal = null;
    }

    setupKeyboardListener() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModal) {
                this.close(this.activeModal);
            }
        });
    }
}

// Class utama untuk Gallery
class GalleryManager {
    constructor() {
        this.modalManager = new ModalManager();
        this.initializeElements();
        this.initializeEventListeners();
        this.images = [];
    }

    initializeElements() {
        this.elements = {
            galleryModal: document.getElementById('galleryModal'),
            modalGalleryGrid: document.querySelector('.gallery-grid'),
            modalClose: document.querySelector('.modal-close'),
            imageModal: document.getElementById('imageModal'),
            modalImage: document.getElementById('modalImage'),
            modalCaption: document.getElementById('modalCaption'),
            modalBuilding: document.getElementById('modalBuilding')
        };
    }

    
    initializeEventListeners() {
        // Filter change events
        if (this.elements.categoryFilter) {
            this.elements.categoryFilter.addEventListener('change', () => this.applyFilters());
        }

        if (this.elements.sortFilter) {
            this.elements.sortFilter.addEventListener('change', () => this.applyFilters());
        }

        // Search events
        if (this.elements.searchInput) {
            this.elements.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.applyFilters();
            });
        }

        if (this.elements.searchButton) {
            this.elements.searchButton.addEventListener('click', () => this.applyFilters());
        }

        // Modal close events
        if (this.elements.modalClose) {
            this.elements.modalClose.addEventListener('click', () => {
                this.closeModal();
            });
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
                if (e.key === 'Escape') {
                    this.closeModal();
               }
            });
            

        // Initialize lazy loading
        this.initializeLazyLoading();
    }    initializeEventListeners() {
        // Filter change events
        if (this.elements.categoryFilter) {
            this.elements.categoryFilter.addEventListener('change', () => this.applyFilters());
        }

        if (this.elements.sortFilter) {
            this.elements.sortFilter.addEventListener('change', () => this.applyFilters());
        }

        // Search events
        if (this.elements.searchInput) {
            this.elements.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.applyFilters();
            });
        }

        if (this.elements.searchButton) {
            this.elements.searchButton.addEventListener('click', () => this.applyFilters());
        }

        // Modal close events
        if (this.elements.modalClose) {
            this.elements.modalClose.addEventListener('click', () => {
                this.modalManager.close(this.elements.galleryModal);
            });
        }

        // Close modal when clicking outside
        if (this.elements.galleryModal) {
            this.elements.galleryModal.addEventListener('click', (e) => {
                if (e.target === this.elements.galleryModal) {
                    this.modalManager.close(this.elements.galleryModal);
                }
            });
        }

        // Initialize lazy loading
        this.initializeLazyLoading();
    }

    initializeLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            observer.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    

    async fetchGalleryImages(buildingId) {
        const response = await fetch(`get-building-gallery.php?id=${buildingId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("Invalid response type - expected JSON");
        }

        return await response.json();
    } catch (error) {
        console.error('Fetch Error:', error);
        throw error;
    }



    async loadGallery(buildingId) {
        try {
            const modal = this.elements.galleryModal;
            const galleryGrid = this.elements.modalGalleryGrid;
            
            if (!modal || !galleryGrid) {
                throw new Error('Required elements not found');
            }

            modal.classList.add('active');
            galleryGrid.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Memuat galeri...</p>
                </div>
            `;

            const response = await fetch(`get-building-gallery.php?id=${buildingId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Invalid response type - expected JSON");
            }

            let data;
            try {
                data = await response.json();
            } catch (e) {
                console.error("JSON Parse Error:", e);
                throw new Error("Failed to parse server response");
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            if (data.status === 'success' && Array.isArray(data.images)) {
                this.images = data.images;
                if (data.images.length === 0) {
                    galleryGrid.innerHTML = `
                        <div class="empty-gallery">
                            <i class="fas fa-images"></i>
                            <p>Tidak ada foto dalam galeri ini</p>
                        </div>
                    `;
                } else {
                    galleryGrid.innerHTML = this.images.map((image, index) => `
                        <div class="gallery-item" onclick="gallery.previewImage('${image.image_path}', '${image.caption || ''}')">
                            <img src="../uploads/gallery/${image.image_path}" 
                                 alt="${image.caption || ''}"
                                 loading="lazy">
                            ${image.caption ? `
                                <div class="image-caption">
                                    ${image.caption}
                                </div>
                            ` : ''}
                        </div>
                    `).join('');
                }
            } else {
                throw new Error("Invalid data format from server");
            }

        } catch (error) {
            console.error('Gallery Load Error:', error);
            if (this.elements.modalGalleryGrid) {
                this.elements.modalGalleryGrid.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Error: ${error.message}</p>
                        <button onclick="gallery.loadGallery(${buildingId})" class="retry-button">
                            <i class="fas fa-redo"></i> Coba Lagi
                        </button>
                    </div>
                `;
            }
        }
    }

        previewImage(imagePath, caption) {
        if (this.elements.modalImage) {
            this.elements.modalImage.src = `../uploads/gallery/${imagePath}`;
        }
        if (this.elements.modalCaption) {
            this.elements.modalCaption.textContent = caption || '';
        }
        if (this.elements.imageModal) {
            this.elements.imageModal.classList.add('active');
        }
    }

    closeModal() {
        if (this.elements.galleryModal) {
            this.elements.galleryModal.classList.remove('active');
        }
        if (this.elements.imageModal) {
            this.elements.imageModal.classList.remove('active');
        }
    }
    renderGallery() {
        if (!this.images || this.images.length === 0) {
            this.elements.modalGalleryGrid.innerHTML = `
                <div class="empty-gallery">
                    <i class="fas fa-images"></i>
                    <p>Tidak ada gambar dalam galeri ini</p>
                </div>
            `;
            return;
        }

        this.elements.modalGalleryGrid.innerHTML = this.images.map((image, index) => `
            <div class="modal-gallery-item">
                <img src="../uploads/gallery/${image.image_path}" 
                     alt="${image.caption || ''}"
                     onclick="gallery.viewImage(${index})"
                     loading="lazy">
                ${image.caption ? `
                    <div class="image-caption">${image.caption}</div>
                ` : ''}
            </div>
        `).join('');
    }


    generateGalleryHTML() {
        return this.images.map((image, index) => `
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
        const image = this.images[index];
        if (!image) return;

        if (this.elements.modalImage) {
            this.elements.modalImage.src = `../uploads/gallery/${image.image_path}`;
        }

        if (this.elements.modalCaption) {
            this.elements.modalCaption.textContent = image.caption || '';
        }

        this.elements.imageModal.classList.add('active');
    }

    openModal() {
        this.elements.galleryModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        this.elements.galleryModal.classList.remove('active');
        if (this.elements.imageModal) {
            this.elements.imageModal.classList.remove('active');
        }
        document.body.style.overflow = '';
    }



    nextImage() {
        this.viewImage((this.currentImageIndex + 1) % this.images.length);
    }

    previousImage() {
        this.viewImage((this.currentImageIndex - 1 + this.images.length) % this.images.length);
    }

    applyFilters() {
        const filters = {
            category: this.elements.categoryFilter?.value || '',
            sort: this.elements.sortFilter?.value || '',
            search: this.elements.searchInput?.value || ''
        };

        window.location.href = 'gallery.php?' + Object.entries(filters)
            .filter(([_, value]) => value)
            .map(([key, value]) => `${key}=${encodeURIComponent(value)}`)
            .join('&');
    }
}

// Initialize gallery and make it globally available
const gallery = new GalleryManager();

// Expose necessary methods to window
window.viewGallery = (buildingId) => gallery.loadGallery(buildingId);
window.viewImage = (index) => gallery.viewImage(index);

// Initialize on DOM load
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

// Add this to your existing JavaScript
let galleryImages = [];

async function viewGallery(buildingId) {
    try {
        // Show loading state
        const modal = document.getElementById('galleryModal');
        const galleryGrid = document.getElementById('modalGalleryGrid');
        modal.classList.add('active');
        
        galleryGrid.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
            </div>
        `;

        // Fetch gallery data
        const response = await fetch(`get-building-gallery.php?id=${buildingId}`);
        const data = await response.json();

        if (data.status === 'success') {
            galleryImages = data.images;
            renderGalleryGrid(galleryImages);
        } else {
            throw new Error(data.message || 'Failed to load gallery');
        }
    } catch (error) {
        document.getElementById('modalGalleryGrid').innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <p>${error.message}</p>
            </div>
        `;
    }
}

function renderGalleryGrid(images) {
    const galleryGrid = document.getElementById('modalGalleryGrid');
    
    if (!images || images.length === 0) {
        galleryGrid.innerHTML = `
            <div class="empty-gallery">
                <i class="fas fa-images"></i>
                <p>Tidak ada foto dalam galeri ini</p>
            </div>
        `;
        return;
    }

    galleryGrid.innerHTML = images.map((image, index) => `
        <div class="gallery-item" onclick="previewImage('${image.image_path}', '${image.caption || ''}')">
            <img src="../uploads/gallery/${image.image_path}" 
                 alt="${image.caption || ''}"
                 loading="lazy">
            ${image.caption ? `
                <div class="image-caption">
                    ${image.caption}
                </div>
            ` : ''}
        </div>
    `).join('');
}

function previewImage(imagePath, caption) {
    const previewModal = document.getElementById('imagePreviewModal');
    const previewImage = document.getElementById('previewImage');
    const previewCaption = document.getElementById('previewCaption');
    
    previewImage.src = `../uploads/gallery/${imagePath}`;
    previewCaption.textContent = caption;
    previewModal.classList.add('active');
}

function closeImagePreview() {
    document.getElementById('imagePreviewModal').classList.remove('active');
}

function closeModal() {
    document.getElementById('galleryModal').classList.remove('active');
    document.getElementById('imagePreviewModal').classList.remove('active');
    galleryImages = [];
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeModal();
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

