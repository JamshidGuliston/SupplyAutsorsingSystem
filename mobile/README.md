# Chef Mobile App (Android)

React Native 0.74 / TypeScript 5 / Node 20+.

## Quick start

1. Install Node 20 LTS, JDK 17, Android Studio with SDK Platform 34.
2. From repo root: `cd mobile && npm install`.
3. Start Metro: `npx react-native start`.
4. In another terminal: `npx react-native run-android` (with a device or emulator running).

## Environments

`react-native-config` reads from `.env` (default), `.env.staging`, or `.env.production`. Build flavor selection happens via Gradle:

- Debug: `cd android && ./gradlew assembleDebug` — uses `.env`
- Staging: `./gradlew assembleStagingRelease` — uses `.env.staging`
- Production: `./gradlew assembleProductionRelease` — uses `.env.production`

## Backend API

Talks to a Laravel backend exposing `/api/v1/...` (Plan 1 of the parent project). Set `API_BASE_URL` in the appropriate env file.

## Firebase

Place `google-services.json` (downloaded from Firebase console) at `android/app/google-services.json`. This file is gitignored.

## Tests

`npx jest` runs unit tests. E2E tests are not yet wired (planned for a future iteration).

## Folder layout

- `src/api/` — axios client, error mapping
- `src/auth/` — keychain, login, session
- `src/attendance/` — GPS, camera, queue, flusher, screens-data
- `src/components/` — reusable UI primitives
- `src/navigation/` — RootNavigator, AuthStack, MainTabs
- `src/screens/` — top-level screens
- `src/push/` — FCM
- `src/theme/`, `src/lib/` — colors, helpers
- `android/app/src/main/java/.../mockgps/` — native mock-GPS detection module
