# Sudoku

The classic 9×9 logic puzzle developed by Saverio Morelli.
Available as a web app and as an Android app for the Google Play Store.

## Features

- **4 difficulty levels**: Easy, Medium, Hard, Expert.
- **Pencil notes**: automatic or manual candidates for each cell.
- **Explanatory hints**: step-by-step logical hints that explain *why* a particular number is correct.
- **Conflict highlighting**: immediate check for errors on rows, columns, and 3×3 blocks.
- **Light and Dark theme**: adaptive style matching system preferences.
- **Statistics and timer**: track your personal best records.
- **100% offline**: no internet connection required to play.
- **Multilingual**: Italian, English, Spanish, French, German.
- **No account, no advertisements**: completely free.

## Android App (Google Play)

The `android/` directory is an Android Studio project (Java) with a full-screen native WebView shell:

- Portrait orientation optimised for interaction with the 9×9 grid;
- Edge-to-edge system with status bar colours matching the light/dark theme;
- Back navigation handling with `OnBackInvokedDispatcher`;
- Offline error screen;
- Complete assets ready for Google Play Store (`play-store-icon-512.png`, `store/en-GB/` with feature graphic and
  1080×1920 screenshots, `store/listing.md`).

### Building and Publishing

1. Open the `projects/sudoku/android` folder in Android Studio (File → Open).
2. Wait for Gradle sync to complete.
3. Copy `keystore.properties.sample` to `keystore.properties` (ignored by git) and enter your Google Play signing key details.
4. Generate the bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Upload the `app-release.aab` file to Google Play Console.
6. Populate the store listing using `android/store/listing.md` and upload the 512px icon and feature graphic.
