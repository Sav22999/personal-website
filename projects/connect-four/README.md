# Connect Four (Forza Quattro)

The classic 4-in-a-row alignment game developed by Saverio Morelli.
Available as a web app and as an Android app for the Google Play Store.

## Features

- **Online rooms with code**: play with friends by sharing a 6-character room code or direct invite link.
- **Random matchmaking**: instantly find opponents online.
- **On the same device**: turn-based Pass & Play mode on smartphone or tablet.
- **Against the computer**: AI engine with multiple difficulty levels.
- **Disc customisation**: vibrant colour combinations for discs and the grid.
- **Real-time chat**: send messages during the game.
- **Auto, Light, or Dark theme**: tabs on desktop, a toggle button on phones (`theme.js`).
- **Multilingual**: Italian, English, Spanish, French, German.
- **No registration, no advertisements**: 100% free.
- **Works offline**: the service worker (`sw.js`) launches the game without a network connection for local games on the same device or against the computer.
- **Zero third-party requests**: bundled fonts (Fredoka and Nunito, SIL OFL licence) in `fonts/`.
- **Ephemeral data**: inactive online rooms are purged nightly by `projects/cron/cleanup.php`.

## Android App (Google Play)

The `android/` directory is an Android Studio project (Java) with a full-screen native WebView shell:

- Edge-to-edge layout adapting to system bars;
- Automatic Deep Links / App Links for instant opening of rooms (supports both `/projects/connect-four/` and `/projects/forza-quattro/`);
- Back navigation button: closes active in-game dialogs first (`window.onAndroidBack` in `game.js`);
- Offline error screen;
- Adaptive launcher icon with transparent foreground and Android 13+ themed icon (`ic_launcher_foreground`, `ic_launcher_background`, `ic_launcher_monochrome`);
- Complete assets ready for Google Play Store (`play-store-icon-512.png`, `store/en-GB/` with feature graphic and 1080×1920 screenshots, `store/listing.md`).

### Building and Publishing

1. Open the `projects/connect-four/android` folder in Android Studio (File → Open).
2. Wait for Gradle sync to complete.
3. Copy `keystore.properties.sample` to `keystore.properties` (ignored by git) and enter your Google Play signing key details.
4. Generate the Android App Bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Upload the generated `.aab` (in `app/release/app-release.aab`) to Google Play Console.
6. Copy the store listing copy from `android/store/listing.md` and use the provided icon and feature graphic.
