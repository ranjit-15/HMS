/**
 * Hive page functionality
 * - Booking modal open/close with conflict checking
 * - Countdown timers
 * - Zoom/pan controls
 * - Keyboard navigation
 * - Pending booking reconfirm prompt
 */

// Modal elements
let modal, tableInput, startInput, endInput, modalTableName, defaultMinutes;
let conflictWarning, submitBtn, currentTableId;

// Reconfirm modal elements
let reconfirmModal, reconfirmTableName, reconfirmCountdown, reconfirmBtn;
let pendingTimeoutMinutes = 15;
let reconfirmShown = false;
let currentPendingBookingId = null;

// Zoom/pan state
let scale = 1;
let translateX = 0;
let translateY = 0;
let isPanning = false;
let startX = 0;
let startY = 0;
let hiveContainer = null;
let hiveGrid = null;

// Debounce timer for conflict check
let conflictCheckTimer = null;

/**
 * Initialize hive functionality
 * @param {Object} config - Configuration object
 * @param {number} config.defaultBookingMinutes - Default booking duration in minutes
 * @param {number} config.pendingTimeoutMinutes - Minutes until pending booking expires
 */
export function initHive(config = {}) {
    defaultMinutes = config.defaultBookingMinutes || 60;
    pendingTimeoutMinutes = config.pendingTimeoutMinutes || 15;
    
    modal = document.getElementById('booking-modal');
    tableInput = document.getElementById('table_id');
    startInput = document.getElementById('start_at');
    endInput = document.getElementById('end_at');
    modalTableName = document.getElementById('modal-table-name');
    conflictWarning = document.getElementById('conflict-warning');
    submitBtn = document.getElementById('booking-submit');
    hiveContainer = document.getElementById('hive-container');
    hiveGrid = document.getElementById('hive-grid');
    
    // Reconfirm modal elements
    reconfirmModal = document.getElementById('reconfirm-modal');
    reconfirmTableName = document.getElementById('reconfirm-table-name');
    reconfirmCountdown = document.getElementById('reconfirm-countdown');
    reconfirmBtn = document.getElementById('reconfirm-btn');

    // Initialize countdown timers
    initCountdowns();
    
    // Initialize pending booking countdowns
    initPendingCountdowns();
    
    // Initialize pending expiry monitor (shows reconfirm prompt)
    initPendingExpiryMonitor();
    
    // Initialize zoom/pan
    initZoomPan();
    
    // Initialize keyboard navigation
    initKeyboardNav();
    
    // Add conflict check listeners
    if (startInput && endInput) {
        startInput.addEventListener('change', debounceConflictCheck);
        endInput.addEventListener('change', debounceConflictCheck);
    }

    // Expose functions globally for inline onclick handlers
    window.openBookingModal = openBookingModal;
    window.closeBookingModal = closeBookingModal;
    window.closeReconfirmModal = closeReconfirmModal;
    window.zoomIn = zoomIn;
    window.zoomOut = zoomOut;
    window.resetZoom = resetZoom;
}

/**
 * Open booking modal with table info
 * @param {HTMLElement} btn - Button element with data attributes
 */
export function openBookingModal(btn) {
    if (!modal) return;

    const now = new Date();
    const start = toLocalInput(now);
    const end = toLocalInput(new Date(now.getTime() + defaultMinutes * 60000));

    currentTableId = btn.dataset.tableId;
    tableInput.value = currentTableId;
    modalTableName.textContent = btn.dataset.tableName;
    startInput.value = start;
    endInput.value = end;
    
    // Reset conflict state
    hideConflictWarning();

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Focus first input for accessibility
    startInput.focus();
    
    // Check for conflicts with initial time
    checkConflict();
}

/**
 * Close booking modal
 */
export function closeBookingModal() {
    if (!modal) return;
    
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentTableId = null;
}

/**
 * Convert Date to datetime-local input format
 * @param {Date} date
 * @returns {string}
 */
function toLocalInput(date) {
    const pad = (n) => String(n).padStart(2, '0');
    const yyyy = date.getFullYear();
    const mm = pad(date.getMonth() + 1);
    const dd = pad(date.getDate());
    const hh = pad(date.getHours());
    const mi = pad(date.getMinutes());
    return `${yyyy}-${mm}-${dd}T${hh}:${mi}`;
}

/**
 * Initialize countdown timers for elements with [data-countdown]
 */
