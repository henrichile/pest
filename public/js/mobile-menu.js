window.addEventListener("load", function() {
    const toggle = document.getElementById("sidebar-toggle");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    if (toggle && sidebar && overlay) {
        toggle.onclick = function() {
            sidebar.classList.toggle("-translate-x-full");
            overlay.classList.toggle("hidden");
        };
        overlay.onclick = function() {
            sidebar.classList.add("-translate-x-full");
            overlay.classList.add("hidden");
        };
    }
});