document.addEventListener('DOMContentLoaded', () => {
  const uuidInput = document.getElementById('uuidInput');
  const ESP32_IP = "http://192.168.137.102";
  let pollInterval;
  let currentUsers = [];

  // 1. Polling optimizado
  function setupPolling() {
    clearInterval(pollInterval);
    pollInterval = setInterval(async () => {
      try {
        const response = await fetch(`${ESP32_IP}/uuid`);
        if (!response.ok) throw new Error("Error en la respuesta");
        const uuid = await response.text();

        // Solo actualizar si el UUID es diferente y no vacío
        if (uuid && uuid !== localStorage.getItem('uuid')) {
          uuidInput.value = uuid;
          localStorage.setItem('uuid', uuid);
          showAlert("Nuevo UUID detectado", "info");
        }
      } catch (error) {
        console.error("Error en polling:", error);
        if (!localStorage.getItem('uuid')) {
          showAlert("Error conectando al ESP32", "danger");
        }
      }
    }, 2000);
  }

  // 2. Carga y renderizado usuarios
  async function loadUsers() {
    try {
      const response = await fetch('api/get_users.php');
      if (!response.ok) throw new Error("Error en la respuesta");

      const data = await response.json();
      if (data.status === 'error') throw new Error(data.message);

      currentUsers = data.data || [];
      renderUsers();
    } catch (error) {
      console.error("Error cargando usuarios:", error);
      showAlert("Error al cargar usuarios", "danger");
    }
  }

  function renderUsers() {
    const tbody = document.getElementById('usersBody');
    tbody.innerHTML = currentUsers.map(user => `
      <tr data-id="${user.id}">
        <td>${user.id}</td>
        <td>${user.uuid}</td>
        <td class="user-name">${user.name}</td>
        <td>${new Date(user.registered_at).toLocaleString()}</td>
        <td>
          <button class="btn btn-warning btn-sm" onclick="editUser(${user.id}, '${escapeString(user.name)}')">
            <i class="bi bi-pencil-square"></i>
          </button>
          <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
    `).join('');
  }

  // 3. CRUD: registro, edición y eliminación
  async function registerUser(formData) {
    try {
      const response = await fetch('api/register.php', {
        method: 'POST',
        body: formData
      });
      if (!response.ok) throw new Error("Error en la respuesta");

      const result = await response.json();
      showAlert(result.message, result.status);

      if (result.status === 'success') {
        await loadUsers();
        resetForm();
      }
    } catch (error) {
      console.error("Error registrando usuario:", error);
      showAlert("Error en el registro", "danger");
    }
  }

  async function deleteUser(id) {
    if (!confirm("¿Eliminar este usuario?")) return;

    try {
      const formData = new FormData();
      formData.append('id', id);

      const response = await fetch('api/delete_user.php', {
        method: 'POST',
        body: formData
      });
      if (!response.ok) throw new Error("Error en la respuesta");

      const result = await response.json();
      showAlert(result.message, result.status);

      if (result.status === 'success') {
        currentUsers = currentUsers.filter(user => user.id !== id);
        renderUsers();
      }
    } catch (error) {
      console.error("Error eliminando usuario:", error);
      showAlert("Error al eliminar", "danger");
    }
  }

  async function editUser(id, oldName) {
    const newName = prompt("Nuevo nombre:", oldName);
    if (!newName || newName.trim() === oldName) return;

    try {
      const formData = new FormData();
      formData.append('id', id);
      formData.append('name', newName.trim());

      const response = await fetch('api/update_user.php', {
        method: 'POST',
        body: formData
      });
      if (!response.ok) throw new Error("Error en la respuesta");

      const result = await response.json();
      showAlert(result.message, result.status);

      if (result.status === 'success') {
        const userIndex = currentUsers.findIndex(user => user.id === id);
        if (userIndex !== -1) {
          currentUsers[userIndex].name = newName;
          renderUsers();
        }
      }
    } catch (error) {
      console.error("Error actualizando usuario:", error);
      showAlert("Error al actualizar", "danger");
    }
  }

  // Helpers
  function resetForm() {
    document.getElementById('registerForm').reset();
    uuidInput.value = '';
    localStorage.removeItem('uuid');
  }

  function escapeString(str) {
    // Escapa comillas simples y dobles para evitar problemas en HTML/JS
    return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
  }

  // Mejor manejo de alertas para evitar sobreescribir al anterior
  function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = `alert-${Date.now()}`;
    alertContainer.innerHTML = `
      <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    setTimeout(() => {
      const alert = document.getElementById(alertId);
      if (alert) {
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 1000);
      }
    }, 5000);
  }

  // Modo oscuro
  function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
  }

  // Exponer funciones globales para botones inline
  window.deleteUser = deleteUser;
  window.editUser = editUser;
  window.toggleDarkMode = toggleDarkMode;

  // Inicialización principal
  function init() {
    const storedUUID = localStorage.getItem('uuid');
    if (storedUUID) uuidInput.value = storedUUID;

    // Activar modo oscuro si estaba guardado
    if (localStorage.getItem('darkMode') === 'true') {
      document.body.classList.add('dark-mode');
    }

    setupPolling();
    loadUsers();

    document.getElementById('registerForm').addEventListener('submit', (e) => {
      e.preventDefault();
      registerUser(new FormData(e.target));
    });
  }

  init();
});
