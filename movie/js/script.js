// Carousel functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-item');

function showSlide(index) {
    const carouselInner = document.getElementById('carouselInner');
    if (!carouselInner) return;
    
    const slides = carouselInner.children.length;
    if (index >= slides) currentSlide = 0;
    if (index < 0) currentSlide = slides - 1;
    
    carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
}

function changeSlide(direction) {
    currentSlide += direction;
    showSlide(currentSlide);
}

// Auto-play carousel
setInterval(() => {
    changeSlide(1);
}, 5000);

// Seat selection functionality
function selectSeat(seatElement) {
    if (seatElement.classList.contains('booked')) return;
    
    seatElement.classList.toggle('selected');
    updateBookingSummary();
}

function updateBookingSummary() {
    const selectedSeats = document.querySelectorAll('.seat.selected');
    const seatCount = selectedSeats.length;
    const ticketPrice = parseFloat(document.getElementById('ticketPrice')?.value || 0);
    const totalAmount = seatCount * ticketPrice;
    
    const seatCountElement = document.getElementById('selectedSeatsCount');
    const totalAmountElement = document.getElementById('totalAmount');
    
    if (seatCountElement) seatCountElement.textContent = seatCount;
    if (totalAmountElement) totalAmountElement.textContent = `₹${totalAmount.toFixed(2)}`;
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    for (let input of inputs) {
        if (!input.value.trim()) {
            alert(`Please fill in the ${input.name || 'required field'}`);
            input.focus();
            return false;
        }
    }
    
    return true;
}

// Search functionality
function searchMovies() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput?.value.toLowerCase() || '';
    const movieCards = document.querySelectorAll('.movie-card');
    
    movieCards.forEach(card => {
        const title = card.querySelector('.movie-title')?.textContent.toLowerCase() || '';
        const genre = card.querySelector('.movie-details')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchTerm) || genre.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add search listener
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', searchMovies);
    }
    
    // Initialize tooltips, modals, etc.
    console.log('MovieBook website initialized');
});
