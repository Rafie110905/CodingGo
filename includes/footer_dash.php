    <script>
        const themeToggle = document.getElementById('theme-toggle');
        if(themeToggle) {
            const themeText = themeToggle.querySelector('.theme-text');
            const sunIcon = themeToggle.querySelector('.sun-icon');
            const moonIcon = themeToggle.querySelector('.moon-icon');

            // Load saved theme
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
                if(themeText) themeText.textContent = 'Dark';
                if(sunIcon) sunIcon.style.display = 'none';
                if(moonIcon) moonIcon.style.display = 'block';
            }

            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                
                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                if (isDark) {
                    if(themeText) themeText.textContent = 'Dark';
                    if(sunIcon) sunIcon.style.display = 'none';
                    if(moonIcon) moonIcon.style.display = 'block';
                } else {
                    if(themeText) themeText.textContent = 'Light';
                    if(sunIcon) sunIcon.style.display = 'block';
                    if(moonIcon) moonIcon.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
