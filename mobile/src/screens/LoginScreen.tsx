import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet } from 'react-native';
import { ScreenContainer } from '../components/ScreenContainer';
import { PrimaryButton } from '../components/PrimaryButton';
import { ErrorBanner } from '../components/ErrorBanner';
import { useAuthStore } from '../auth/authStore';
import { login } from '../auth/loginApi';
import { mapServerError } from '../api/errors';
import { colors } from '../theme/colors';

export function LoginScreen() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const setSession = useAuthStore((s) => s.setSession);

  const onSubmit = async () => {
    setError(null);
    if (!email.trim() || !password) {
      setError('Email va parolni kiriting');
      return;
    }
    setLoading(true);
    try {
      const { token, user } = await login(email.trim(), password);
      await setSession(user, token);
    } catch (e: any) {
      setError(mapServerError(e?.response?.data));
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScreenContainer>
      <View style={styles.center}>
        <Text style={styles.title}>Chef Mobile</Text>
        <Text style={styles.subtitle}>Kirish</Text>
        <ErrorBanner message={error} />
        <TextInput
          style={styles.input}
          placeholder="Email"
          autoCapitalize="none"
          autoCorrect={false}
          keyboardType="email-address"
          value={email}
          onChangeText={setEmail}
        />
        <TextInput
          style={styles.input}
          placeholder="Parol"
          secureTextEntry
          value={password}
          onChangeText={setPassword}
        />
        <PrimaryButton label="Kirish" onPress={onSubmit} loading={loading} />
      </View>
    </ScreenContainer>
  );
}

const styles = StyleSheet.create({
  center: { flex: 1, justifyContent: 'center' },
  title: { fontSize: 28, fontWeight: '700', color: colors.textPrimary, marginBottom: 4 },
  subtitle: { fontSize: 16, color: colors.textMuted, marginBottom: 24 },
  input: {
    borderWidth: 1,
    borderColor: colors.border,
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    marginBottom: 12,
  },
});
