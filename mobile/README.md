# Chef Mobile App (Android)

React Native 0.74 / TypeScript 5 / Node 20+.

## ⚠️ Before your first APK build — REQUIRED

Three configuration steps are not committed to git and **must be done manually** by the deployer. Skipping any of them causes the build to fail or the running app to be unable to talk to the backend.

### 1. Drop `google-services.json` from Firebase

The release build applies the `com.google.gms.google-services` Gradle plugin. Without the file the build aborts with `File google-services.json is missing`.

1. Go to https://console.firebase.google.com → your project → Android app `uz.kindergarden.chefmobile`.
2. Download `google-services.json`.
3. Place it at `mobile/android/app/google-services.json`. (It is gitignored — do NOT commit.)

### 2. Edit `mobile/.env` for the target backend

The default `mobile/.env` points at `http://10.0.2.2:8000/api/v1`, which is the **Android emulator's** loopback to the dev host. **It will not work on a real phone.** Choose one:

| Target | What to set |
|---|---|
| Real phone hitting your dev laptop on the same Wi-Fi | `API_BASE_URL=http://192.168.X.Y:8000/api/v1` (your laptop's LAN IP) |
| Real phone hitting staging | `API_BASE_URL=https://staging.your-domain.uz/api/v1` (use HTTPS — preferred) |
| Production | Use `.env.production` and `assembleProductionRelease` flavor |

### 3. If using cleartext HTTP for dev — edit network security config

`mobile/android/app/src/main/res/xml/network_security_config.xml` allows cleartext only for specific hosts: `10.0.2.2`, `192.168.1.0`, `localhost`. Android does **suffix matching**, not CIDR — so `192.168.1.0` matches the literal hostname only, NOT the whole 192.168.1.0/24 subnet.

If your dev laptop is at e.g. `192.168.0.42`, add a `<domain>` entry:

```xml
<domain includeSubdomains="true">192.168.0.42</domain>
```

For HTTPS staging/production, no change is needed.

---

## Quick start

1. Install Node 20 LTS, JDK 17, Android Studio with SDK Platform 34.
2. From repo root: `cd mobile && npm install`.
3. Complete the three configuration steps above.
4. Start Metro: `npx react-native start`.
5. In another terminal: `npx react-native run-android` (with a device or emulator running).

## Environments

`react-native-config` reads from `.env` (default), `.env.staging`, or `.env.production`. Build flavor selection happens via Gradle:

- Debug: `cd android && ./gradlew assembleDebug` — uses `.env`
- Staging: `./gradlew assembleStagingRelease` — uses `.env.staging`
- Production: `./gradlew assembleProductionRelease` — uses `.env.production`

## Backend API

Talks to a Laravel backend exposing `/api/v1/...` (Plan 1 of the parent project).

## Tests

`npx jest` runs unit tests. Currently 13 unit tests across 5 suites pass: tashkent helpers, tokenStore, error mapping, authStore, attendanceQueue. E2E tests are not yet wired (planned for a future iteration).

## Folder layout

- `src/api/` — axios client, error mapping
- `src/auth/` — keychain, login, session
- `src/attendance/` — GPS, camera, queue, flusher, screens-data
- `src/components/` — reusable UI primitives (ScreenContainer, PrimaryButton, ErrorBanner, OfflineIndicator)
- `src/navigation/` — RootNavigator, AuthStack, MainTabs
- `src/screens/` — top-level screens (Login, Home, Attendance, Notifications, Profile)
- `src/push/` — FCM token registration
- `src/theme/`, `src/lib/` — colors, helpers
- `android/app/src/main/java/.../mockgps/` — native Kotlin mock-GPS detection module

## Known limitations (deferred to later plans)

- `mockGps` native module reads `Settings.Secure.ALLOW_MOCK_LOCATION` which is deprecated on Android 6+ and usually returns null. The reliable mock check on modern Android is the `pos.mocked` field from the geolocation library, which we already check in `locationService.ts`.
- Failed queue items (validation errors from server) are dropped silently. A dead-letter UI surface is on the roadmap.
- Other chef screens (send-numbers, minus-products, orders, certificates, history) — **Plan 4**.
- Background geofence-exit tracking and incoming push notification handling — **Plan 5**.
- iOS build, App Store, TestFlight — **Plan 6**.

## Manual smoke checklist (post-APK install)

Pre-conditions: Plan 1 backend deployed; pilot kindergarten has lat/lng configured; chef user (role_id=6) attached to that kindergarten with known credentials.

1. Install APK (`adb install app-debug.apk`).
2. Open app → Login screen.
3. Login with chef credentials → tabs visible, lands on Home placeholder.
4. Tap "Davomat" → "Hali kelmadingiz" + green "📷 Keldim".
5. Stand inside geofence. Tap "Keldim" → permissions prompts (location + camera) → grant both → front camera → take selfie → status updates within ~3 sec.
6. Tap "Kelishni qayta yuborish" → same flow → server reflects increment.
7. Walk outside geofence. Tap "Ketdim" → 422 error banner with distance.
8. Walk back inside. Tap "Ketdim" → success.
9. Force-quit app. Reopen → today's row still shown.
10. Airplane mode ON → tap "Kelishni qayta yuborish" → no immediate confirmation, OfflineIndicator shows pending → airplane mode OFF → within ~30 sec the indicator clears.
11. Profile → Chiqish → confirms → returns to Login.
