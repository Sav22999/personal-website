# Chess

Online and offline chess game developed by Saverio Morelli.
Available as a web app and as an Android app for the Google Play Store.

## Features

- **Online rooms with code**: challenge friends by sharing a 6-character room code or direct invite link.
- **Random matchmaking**: quickly find online opponents.
- **Two players on the same device**: pass & play mode on smartphone or tablet.
- **Against the computer**: AI engine with multiple difficulty levels.
- **Help and rules**: legal move highlighting, piece guides, and hints.
- **Customisation**: board themes and piece sets, adjustable game clock, real-time chat.
- **Auto, Light, or Dark theme**: tabs on desktop, a toggle button on phones (`theme.js`).
- **Multilingual**: Italian, English, Spanish, French, German.
- **No account, no advertisements**: 100% free.

## Android App (Google Play)

The `android/` directory is an Android Studio project (Java) with a full-screen native WebView shell:

- Edge-to-edge layout adapting to system bars;
- Automatic Deep Links / App Links for instant opening of room codes
  (`https://www.saveriomorelli.com/projects/chess/?code=ABC123`);
- Back navigation handling with `OnBackInvokedDispatcher`;
- Offline error screen with custom retry button;
- Complete assets ready for the Google Play Store (`play-store-icon-512.png`, `store/en-GB/` with feature graphic and
  1080×1920 screenshots, `store/listing.md`).

### Building and Publishing

1. Open the `projects/chess/android` folder in Android Studio (File → Open).
2. Wait for Gradle sync to complete.
3. Copy `keystore.properties.sample` to `keystore.properties` (ignored by git) and enter your Google Play signing key details.
4. Generate the Android App Bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Upload the generated `.aab` (in `app/release/app-release.aab`) to Google Play Console.
6. Copy the store listing copy from `android/store/listing.md` and use the provided icon and feature graphic.
