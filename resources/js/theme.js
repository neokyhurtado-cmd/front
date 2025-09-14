const KEY = "theme"; // "light" | "dark"

function applyTheme(t) {
  const root = document.documentElement;
  if (t === "dark") root.classList.add("dark");
  else root.classList.remove("dark");
}

function init() {
  // 1) respeta preferencia del usuario si no hay localStorage
  const stored = localStorage.getItem(KEY);
  const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
  const theme = stored ?? (prefersDark ? "dark" : "light");
  applyTheme(theme);
}

export function toggleTheme() {
  const isDark = document.documentElement.classList.toggle("dark");
  localStorage.setItem(KEY, isDark ? "dark" : "light");
}

init();

// expone para el botón
window.__toggleTheme = toggleTheme;