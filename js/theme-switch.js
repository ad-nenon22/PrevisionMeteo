document.addEventListener('DOMContentLoaded', () => {
  const checkbox = document.getElementById('checkbox');

  checkbox.addEventListener('change', () => {
    const newStyle = checkbox.checked ? 'dark.css' : 'styles.css';
    const url = new URL(window.location.href);
    url.searchParams.set('style', newStyle);
    window.location.href = url.toString(); // Redirige avec ?style=...
  });
});
