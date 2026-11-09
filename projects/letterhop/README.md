# LetterHop

The word ladder game developed by Saverio Morelli: hop from one word to another by changing one letter at a time (e.g. COLD → CORD → CARD → WARD → WARM).
Available as a web app and as an Android app for the Google Play Store.

## Rules and Scoring

- **Three levels**, defined by the minimum number of word transitions between start and finish: Base 4, Medium 6, Hard 8.
- **The minimum is exact**: measured across the full dictionary, so no shorter path exists. At least one minimum solution uses only common everyday words.
- **Free choice of words**: players may use any valid dictionary word, but cannot reuse the same word twice within a game.
- **Points**: 100 / 200 / 300 per level, −10 for each word beyond the minimum, −15 per hint requested, minimum 10. A hint can only be requested if at least 10 points remain afterwards. Undoing moves is free. Using "Show Solution" awards 0 points.
- **Stars**: 3 stars for minimum path without hints, 2 stars with up to two extra words, otherwise 1 star.
- **Length**: 5 letters (default) or 6 letters.

## Features

- **Multilingual**: interface and word lists in Italian, English, Spanish, French, German, with QWERTY, AZERTY, or QWERTZ keyboards.
- **Two-stage hints**: first highlights which letter position to change, then reveals the word.
- **Automatic game saving**, timer with pause, light and dark themes.
- **100% offline** after initial load (`sw.js` caches all five language dictionaries). No third-party network requests: fonts are bundled locally in `fonts/`.
- **No account, no advertisements**.

## Dictionaries

`words/<language>.json` contains:

- `words`: words grouped by length, common words listed first followed by the rest, ordered by frequency. Hints always pick the most familiar word.
- `puzzles`: grouped by length and minimum step count, up to 600 start-finish pairs.

Diacritics and accents are stripped (e.g. CITTÀ → CITTA). Words containing characters such as ñ, ä, ö, ü, ß, œ are excluded so the keyboard remains pure A–Z.

To regenerate dictionaries, use the sources in `words/CREDITS.md` and a Python environment with `wordfreq` and `spylls`:

1. `tools/check_words.py <language> <workspace_dir>` filters words accepted by Hunspell.
2. `tools/build_words.py <workspace_dir> <project_dir>` strips excluded words, constructs the word graph, and selects puzzle pairs.

`tools/filters.py` holds hand-curated word filters: `DROP` (excluded from game), `LOAN` (valid as intermediate steps but never as start, goal, or solution), `SAFE` (regular words mistakenly flagged by profanity filters). `tools/make_icons.py` renders game icons and graphics. The `tools/` folder is blocked from direct browser access.

## Android App (Google Play)

The `android/` directory is an Android Studio project (Java) with a full-screen native WebView shell:

- Portrait orientation, edge-to-edge layout with system bars matching the light/dark theme;
- Back navigation button: closes active in-game dialogs first (`window.onAndroidBack` in `game.js`);
- Offline error screen;
- Adaptive launcher icon with transparent foreground and Android 13+ themed icon;
- Complete assets ready for Google Play Store (`play-store-icon-512.png`, `store/en-GB/`, `store/listing.md`).

### Building and Publishing

1. Open the `projects/letterhop/android` folder in Android Studio (File → Open).
2. Wait for Gradle sync to complete.
3. Copy `keystore.properties.sample` to `keystore.properties` (ignored by git) and enter your Google Play signing key details.
4. Generate the bundle: **Build > Generate Signed App Bundle / APK > Android App Bundle**.
5. Upload the `app-release.aab` file to Google Play Console.
6. Populate the store listing using `android/store/listing.md` and upload the 512px icon and feature graphic.
