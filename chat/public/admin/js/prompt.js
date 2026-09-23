(function () {
    var form = document.getElementById('promptForm');
    if (!form) return;

    var textarea = document.getElementById('promptTextarea');
    var status = document.getElementById('promptStatus');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        status.textContent = 'Guardando…';
        status.classList.remove('login_message_error');

        fetch('api/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ instructions: textarea.value })
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.ok === 1) {
                status.textContent = 'Guardado. El asistente ya usa este texto.';
            } else {
                status.textContent = 'No se pudo guardar. Intentá de nuevo.';
                status.classList.add('login_message_error');
            }
        })
        .catch(function () {
            status.textContent = 'Error de conexión.';
            status.classList.add('login_message_error');
        });
    });
})();
