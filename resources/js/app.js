import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    },
});
console.log(window.Echo);
// Esperar a que el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    const userId = document.head.querySelector('meta[name="user-id"]')?.content;

    if (!userId) return;

    const bell = document.getElementById('notification-bell');
    const badge = document.getElementById('notification-count');
    const list = document.getElementById('notification-list');
    const dropdown = document.getElementById('notification-dropdown');

    // Escuchar notificaciones privadas para el usuario autenticado
    Echo.private(`App.Models.User.${userId}`).notification((notification) => {
        // Actualizar contador
        if (badge) {
            badge.classList.remove('hidden');
            badge.textContent = parseInt(badge.textContent || '0') + 1;
        }

        // Eliminar el mensaje de "sin notificaciones" si existe
        const emptyMsg = list?.querySelector('li.text-gray-500');
        if (emptyMsg) emptyMsg.remove();

        // Crear y agregar el nuevo ítem de notificación
        const item = document.createElement('li');
        item.className = 'p-4 text-sm hover:bg-gray-100 cursor-pointer border-b';
        item.innerHTML = `
            <div class="font-medium">${notification.titulo}</div>
            <div class="text-xs text-gray-500">${notification.fecha_hora}</div>
        `;
        list?.prepend(item);

        // Alerta opcional
        // alert(`Nueva invitación: ${notification.titulo}`);
    });

    // Alternar dropdown de notificaciones
    if (bell && dropdown) {
        bell.addEventListener('click', () => {
            dropdown.classList.toggle('hidden');
        });
    }
});
