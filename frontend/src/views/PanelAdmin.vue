<template>
  <main class="admin-page">
    <section class="admin-hero">
      <div>
        <p class="admin-label">Panel de administración</p>
        <h1>Bienvenido al panel admin</h1>
        <p class="admin-description">
          Gestiona gatos, solicitudes de adopción y mensajes desde un único lugar.
        </p>
      </div>
    </section>

    <section class="dashboard-grid">
      <article class="dashboard-card cats-card">
        <div class="card-top">
          <div class="dashboard-icon">
            <i class="bi bi-heart-fill"></i>
          </div>

          <span class="dashboard-badge">CRUD</span>
        </div>

        <h2>Gestión de gatos</h2>
        <p>
          Administra las fichas de los gatos disponibles para adopción:
          crear, consultar, editar o eliminar.
        </p>

        <div class="action-list">
          <button class="action-btn" type="button">
            <i class="bi bi-card-list"></i>
            Ver gatos
          </button>

          <RouterLink class="action-btn" to="/añadirGato">
            <i class="bi bi-plus-circle"></i>
            Añadir gato
          </RouterLink>

          <button class="action-btn" type="button">
            <i class="bi bi-pencil-square"></i>
            Editar gato
          </button>

          <button class="action-btn danger" type="button">
            <i class="bi bi-trash3"></i>
            Eliminar gato
          </button>
        </div>
      </article>

      <article class="dashboard-card requests-card">
        <div class="card-top">
          <div class="dashboard-icon">
            <i class="bi bi-file-earmark-text-fill"></i>
          </div>

          <span class="dashboard-badge">Solicitudes</span>
        </div>

        <h2>Solicitudes de adopción</h2>
        <p>
          Revisa las peticiones enviadas por personas interesadas en adoptar
          y controla su estado.
        </p>

        <div class="action-list">
          <button class="action-btn" type="button">
            <i class="bi bi-inbox-fill"></i>
            Ver solicitudes
          </button>

          <button class="action-btn" type="button">
            <i class="bi bi-check-circle"></i>
            Aceptadas
          </button>

          <button class="action-btn" type="button">
            <i class="bi bi-hourglass-split"></i>
            Pendientes
          </button>

          <button class="action-btn danger" type="button">
            <i class="bi bi-x-circle"></i>
            Rechazadas
          </button>
        </div>
      </article>

      <article class="dashboard-card messages-card">
        <div class="card-top">
          <div class="dashboard-icon">
            <i class="bi bi-chat-dots-fill"></i>
          </div>

          <span class="dashboard-badge">Mensajes</span>
        </div>

        <h2>Mensajería</h2>
        <p>
          Consulta mensajes recibidos desde contacto, dudas sobre adopciones
          o comunicaciones pendientes.
        </p>

        <div class="action-list">
          <button class="action-btn" type="button">
            <i class="bi bi-envelope-fill"></i>
            Bandeja de entrada
          </button>

          <button class="action-btn" type="button">
            <i class="bi bi-reply-fill"></i>
            Respondidos
          </button>

          <button class="action-btn" type="button">
            <i class="bi bi-envelope-exclamation-fill"></i>
            Sin responder
          </button>

          <button class="action-btn danger" type="button">
            <i class="bi bi-archive-fill"></i>
            Archivados
          </button>
        </div>
      </article>
    </section>

    <section class="summary-section">
      <div class="summary-header">
        <h2>Resumen rápido</h2>
        <span>Vista de ejemplo</span>
      </div>

      <div class="summary-grid">
        <article class="summary-card">
          <i class="bi bi-heart-fill"></i>
          <div>
            <strong>12</strong>
            <p>Gatos publicados</p>
          </div>
        </article>

        <article class="summary-card">
          <i class="bi bi-hourglass-split"></i>
          <div>
            <strong>5</strong>
            <p>Solicitudes pendientes</p>
          </div>
        </article>

        <article class="summary-card">
          <i class="bi bi-envelope-exclamation-fill"></i>
          <div>
            <strong>3</strong>
            <p>Mensajes sin responder</p>
          </div>
        </article>
      </div>
    </section>
  </main>
</template>

<style scoped>
.admin-page {
  min-height: calc(100vh - 80px);
  padding: 40px;
  background: linear-gradient(135deg, #fff8d6, #f8e8d8);
}

.admin-hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  padding: 32px;
  margin-bottom: 32px;
  background-color: #ffffff;
  border-radius: 24px;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.admin-label {
  margin-bottom: 8px;
  color: #df9800;
  font-family: 'coolvetica', sans-serif;
  font-size: 18px;
}

.admin-hero h1 {
  margin: 0;
  color: #423430;
  font-family: 'coolvetica', sans-serif;
  font-size: 44px;
}

.admin-description {
  margin-top: 12px;
  margin-bottom: 0;
  color: #654236;
  font-size: 18px;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-bottom: 32px;
}

.dashboard-card {
  padding: 28px;
  background-color: #ffffff;
  border: 2px solid transparent;
  border-radius: 24px;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.dashboard-card:hover {
  transform: translateY(-6px);
  border-color: #df9800;
}

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 22px;
}

.dashboard-icon {
  width: 62px;
  height: 62px;
  display: grid;
  place-items: center;
  color: #654236;
  background-color: #faf8b3;
  border-radius: 20px;
  font-size: 30px;
}

.dashboard-badge {
  padding: 6px 14px;
  color: #df9800;
  background-color: #fff3c4;
  border-radius: 999px;
  font-family: 'coolvetica', sans-serif;
  font-size: 14px;
}

.dashboard-card h2 {
  margin-bottom: 12px;
  color: #423430;
  font-family: 'coolvetica', sans-serif;
  font-size: 28px;
}

.dashboard-card p {
  min-height: 72px;
  color: #654236;
  font-size: 16px;
}

.action-list {
  display: grid;
  gap: 10px;
  margin-top: 20px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 11px 16px;
  color: #654236;
  background-color: #faf8b3;
  border: none;
  border-radius: 999px;
  font-family: 'coolvetica', sans-serif;
  font-size: 17px;
  text-align: left;
  text-decoration: none;
}

.action-btn:hover {
  background-color: #f1dc7a;
}

.action-btn.danger {
  color: #ffffff;
  background-color: #b85c4c;
}

.action-btn.danger:hover {
  background-color: #984638;
}

.summary-section {
  padding: 28px;
  background-color: #ffffff;
  border-radius: 24px;
  box-shadow: 0 12px 30px rgba(66, 52, 48, 0.12);
}

.summary-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 22px;
}

.summary-header h2 {
  margin: 0;
  color: #423430;
  font-family: 'coolvetica', sans-serif;
  font-size: 30px;
}

.summary-header span {
  color: #df9800;
  font-family: 'coolvetica', sans-serif;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

.summary-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 22px;
  background-color: #fff8d6;
  border-radius: 20px;
}

.summary-card i {
  color: #df9800;
  font-size: 34px;
}

.summary-card strong {
  display: block;
  color: #423430;
  font-family: 'coolvetica', sans-serif;
  font-size: 32px;
  line-height: 1;
}

.summary-card p {
  margin: 6px 0 0;
  color: #654236;
}

@media (max-width: 1100px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .dashboard-card p {
    min-height: auto;
  }
}

@media (max-width: 768px) {
  .admin-page {
    padding: 24px 16px;
  }

  .summary-grid {
    grid-template-columns: 1fr;
  }

  .admin-hero h1 {
    font-size: 34px;
  }

  .summary-header {
    align-items: flex-start;
    flex-direction: column;
    gap: 6px;
  }
}
</style>