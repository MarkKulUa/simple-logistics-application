import Axios from 'axios'

const axios = Axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Logout logic
    }
    return Promise.reject(error)
  }
)

export default axios
