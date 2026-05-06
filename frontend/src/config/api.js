export const API_URL = import.meta.env.DEV
    ? "http://localhost/adoptame-api/backend"
    : "";

export const buildUrl = (path) => `${API_URL}${path}`;