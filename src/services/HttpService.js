const baseUrl = 'http://localhost/vue-tailwind-admin-dashboard/api'

export default {
  get: async (url) => {
    const res = await fetch(`${baseUrl}${url}`, {
      credentials: 'include',
    })
    return res.json()
  },

  post: async (url, data) => {
    const isFormData = data instanceof FormData
    const config = {
      method: 'POST',
      credentials: 'include',
    }

    if (isFormData) {
      config.body = data
    } else {
      config.headers = {
        'Content-Type': 'application/json',
      }
      config.body = JSON.stringify(data)
    }

    const res = await fetch(`${baseUrl}${url}`, config)
    return res.json()
  },
}
