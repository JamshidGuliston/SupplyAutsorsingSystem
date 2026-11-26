import React from 'react';
import { StyleSheet } from 'react-native';
import { Card } from '../Card';
import { EmptyState } from '../EmptyState';
import { Button } from '../Button';
import { WifiOffIcon } from '../icons/WifiOffIcon';
import { colors } from '../../theme/colors';

// Bosh va Davomat uchun umumiy "yuklab bo'lmadi" kartasi. `onRetry` berilmasa tugma chizilmaydi
// (masalan, bog'cha biriktirilmagan — qayta urinish foyda bermaydi).
export function LoadFailed({ message, onRetry }: { message: string; onRetry?: () => void }) {
  return (
    <Card style={styles.card}>
      <EmptyState
        icon={<WifiOffIcon size={26} color={colors.danger} />}
        tone="danger"
        size={56}
        title="Ma'lumotni yuklab bo'lmadi"
        body={message}
        action={
          onRetry ? (
            <Button label="Qayta urinish" variant="outline" size="sm" onPress={onRetry} />
          ) : undefined
        }
      />
    </Card>
  );
}

const styles = StyleSheet.create({
  // Gorizontal ichki chekinishni EmptyState o'zi beradi.
  card: { paddingVertical: 22 },
});
