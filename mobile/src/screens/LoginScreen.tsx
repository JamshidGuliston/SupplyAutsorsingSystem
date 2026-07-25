import React, { useState } from 'react';
import { View, Image, ScrollView, StyleSheet } from 'react-native';
import { Screen } from '../components/Screen';
import { Section } from '../components/Section';
import { PageHeader } from '../components/PageHeader';
import { Field } from '../components/Field';
import { Button } from '../components/Button';
import { Banner } from '../components/Banner';
import { useAuthStore } from '../auth/authStore';
import { login } from '../auth/loginApi';
import { mapServerError } from '../api/errors';

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
    <Screen>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={styles.scroll}>
        <View style={styles.logoWrap}>
          <Image
            source={require('../assets/global_tam_logo.jpg')}
            style={styles.logo}
            resizeMode="contain"
          />
        </View>
        <PageHeader eyebrow="Oshpaz kabineti" title="Kirish" />
        <Section style={styles.form}>
          <Banner tone="error" message={error} />
          <Field
            label="Email"
            placeholder="email@globaltam.uz"
            autoCapitalize="none"
            autoCorrect={false}
            keyboardType="email-address"
            value={email}
            onChangeText={setEmail}
          />
          <Field
            label="Parol"
            placeholder="••••••••"
            secureTextEntry
            value={password}
            onChangeText={setPassword}
          />
          <Button label="Kirish" onPress={onSubmit} loading={loading} />
        </Section>
      </ScrollView>
    </Screen>
  );
}

const styles = StyleSheet.create({
  scroll: { flexGrow: 1, justifyContent: 'center' },
  logoWrap: { alignItems: 'center', paddingTop: 26 },
  logo: { width: 240, height: 96 },
  form: { paddingTop: 2 },
});
