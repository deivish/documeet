import './bootstrap';

// Función para mostrar notificación toast
function mostrarNotificacionToast(notification) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 right-4 bg-white border-l-4 border-indigo-600 shadow-2xl rounded-lg p-4 max-w-md z-50 transform transition-all duration-300 translate-x-full';
    
    toast.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-800 mb-1">Nueva Invitación</h4>
                <p class="text-sm text-gray-600 mb-1">${notification.titulo || notification.mensaje}</p>
                <p class="text-xs text-gray-500">📅 ${notification.fecha_hora}</p>
                <a href="/reuniones/${notification.reunion_id}" 
                   class="inline-block mt-2 text-xs bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                    Ver reunión
                </a>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" 
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);

    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 8000);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    const userId = document.head.querySelector('meta[name="user-id"]')?.content;

    if (!userId) {
        console.log('❌ Usuario no autenticado');
        return;
    }

    console.log('🔔 Iniciando sistema de notificaciones para usuario:', userId);

    const bell = document.getElementById('notification-bell');
    const badge = document.getElementById('notification-count');
    const list = document.getElementById('notification-list');
    const dropdown = document.getElementById('notification-dropdown');

    // ✅ Escuchar notificaciones en el canal privado
    window.Echo.private(`users.${userId}`)
        .notification((notification) => {
            console.log('📩 Notificación recibida:', notification);

            // Mostrar toast
            mostrarNotificacionToast(notification);

            // Actualizar badge
            if (badge) {
                const currentCount = parseInt(badge.textContent || '0');
                badge.textContent = currentCount + 1;
                badge.classList.remove('hidden');
            }

            // Eliminar mensaje "sin notificaciones"
            const emptyMsg = list?.querySelector('li.text-gray-500');
            if (emptyMsg) emptyMsg.remove();

            // Agregar notificación a la lista del dropdown
            if (list) {
                const item = document.createElement('li');
                item.className = 'p-4 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer';
                item.innerHTML = `
                    <a href="/reuniones/${notification.reunion_id}">
                        <strong>${notification.titulo}</strong><br>
                        Fecha: ${notification.fecha_hora}
                    </a>
                `;
                list.prepend(item);
            }

            // Reproducir sonido (opcional)
            try {
                const audio = new Audio('/sounds/notification.mp3');
                audio.volume = 0.3;
                audio.play().catch(() => {});
            } catch (error) {
                console.log('No se pudo reproducir sonido');
            }
        });

    console.log('✅ Sistema de notificaciones iniciado');

    // Toggle dropdown
    if (bell && dropdown) {
        bell.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
});