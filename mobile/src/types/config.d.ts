declare module 'react-native-config' {
  interface Env {
    API_BASE_URL: string;
    APP_ENV: 'development' | 'staging' | 'production';
  }
  const Config: Env;
  export default Config;
}
