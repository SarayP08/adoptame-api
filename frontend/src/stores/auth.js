import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    logueado: false,
    usuario: null,
  }),

  actions: {
    async login(email, password) {
      try {
        console.log("ANTES FETCH");

        const res = await fetch("http://localhost/adoptame-api/backend/api/auth/login.php", {
          method: "POST",

          credentials: "include",

          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            email,
            password,
          }),
        });

        console.log("DESPUÉS FETCH");

        const text = await res.text();

        console.log("RESPUESTA CRUDA:");
        console.log(text);

        let data;

        try {
          data = JSON.parse(text);
        } catch (e) {
          console.error("JSON INVÁLIDO");

          return {
            ok: false,
            message: "El servidor no devuelve JSON válido",
          };
        }

        console.log("JSON PARSEADO:");
        console.log(data);

        if (data.ok) {
          this.logueado = true;
          this.usuario = data.usuario;

          return {
            ok: true,
          };
        } else {
          return {
            ok: false,
            message: data.message,
          };
        }
      } catch (error) {
        console.error(error);

        return {
          ok: false,
          message: "Error de conexión",
        };
      }
    },
    async logout() {
      try {
        await fetch("http://localhost/adoptame-api/backend/api/auth/logout.php", {
          method: "POST",
          credentials: "include",
        });
      } catch (error) {
        console.error(error);
      }

      this.logueado = false;
      this.usuario = null;
    },
    async comprobarSesion() {
      try {
        const res = await fetch("http://localhost/adoptame-api/backend/api/auth/revisarSesion.php", {
          credentials: "include",
        });

        const data = await res.json();

        this.logueado = data.logueado;

        if (data.usuario) {
          this.usuario = data.usuario;
        }
      } catch (error) {
        this.logueado = false;
        this.usuario = null;
      }
    },
  },
});
