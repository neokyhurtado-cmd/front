import './bootstrap';
import 'flowbite';

// Modo oscuro persistente
(function () {
  const saved = localStorage.getItem('theme');
  const html = document.documentElement;
  if (saved === 'light') html.classList.remove('dark'); else html.classList.add('dark');
})();
window.toggleTheme = () => {
  const html = document.documentElement;
  const isDark = html.classList.contains('dark');
  html.classList.toggle('dark', !isDark);
  localStorage.setItem('theme', isDark ? 'light' : 'dark');
};
