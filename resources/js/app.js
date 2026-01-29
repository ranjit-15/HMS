import './bootstrap';

// Import page-specific modules
import { initHive, initCountdowns } from './hive.js';
import { initProfile } from './profile.js';

// Expose functions globally for Blade templates
window.initHive = initHive;
window.initCountdowns = initCountdowns;
window.initProfile = initProfile;

// Auto-initialize countdowns on any page that has them
document.addEventListener('DOMContentLoaded', () => {
    // Initialize countdowns if any exist on the page
    if (document.querySelector('[data-countdown]')) {
        initCountdowns();
    }
});