export function initCountdowns() {
    document.querySelectorAll('[data-countdown]').forEach(el => {
        const end = new Date(el.dataset.end);
        
        const tick = () => {
            const diff = end - new Date();
            
            if (diff <= 0) {
                el.textContent = 'Expired';
                return;
            }
            
            const hrs = Math.floor(diff / 3600000);
            const mins = Math.floor((diff % 3600000) / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            
            // Show hours if > 0, otherwise show minutes and seconds
            if (hrs > 0) {
                el.textContent = `${hrs}h ${mins}m left`;
            } else {
                el.textContent = `${mins}m ${secs}s left`;
            }
            
            requestAnimationFrame(() => setTimeout(tick, 500));
        };
        
        tick();
    });
}

/**
 * Initialize countdown timers for pending booking elements with [data-pending-countdown]
 */
function initPendingCountdowns() {
    document.querySelectorAll('[data-pending-countdown]').forEach(el => {
        const expiry = new Date(el.dataset.expiry);
        
        const tick = () => {
            const diff = expiry - new Date();
            
            if (diff <= 0) {
                el.textContent = 'Expired!';
                el.classList.add('text-red-600', 'font-bold');
                return;
            }
            
            const mins = Math.floor(diff / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            
            el.textContent = `Confirm within ${mins}m ${secs}s`;
            
            // Flash warning color when under 3 minutes
            if (mins < 3) {
                el.classList.add('animate-pulse');
            }
            
            requestAnimationFrame(() => setTimeout(tick, 500));
        };
        
        tick();
    });
}

/**
 * Monitor pending bookings and show reconfirm prompt before expiry
 */
function initPendingExpiryMonitor() {
    const pendingSeats = document.querySelectorAll('[data-pending-expiry]');
    
    if (pendingSeats.length === 0) return;
    
    // Find the first pending booking (user should only have one)
    const seat = pendingSeats[0];
    const expiry = new Date(seat.dataset.pendingExpiry);
    const bookingId = seat.dataset.bookingId;
    const tableName = seat.dataset.tableName;
    
    currentPendingBookingId = bookingId;
    
    // Set up the reconfirm button to submit the confirm form
    if (reconfirmBtn) {
        reconfirmBtn.addEventListener('click', () => {
            const form = document.getElementById(`confirm-form-${bookingId}`);
            if (form) {
                form.submit();
            }
        });
    }
    
    const checkExpiry = () => {
        const diff = expiry - new Date();
        
        // If already expired, don't show modal
        if (diff <= 0) {
            return;
        }
        
        // Show reconfirm modal when 3 minutes or less remain
        const warningThreshold = 3 * 60 * 1000; // 3 minutes
        
        if (diff <= warningThreshold && !reconfirmShown) {
            showReconfirmModal(tableName, expiry);
            reconfirmShown = true;
        }
        
        // Continue checking every second
        if (diff > 0) {
            setTimeout(checkExpiry, 1000);
        }
    };
    
    // Start monitoring
    checkExpiry();
}

/**
 * Show the reconfirm modal with countdown
 */
function showReconfirmModal(tableName, expiry) {
    if (!reconfirmModal) return;
    
    if (reconfirmTableName) {
        reconfirmTableName.textContent = tableName;
    }
    
    reconfirmModal.classList.remove('hidden');
    reconfirmModal.classList.add('flex');
    
    // Play alert sound (if available)
    try {
        const audio = new Audio('/sounds/alert.mp3');
        audio.volume = 0.5;
        audio.play().catch(() => {}); // Ignore if sound not available
    } catch (e) {
        // Sound not available
    }
    
    // Start countdown in modal
    const tickModal = () => {
        const diff = expiry - new Date();
        
        if (diff <= 0) {
            if (reconfirmCountdown) {
                reconfirmCountdown.textContent = 'EXPIRED';
                reconfirmCountdown.classList.add('text-red-600');
            }
            // Auto-close and reload after expiry
            setTimeout(() => {
                window.location.reload();
            }, 2000);
            return;
        }
        
        const mins = Math.floor(diff / 60000);
        const secs = Math.floor((diff % 60000) / 1000);
        
        if (reconfirmCountdown) {
            reconfirmCountdown.textContent = `${mins}:${String(secs).padStart(2, '0')}`;
        }
        
        requestAnimationFrame(() => setTimeout(tickModal, 500));
    };
    
    tickModal();
    
    // Focus the confirm button for accessibility
    if (reconfirmBtn) {
        reconfirmBtn.focus();
    }
}

/**
 * Close the reconfirm modal
 */
export function closeReconfirmModal() {
    if (!reconfirmModal) return;
    
    reconfirmModal.classList.add('hidden');
    reconfirmModal.classList.remove('flex');
}

// ==========================================
// ZOOM & PAN FUNCTIONALITY
// ==========================================

/**
 * Initialize zoom and pan controls
 */
function initZoomPan() {
    if (!hiveContainer || !hiveGrid) return;
    
    // Mouse wheel zoom
    hiveContainer.addEventListener('wheel', (e) => {
        if (e.ctrlKey) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            setScale(scale + delta);
        }
    }, { passive: false });
    
    // Pan with mouse drag
    hiveContainer.addEventListener('mousedown', startPan);
    document.addEventListener('mousemove', doPan);
    document.addEventListener('mouseup', endPan);
    
    // Touch support
    hiveContainer.addEventListener('touchstart', handleTouchStart, { passive: false });
    hiveContainer.addEventListener('touchmove', handleTouchMove, { passive: false });
    hiveContainer.addEventListener('touchend', endPan);
}

function startPan(e) {
    // Only pan with middle mouse button or when holding shift
    if (e.button === 1 || e.shiftKey) {
        e.preventDefault();
        isPanning = true;
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
        hiveContainer.style.cursor = 'grabbing';
    }
}

function doPan(e) {
    if (!isPanning) return;
    e.preventDefault();
    translateX = e.clientX - startX;
    translateY = e.clientY - startY;
    applyTransform();
}

function endPan() {
    isPanning = false;
    if (hiveContainer) {
        hiveContainer.style.cursor = '';
    }
}

// Touch handling
let lastTouchDistance = 0;

function handleTouchStart(e) {
    if (e.touches.length === 2) {
        e.preventDefault();
        lastTouchDistance = getTouchDistance(e.touches);
    } else if (e.touches.length === 1) {
        isPanning = true;
        startX = e.touches[0].clientX - translateX;
        startY = e.touches[0].clientY - translateY;
    }
}

function handleTouchMove(e) {
    if (e.touches.length === 2) {
        e.preventDefault();
        const distance = getTouchDistance(e.touches);
        const delta = (distance - lastTouchDistance) * 0.01;
        setScale(scale + delta);
        lastTouchDistance = distance;
    } else if (e.touches.length === 1 && isPanning) {
        e.preventDefault();
        translateX = e.touches[0].clientX - startX;
        translateY = e.touches[0].clientY - startY;
        applyTransform();
    }
}

function getTouchDistance(touches) {
    const dx = touches[0].clientX - touches[1].clientX;
    const dy = touches[0].clientY - touches[1].clientY;
    return Math.sqrt(dx * dx + dy * dy);
}

function setScale(newScale) {
    scale = Math.max(0.5, Math.min(2, newScale));
    applyTransform();
    updateZoomDisplay();
}

function applyTransform() {
    if (hiveGrid) {
        hiveGrid.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    }
}

function updateZoomDisplay() {
    const display = document.getElementById('zoom-level');
    if (display) {
        display.textContent = Math.round(scale * 100) + '%';
    }
}

export function zoomIn() {
    setScale(scale + 0.25);
}

export function zoomOut() {
    setScale(scale - 0.25);
}

export function resetZoom() {
    scale = 1;
    translateX = 0;
    translateY = 0;
    applyTransform();
    updateZoomDisplay();
}

// ==========================================
// KEYBOARD NAVIGATION
// ==========================================

/**
 * Initialize keyboard navigation for seats
 */
function initKeyboardNav() {
    const seats = document.querySelectorAll('[data-seat]');
    if (seats.length === 0) return;
    
    seats.forEach((seat, index) => {
        seat.setAttribute('tabindex', '0');
        seat.setAttribute('role', 'button');
        
        seat.addEventListener('keydown', (e) => {
            const cols = parseInt(document.getElementById('hive-grid')?.dataset.cols || '4');
            let targetIndex = index;
            
            switch (e.key) {
                case 'ArrowRight':
                    targetIndex = Math.min(index + 1, seats.length - 1);
                    break;
                case 'ArrowLeft':
                    targetIndex = Math.max(index - 1, 0);
                    break;
                case 'ArrowDown':
                    targetIndex = Math.min(index + cols, seats.length - 1);
                    break;
                case 'ArrowUp':
                    targetIndex = Math.max(index - cols, 0);
                    break;
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    if (seat.dataset.state === 'available') {
                        openBookingModal(seat);
                    }
                    return;
                default:
                    return;
            }
            
            e.preventDefault();
            seats[targetIndex].focus();
        });
    });
}

// ==========================================
// CONFLICT CHECKING
// ==========================================

/**
 * Debounce conflict check to avoid too many API calls
 */
function debounceConflictCheck() {
    if (conflictCheckTimer) {
        clearTimeout(conflictCheckTimer);
    }
    conflictCheckTimer = setTimeout(checkConflict, 500);
}

/**
 * Check for booking conflicts via API
 */
async function checkConflict() {
    if (!currentTableId || !startInput?.value || !endInput?.value) return;
    
    const startAt = startInput.value;
    const endAt = endInput.value;
    
    // Basic validation
    if (new Date(startAt) >= new Date(endAt)) {
        showConflictWarning('End time must be after start time');
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch(`/api/hive/availability?table_id=${currentTableId}&start_at=${encodeURIComponent(startAt)}&end_at=${encodeURIComponent(endAt)}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });
        
        if (!response.ok) {
            // API not available yet, allow submission
            hideConflictWarning();
            return;
        }
        
        const data = await response.json();
        
        if (data.conflict) {
            showConflictWarning(data.message || 'This time slot conflicts with an existing booking');
        } else {
            hideConflictWarning();
        }
    } catch (error) {
        // API error - allow submission, server will validate
        hideConflictWarning();
        console.warn('Conflict check failed:', error);
    }
}

function showConflictWarning(message) {
    if (conflictWarning) {
        conflictWarning.textContent = message;
        conflictWarning.classList.remove('hidden');
    }
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

function hideConflictWarning() {
    if (conflictWarning) {
        conflictWarning.classList.add('hidden');
    }
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// ==========================================
// EVENT LISTENERS
// ==========================================

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeBookingModal();
    }
});

// Close modal when clicking backdrop
document.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeBookingModal();
    }
});

// Close modal when clicking backdrop
document.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeBookingModal();
    }
});
