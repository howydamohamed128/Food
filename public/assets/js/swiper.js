/**
 * Custom swiper implementation for banner slider
 */
class Swiper {
    constructor(selector) {
        this.container = document.querySelector(selector);
        if (!this.container) return;
        
        this.slides = this.container.querySelectorAll('.swiper-slide');
        this.totalSlides = this.slides.length;
        this.currentIndex = 0;
        this.autoplayInterval = null;
        
        this.prevBtn = this.container.querySelector('.swiper-button-prev');
        this.nextBtn = this.container.querySelector('.swiper-button-next');
        this.pagination = this.container.querySelector('.swiper-pagination');
        
        this.init();
    }
    
    init() {
        // Create pagination bullets
        this.createPagination();
        
        // Add event listeners
        this.prevBtn.addEventListener('click', () => this.prevSlide());
        this.nextBtn.addEventListener('click', () => this.nextSlide());
        
        // Start autoplay
        this.startAutoplay();
        
        // Pause autoplay on hover
        this.container.addEventListener('mouseenter', () => this.stopAutoplay());
        this.container.addEventListener('mouseleave', () => this.startAutoplay());
    }
    
    createPagination() {
        this.pagination.innerHTML = '';
        for (let i = 0; i < this.totalSlides; i++) {
            const bullet = document.createElement('div');
            bullet.classList.add('swiper-pagination-bullet');
            if (i === this.currentIndex) {
                bullet.classList.add('active');
            }
            bullet.addEventListener('click', () => this.goToSlide(i));
            this.pagination.appendChild(bullet);
        }
    }
    
    updatePagination() {
        const bullets = this.pagination.querySelectorAll('.swiper-pagination-bullet');
        bullets.forEach((bullet, index) => {
            if (index === this.currentIndex) {
                bullet.classList.add('active');
            } else {
                bullet.classList.remove('active');
            }
        });
    }
    
    goToSlide(index) {
        // Hide all slides
        this.slides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Show current slide
        this.currentIndex = index;
        if (this.currentIndex < 0) this.currentIndex = this.totalSlides - 1;
        if (this.currentIndex >= this.totalSlides) this.currentIndex = 0;
        
        this.slides[this.currentIndex].classList.add('active');
        this.updatePagination();
    }
    
    nextSlide() {
        this.goToSlide(this.currentIndex + 1);
    }
    
    prevSlide() {
        this.goToSlide(this.currentIndex - 1);
    }
    
    startAutoplay() {
        this.stopAutoplay(); // Clear any existing interval
        this.autoplayInterval = setInterval(() => {
            this.nextSlide();
        }, 5000); // Change slide every 5 seconds
    }
    
    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
}

// Initialize swiper when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const banner = new Swiper('.swiper-container');
});
