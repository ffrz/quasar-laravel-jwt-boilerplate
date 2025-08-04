import apiClient from '../api/api';
import api from '../api/api';

interface RegisterData {
  name: string;
  email: string;
  password: string;
  confirm_password: string;
}

const registerService = {
  async register(userData: RegisterData) {
    const response = await apiClient.post('/reister ', userData);
    return response.data;
  },
};

export default registerService;
