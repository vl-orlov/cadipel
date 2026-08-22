function loginAdm() {
    var login = document.getElementById('inputLogin').value.trim();
    var pass = document.getElementById('inputPassword').value.trim();
    var mensaje = document.getElementById('mensaje');

    mensaje.textContent = '';
    mensaje.classList.remove('login_message_error');

    fetch('includes/login_check.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ login: login, pass: pass })
    })
    .then(function (response) { return response.json(); })
    .then(function (data) {
        if (data.ok === 1) {
            window.location.href = 'index.php';
        } else if (data.locked) {
            mensaje.textContent = 'Demasiados intentos fallidos. Probá de nuevo en unos minutos.';
            mensaje.classList.add('login_message_error');
        } else {
            mensaje.textContent = 'Usuario o contraseña incorrectos.';
            mensaje.classList.add('login_message_error');
        }
    })
    .catch(function () {
        mensaje.textContent = 'Error de conexión. Intentá de nuevo.';
        mensaje.classList.add('login_message_error');
    });
}
