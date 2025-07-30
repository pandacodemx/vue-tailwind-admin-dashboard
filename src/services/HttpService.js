const baseUrl = 'http://localhost/sistema-barberia/vue-tailwind-admin-dashboard/api'; 

export default {
  get: async (url) => {
    const res = await fetch(`${baseUrl}${url}`);
    return res.json();
  },

  post: async (url, data) => {
    const res = await fetch(`${baseUrl}${url}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    return res.json();
  },
};
