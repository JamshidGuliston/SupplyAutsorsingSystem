import React, { useState } from 'react';
import { View, Text, Image, TextInput, StyleSheet } from 'react-native';
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
      <View style={styles.wrap}>
        <View style={styles.headerArea}>
          <Image
            source={require('../assets/global_tam_logo.jpg')}
            style={styles.logo}
            resizeMode="contain"
          />
          <Text style={styles.subtitle}>Kirish</Text>
        </View>
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
  wrap: { flex: 1, justifyContent: 'center', paddingHorizontal: 8 },
  headerArea: { alignItems: 'center', marginBottom: 24 },
  logo: { width: 280, height: 120, marginBottom: 12 },
  subtitle: { fontSize: 18, color: colors.inkMuted },
  input: {
    width: '100%',
    borderWidth: 1,
    borderColor: colors.rule,
    backgroundColor: '#fff',
    borderRadius: 10,
    paddingHorizontal: 16,
    paddingVertical: 16,
    fontSize: 17,
    marginBottom: 14,
  },
});
