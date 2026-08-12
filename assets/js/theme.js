// Theme Management
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (!document.body.hasAttribute('data-theme')) {
        document.body.setAttribute('data-theme', savedTheme);
    }
    updateThemeUI(savedTheme);
}

// Initialize theme when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTheme);
} else {
    initTheme();
}

function toggleTheme() {
    const currentTheme = document.body.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeUI(newTheme);
}

function updateThemeUI(theme) {
    const icons = document.querySelectorAll('.theme-toggle i');
    const texts = document.querySelectorAll('.theme-toggle span');
    
    icons.forEach(icon => {
        if (theme === 'dark') {
            icon.className = 'fas fa-sun';
        } else {
            icon.className = 'fas fa-moon';
        }
    });
    
    texts.forEach(text => {
        if (theme === 'dark') {
            text.textContent = 'Light';
        } else {
            text.textContent = 'Dark';
        }
    });
}
