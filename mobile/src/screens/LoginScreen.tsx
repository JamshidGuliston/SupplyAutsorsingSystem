import React, { useState } from 'react';
import { View, Image, StyleSheet } from 'react-native';
import { HeroScreen } from '../components/HeroScreen';
import { Hero } from '../components/Hero';
import { AppText } from '../components/AppText';
import { Field } from '../components/Field';
import { Button } from '../components/Button';
import { Banner } from '../components/Banner';
import { MailIcon } from '../components/icons/MailIcon';
import { LockIcon } from '../components/icons/LockIcon';
import { useAuthStore } from '../auth/authStore';
import { login } from '../auth/loginApi';
import { userFacingError } from '../api/errors';
import { colors } from '../theme/colors';

const OVERLAP = 28;

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
      setError(userFacingError(e));
    } finally {
      setLoading(false);
    }
  };

  return (
    <HeroScreen
      sheet="white"
      overlap={OVERLAP}
      keyboardShouldPersistTaps="handled"
      hero={
        <Hero minHeight={250} circles={2} overlap={OVERLAP}>
          <View style={styles.badge}>
            <Image source={require('../assets/gt_mark.png')} style={styles.mark} resizeMode="contain" />
          </View>
          <AppText variant="heroTitleLg" color={colors.onHero} style={styles.title}>
            Xush kelibsiz!
          </AppText>
          <AppText variant="heroSub" color={colors.onHeroMuted}>
            Global Ta'm · Oshpaz kabineti
          </AppText>
        </Hero>
      }
    >
      <Banner tone="error" message={error} />
      <Field
        label="Email"
        icon={MailIcon}
        placeholder="email@globaltam.uz"
        autoCapitalize="none"
        autoCorrect={false}
        keyboardType="email-address"
        value={email}
        onChangeText={setEmail}
      />
      <Field
        label="Parol"
        icon={LockIcon}
        placeholder="••••••••"
        secureTextEntry
        value={password}
        onChangeText={setPassword}
      />
      <Button label="Kirish" onPress={onSubmit} loading={loading} />
      <AppText variant="caption" color={colors.inkFaint} style={styles.hint}>
        Parolni unutdingizmi? Texnologga murojaat qiling
      </AppText>
    </HeroScreen>
  );
}

const styles = StyleSheet.create({
  badge: {
    width: 64,
    height: 64,
    borderRadius: 20,
    backgroundColor: colors.card,
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
    marginTop: 24,
    elevation: 6,
    shadowColor: colors.ink,
    shadowOpacity: 0.15,
    shadowRadius: 10,
    shadowOffset: { width: 0, height: 4 },
  },
  mark: { width: 64, height: 64 },
  title: { marginTop: 16 },
  hint: { textAlign: 'center', marginTop: 22 },
});
