document.addEventListener('DOMContentLoaded', () => {
  const adminTableBody = document.getElementById("adminTableBody");
  const editAdminModal = new bootstrap.Modal(document.getElementById("editAdminModal"));
  const addAdminModal = new bootstrap.Modal(document.getElementById("adminModal")); // Modal de agregar administrador

  let editMode = false;
  let editingId = null;

  // Función para cargar administradores
  async function loadAdmins() {
    try {
      const res = await fetch("api_admin/get_admins.php");
      const data = await res.json();
      adminTableBody.innerHTML = "";

      data.forEach((admin, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${index + 1}</td>
          <td>${admin.name}</td>
          <td>${admin.email}</td>
          <td>********</td> <!-- Aquí no mostramos la contraseña -->
          <td>
            <button class="btn btn-sm btn-warning me-1" data-id="${admin.id}" data-action="edit">
              <i class="bi bi-pencil-square"></i> Editar
            </button>
            <button class="btn btn-sm btn-danger" data-id="${admin.id}" data-name="${admin.name}" data-action="delete">
              <i class="bi bi-trash"></i> Eliminar
            </button>
          </td>
        `;
        adminTableBody.appendChild(row);
      });

      addEventListenersToButtons(); // Reasignar los eventos a los botones después de cargar la tabla
    } catch (err) {
      console.error("Error al cargar administradores:", err);
    }
  }

  // Función para agregar los event listeners a los botones de la tabla
  function addEventListenersToButtons() {
    // Botones para editar
    adminTableBody.querySelectorAll("button[data-action='edit']").forEach(btn => {
      btn.addEventListener("click", () => {
        const row = btn.closest("tr");
        document.getElementById("editAdminName").value = row.children[1].textContent;
        document.getElementById("editAdminEmail").value = row.children[2].textContent;
        document.getElementById("editAdminPassword").value = ""; // Limpiar contraseña
        editingId = btn.dataset.id;
        editMode = true;
        editAdminModal.show();
      });
    });

    // Botones para eliminar
    adminTableBody.querySelectorAll("button[data-action='delete']").forEach(btn => {
      btn.addEventListener("click", async () => {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        if (confirm(`¿Eliminar al administrador ${name}?`)) {
          try {
            const res = await fetch("api_admin/delete_admin.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ id })
            });
            const result = await res.json();
            if (result.success) {
              loadAdmins();
            } else {
              alert(result.error || "Error al eliminar.");
            }
          } catch (error) {
            console.error("Error al eliminar administrador:", error);
          }
        }
      });
    });
  }

  // Formulario para editar administrador
  const editAdminForm = document.getElementById("editAdminForm");
  if (editAdminForm) {
    editAdminForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const name = document.getElementById("editAdminName").value.trim();
      const email = document.getElementById("editAdminEmail").value.trim();
      const password = document.getElementById("editAdminPassword").value.trim();

      if (!name || !email) {
        alert("Todos los campos son obligatorios.");
        return;
      }

      const url = "api_admin/update_admin.php";
      const body = { name, email, id: editingId };
      if (password) body.password = password;

      try {
        const res = await fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(body)
        });
        const data = await res.json();

        if (res.ok && data.success) {
          loadAdmins(); // Recargar la lista de administradores
          editAdminModal.hide();
        } else {
          alert(data.error || "Error al guardar los cambios.");
        }
      } catch (err) {
        console.error("Error al enviar datos:", err);
        alert("Error de red o del servidor.");
      }
    });
  }

  // Formulario para agregar administrador
  const addAdminForm = document.getElementById("adminForm");
  if (addAdminForm) {
    addAdminForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const name = document.getElementById("adminName").value.trim();
      const email = document.getElementById("adminEmail").value.trim();
      const password = document.getElementById("adminPassword").value.trim();

      if (!name || !email || !password) {
        alert("Todos los campos son obligatorios.");
        return;
      }

      const url = "api_admin/register_admin.php"; // Asegúrate de que esta URL sea correcta
      const body = { name, email, password };

      try {
        const res = await fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(body)
        });
        const data = await res.json();

        if (res.ok && data.success) {
          loadAdmins(); // Recargar la lista de administradores
          addAdminModal.hide(); // Cerrar el modal de agregar

          // Limpiar campos del formulario
          document.getElementById("adminForm").reset();
        } else {
          alert(data.error || "Error al agregar el administrador.");
        }
      } catch (err) {
        console.error("Error al enviar datos:", err);
        alert("Error de red o del servidor.");
      }
    });

    // Limpiar los campos al cerrar el modal
    document.getElementById("adminModal").addEventListener("hidden.bs.modal", () => {
      document.getElementById("adminForm").reset(); // Limpiar los campos del formulario
    });
  }

  loadAdmins(); // Cargar los administradores al inicio
});
