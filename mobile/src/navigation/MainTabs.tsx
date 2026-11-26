import React from 'react';
import { View, StyleSheet } from 'react-native';
import Svg, { Defs, LinearGradient, Rect, Stop } from 'react-native-svg';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { HomeScreen } from '../screens/HomeScreen';
import { AttendanceScreen } from '../screens/AttendanceScreen';
import { NotificationsScreen } from '../screens/NotificationsScreen';
import { ProfileScreen } from '../screens/ProfileScreen';
import { HomeIcon } from '../components/icons/HomeIcon';
import { PinIcon } from '../components/icons/PinIcon';
import { BellIcon } from '../components/icons/BellIcon';
import { UserIcon } from '../components/icons/UserIcon';
import { IconProps } from '../components/icons/types';
import { colors } from '../theme/colors';
import { typography } from '../theme/typography';
import { hit } from '../theme/spacing';

const Tab = createBottomTabNavigator();

const SHADOW_H = 6;

// Android elevation tepaga soya tashlamaydi — tab paneli tepasida 6 dp li gradient tasma.
function TabBarBackground() {
  return (
    <View pointerEvents="none" style={styles.shadowWrap}>
      <Svg width="100%" height={SHADOW_H}>
        <Defs>
          <LinearGradient id="tabShadow" x1="0" y1="1" x2="0" y2="0">
            <Stop offset="0" stopColor={colors.tabShadow} />
            <Stop offset="1" stopColor={colors.tabShadow} stopOpacity={0} />
          </LinearGradient>
        </Defs>
        <Rect x="0" y="0" width="100%" height={SHADOW_H} fill="url(#tabShadow)" />
      </Svg>
    </View>
  );
}

const icon =
  (Cmp: (p: IconProps) => JSX.Element) =>
  ({ focused }: { focused: boolean }) => (
    <View style={[styles.capsule, focused && styles.capsuleActive]}>
      <Cmp size={22} color={focused ? colors.brand : colors.inkFaint} />
    </View>
  );

export function MainTabs() {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarHideOnKeyboard: true,
        tabBarActiveTintColor: colors.brand,
        tabBarInactiveTintColor: colors.inkFaint,
        tabBarLabelStyle: typography.tab,
        tabBarBackground: () => <TabBarBackground />,
        tabBarStyle: {
          height: hit.tabBar,
          paddingTop: 6,
          paddingBottom: 6,
          backgroundColor: colors.card,
          borderTopWidth: 0,
          elevation: 8,
        },
      }}
    >
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{ tabBarLabel: 'Bosh', tabBarIcon: icon(HomeIcon) }}
      />
      <Tab.Screen
        name="Attendance"
        component={AttendanceScreen}
        options={{ tabBarLabel: 'Davomat', tabBarIcon: icon(PinIcon) }}
      />
      <Tab.Screen
        name="Notifications"
        component={NotificationsScreen}
        options={{ tabBarLabel: 'Xabarlar', tabBarIcon: icon(BellIcon) }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{ tabBarLabel: 'Profil', tabBarIcon: icon(UserIcon) }}
      />
    </Tab.Navigator>
  );
}

const styles = StyleSheet.create({
  shadowWrap: { position: 'absolute', left: 0, right: 0, top: -SHADOW_H, height: SHADOW_H },
  capsule: {
    width: 48,
    height: 28,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: 'transparent',
  },
  capsuleActive: { backgroundColor: colors.brandSoft },
});
