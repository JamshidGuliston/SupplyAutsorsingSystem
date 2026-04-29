import React, { useEffect } from 'react';
import { View, ActivityIndicator } from 'react-native';
import { useAuthStore } from '../auth/authStore';
import { useSessionRestore } from '../auth/useSessionRestore';
import { registerUnauthenticatedHandler } from '../api/client';
import { AuthStack } from './AuthStack';
import { MainTabs } from './MainTabs';

export function RootNavigator() {
  const status = useAuthStore((s) => s.status);
  const clearSession = useAuthStore((s) => s.clearSession);

  useSessionRestore();

  useEffect(() => {
    registerUnauthenticatedHandler(() => {
      void clearSession();
    });
  }, [clearSession]);

  if (status === 'restoring') {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  return status === 'authenticated' ? <MainTabs /> : <AuthStack />;
}
