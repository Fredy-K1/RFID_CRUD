document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const msgBox = document.getElementById("loginMsg");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    msgBox.textContent = "Validando...";
    msgBox.classList.remove("text-danger");
    msgBox.classList.add("text-secondary");

    try {
      const res = await fetch("login_process.php", {
        method: "POST",
        body: new URLSearchParams(data),
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      });

      const result = await res.json();

      if (result.success) {
        msgBox.classList.remove("text-danger", "text-secondary");
        msgBox.classList.add("text-success");
        msgBox.textContent = "Acceso exitoso. Redirigiendo...";
        setTimeout(() => {
          window.location.href = "../index.php";
        }, 1000);
      } else {
        msgBox.classList.remove("text-success", "text-secondary");
        msgBox.classList.add("text-danger");
        msgBox.textContent = result.message || "Error al iniciar sesión.";
      }

    } catch (error) {
      msgBox.classList.remove("text-success");
      msgBox.classList.add("text-danger");
      msgBox.textContent = "Error en el servidor.";
    }
  });
});
