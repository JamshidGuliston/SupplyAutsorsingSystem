import React from 'react';
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

const icon =
  (Cmp: (p: IconProps) => JSX.Element) =>
  ({ focused }: { focused: boolean }) =>
    <Cmp size={22} color={focused ? colors.brand : colors.inkFaint} />;

export function MainTabs() {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: colors.ink,
        tabBarInactiveTintColor: colors.inkFaint,
        tabBarLabelStyle: typography.tab,
        tabBarStyle: {
          height: hit.tabBar,
          paddingTop: 6,
          paddingBottom: 6,
          backgroundColor: colors.surface,
          borderTopWidth: 1,
          borderTopColor: colors.rule,
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
