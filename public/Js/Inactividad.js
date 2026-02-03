const TIEMPO_LIMITE = 20 * 60 * 1000; // 10 Minutos
let timeoutId;

function iniciarTemporizador() {
    // Detectar cualquier interacción del usuario
    document.addEventListener("mousemove", resetearTemporizador);
    document.addEventListener("keypress", resetearTemporizador);
    document.addEventListener("click", resetearTemporizador);
    
    establecerTimeout();
}

function establecerTimeout() {
    timeoutId = setTimeout(mostrarAlerta, TIEMPO_LIMITE);
}

function resetearTemporizador() {
    clearTimeout(timeoutId);
    establecerTimeout();
}

function mostrarAlerta() {
    // Usamos el confirm nativo del navegador (Sin librerías)
    const deseaContinuar = confirm("Tu sesión está a punto de expirar por inactividad. ¿Deseas seguir conectado?");

    if (deseaContinuar) {
        // Si el usuario acepta, notificamos al servidor para que PHP no cierre la sesión
        // Debes crear una ruta simple en tu controlador que devuelva un 200 OK
        fetch(base + "home/ping")
            .then(() => {
                console.log("Sesión renovada en el servidor");
                resetearTemporizador();
            })
            .catch(err => console.error("Error al renovar sesión", err));
    } else {
        // Si cancela o no responde, redirigimos al logout
        window.location.href = base + "logout";
    }
}

// Iniciar al cargar el archivo
iniciarTemporizador();