// Theme toggle
function toggleTheme() {
  const html = document.documentElement;
  const currentTheme = html.getAttribute('data-theme');
  const isDark = currentTheme === 'dark';
  const newTheme = isDark ? 'light' : 'dark';
  
  html.setAttribute('data-theme', newTheme);
  localStorage.setItem('firstmediator_theme', newTheme);
  
  const themeIcon = document.getElementById('theme-icon');
  if (themeIcon) {
    themeIcon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
  }
  
  // Update logo display
  html.style.setProperty('--logo-light-display', newTheme === 'dark' ? 'none' : 'block');
  html.style.setProperty('--logo-dark-display', newTheme === 'dark' ? 'block' : 'none');
}

// Mobile Menu Toggle
function toggleMobileMenu() {
  const menu = document.getElementById('mobile-nav');
  const openIcon = document.getElementById('menu-icon-open');
  const closeIcon = document.getElementById('menu-icon-close');
  
  if (!menu) return;
  
  const isOpen = menu.classList.contains('open');
  
  if (isOpen) {
    menu.classList.remove('open');
    if (openIcon) openIcon.style.display = 'block';
    if (closeIcon) closeIcon.style.display = 'none';
  } else {
    menu.classList.add('open');
    if (openIcon) openIcon.style.display = 'none';
    if (closeIcon) closeIcon.style.display = 'block';
  }
}

// Ensure theme is applied on load (already handled in auth layout, but good for welcome)
document.addEventListener('DOMContentLoaded', () => {
  const theme = localStorage.getItem('firstmediator_theme') || 
               (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  document.documentElement.setAttribute('data-theme', theme);
  
  const isDark = theme === 'dark';
  document.documentElement.style.setProperty('--logo-light-display', isDark ? 'none' : 'block');
  document.documentElement.style.setProperty('--logo-dark-display', isDark ? 'block' : 'none');
  
  const themeIcon = document.getElementById('theme-icon');
  if (themeIcon) {
    themeIcon.textContent = isDark ? '☀️' : '🌙';
  }
});
