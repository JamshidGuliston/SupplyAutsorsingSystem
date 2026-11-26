import React, { ReactNode } from 'react';
import { Modal, View, StyleSheet } from 'react-native';
import { AppText } from './AppText';
import { Button } from './Button';
import { IconCircle } from './IconCircle';
import { LogoutIcon } from './icons/LogoutIcon';
import { colors } from '../theme/colors';
import { spacing } from '../theme/spacing';

export function ConfirmDialog({
  visible,
  title,
  children,
  confirmLabel,
  cancelLabel,
  onConfirm,
  onCancel,
  testID,
}: {
  visible: boolean;
  title: string;
  children: ReactNode;
  confirmLabel: string;
  cancelLabel: string;
  onConfirm: () => void;
  onCancel: () => void;
  testID?: string;
}) {
  return (
    <Modal
      transparent
      animationType="fade"
      visible={visible}
      onRequestClose={onCancel}
      statusBarTranslucent
    >
      <View testID={testID} style={styles.scrim}>
        <View style={styles.card}>
          <IconCircle tone="danger" size={52}>
            <LogoutIcon size={24} color={colors.danger} />
          </IconCircle>
          <AppText variant="sectionTitle" style={styles.title}>
            {title}
          </AppText>
          <AppText variant="body" color={colors.inkSoft} style={styles.body}>
            {children}
          </AppText>
          <View style={styles.actions}>
            <Button label={confirmLabel} variant="dangerSolid" onPress={onConfirm} />
            <Button label={cancelLabel} variant="ghost" onPress={onCancel} style={styles.cancel} />
          </View>
        </View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  scrim: {
    flex: 1,
    backgroundColor: colors.scrim,
    justifyContent: 'center',
    paddingHorizontal: spacing.xl,
  },
  card: {
    backgroundColor: colors.card,
    borderRadius: 22,
    padding: spacing.xl,
    alignItems: 'center',
  },
  title: { marginTop: spacing.lg, textAlign: 'center' },
  body: { marginTop: spacing.sm, textAlign: 'center' },
  actions: { alignSelf: 'stretch', marginTop: spacing.xl },
  cancel: { marginTop: spacing.sm },
});
