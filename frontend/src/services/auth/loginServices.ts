import apiClient from '../api/api';

interface LoginCredentials {
  email: string;
  password: string;
}

const loginService = {
  async login(credentialas: LoginCredentials) {
    const response = await apiClient.post('./login', credentialas);
    return response.data;
  },
};
export default loginService;
