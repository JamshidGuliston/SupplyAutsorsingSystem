import { api } from '../api/client';
import { AuthUser } from './authStore';

interface LoginResponse {
  token: string;
  user: AuthUser;
}

export async function login(email: string, password: string): Promise<LoginResponse> {
  const resp = await api.post<LoginResponse>('/auth/login', { email, password });
  return resp.data;
}
