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

## Native dependency — rebuild required

`react-native-svg` is a native module. After pulling changes that add or
update it, `npm install` alone is not enough — Metro cannot ship native code
over a running app. Rebuild the APK:

```bash
cd mobile && npm install
npx react-native run-android
```

Symptom of skipping this: the app crashes on launch with
`Invariant Violation: requireNativeComponent: "RNSVGSvgView" was not found`.

## Environments

`react-native-config` reads the API base URL from an env file at build time.

**There are no product flavors in this project** — only the `debug` and `release`
build types. Earlier revisions of this README documented
`assembleStagingRelease` / `assembleProductionRelease`; those Gradle tasks do
not exist and never did.

Because there is no flavor whose name matches `staging` or `production`,
`assembleRelease` falls through to the default `.env`. Do not rely on that —
name the file explicitly with the `ENVFILE` variable, so the build cannot
silently pick up whatever `.env` happens to contain:

```bash
cd mobile/android
ENVFILE=.env.production ./gradlew assembleRelease   # production API
ENVFILE=.env.staging    ./gradlew assembleRelease   # staging API
./gradlew assembleDebug                             # uses .env
```

Gradle prints `Reading env from: <file>` early in the build — check that line
matches what you intended before shipping the APK.

Note `.env` is gitignored, so a fresh clone has no `.env` at all and the build
would warn and produce an app with no API URL. `.env.production` and
`.env.staging` are committed.

## Release build

The release build is signed with a real keystore. **It is not in this repo and
must never be.** The keystore lives at `~/.android-keystores/chefmobile-release.jks`
and its credentials in `~/.gradle/gradle.properties`:

```properties
CHEFMOBILE_STORE_FILE=C:/Users/<you>/.android-keystores/chefmobile-release.jks
CHEFMOBILE_KEY_ALIAS=chefmobile
CHEFMOBILE_STORE_PASSWORD=...
CHEFMOBILE_KEY_PASSWORD=...
```

Without those four properties a release build **fails with an explicit error**
rather than falling back to the debug key — a debug-signed release installs
fine, so that mistake would otherwise go unnoticed until the Play Store
rejected it.

```bash
cd mobile/android
ENVFILE=.env.production ./gradlew clean assembleRelease
```

Output: `android/app/build/outputs/apk/release/app-release.apk`

**Back up the keystore.** Lose it and you can never ship an update that
existing installs will accept — the app would have to be uninstalled and
reinstalled on every phone.

**Changing signing key breaks in-place updates.** Builds before v1.1.0 were
signed with the debug key. A phone carrying one of those must uninstall the app
before installing a release-signed APK; Android refuses to update across a
signature change.

Bump `versionCode` in `android/app/build.gradle` for every build you hand out,
otherwise you cannot tell two APKs apart on a device.

For Google Play you would want an app bundle (`./gradlew bundleRelease`,
producing an `.aab`) rather than an APK, plus Play App Signing. Neither is set
up here — the current setup targets APKs distributed by hand.

## Backend API

Talks to a Laravel backend exposing `/api/v1/...` (Plan 1 of the parent project).

## Tests

`npx jest` runs unit tests. Currently 60 tests across 11 suites pass: the five original logic suites plus render tests for AppText, Button, Field, Section/Divider, Banner/StatusLine/PageHeader and the SVG icons. E2E tests are not yet wired (planned for a future iteration).

## Folder layout

- `src/api/` — axios client, error mapping
- `src/auth/` — keychain, login, session
- `src/attendance/` — GPS, camera, queue, flusher, screens-data
- `src/components/` — design-system primitives (AppText, Screen, Section, Field, Button, Banner, PageHeader, …) plus `icons/`
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
