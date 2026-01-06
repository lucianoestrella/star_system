<script>
        // Bloquear botão direito
        document.addEventListener('contextmenu', event => event.preventDefault());

        // Bloquear atalhos de desenvolvedor
        document.addEventListener('keydown', function(event) {
            if (event.keyCode === 123 || // F12
                (event.ctrlKey && event.shiftKey && (event.keyCode === 73 || event.keyCode === 74 || event.keyCode === 67)) || // Ctrl+Shift+I/J/C
                (event.ctrlKey && event.keyCode === 85) // Ctrl+U (Fonte)
            ) {
                event.preventDefault();
                return false;
            }
        });

        // Alerta de segurança no console para curiosos
        console.log("%c⚠️ PARE!", "color: red; font-size: 40px; font-weight: bold;");
        console.log("%cEste é um recurso de navegador voltado para desenvolvedores.", "font-size: 18px;");
    </script>
</body>
</html>