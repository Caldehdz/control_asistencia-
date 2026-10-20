// Confirmación antes de cambiar el estado de un empleado
function confirmarAccion(mensaje) {
    return confirm(mensaje || "¿Está seguro de realizar esta acción?");
}