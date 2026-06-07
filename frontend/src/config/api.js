export const API_URL = import.meta.env.DEV ? "http://localhost/adoptame-api/backend" : "https://saraypa.iesallerulloa.es";

export const buildCatImage = (path) => {
  return `${API_URL}/uploads/gatos/${path}`;
};

export const buildUserImage = (path) => {
  return `${API_URL}/uploads/usuarios/${path}`;
};
