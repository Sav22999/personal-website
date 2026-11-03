import java.util.Properties

plugins {
    id("com.android.application")
}

// Signing data for the release build: keystore.properties (git-ignored, see keystore.properties.sample)
val keystoreProps = Properties().apply {
    val f = rootProject.file("keystore.properties")
    if (f.exists()) f.inputStream().use { load(it) }
}

android {
    namespace = "com.saverio.foodtrack"
    compileSdk = 36

    defaultConfig {
        // The applicationId is the identity of the app on Google Play: it can never change after the first upload
        applicationId = "com.saverio.foodtrack"
        minSdk = 24
        targetSdk = 36
        versionCode = 1
        versionName = "1.0.0"
    }

    signingConfigs {
        if (keystoreProps.getProperty("storeFile") != null) {
            create("release") {
                storeFile = file(keystoreProps.getProperty("storeFile"))
                storePassword = keystoreProps.getProperty("storePassword")
                keyAlias = keystoreProps.getProperty("keyAlias")
                keyPassword = keystoreProps.getProperty("keyPassword")
            }
        }
    }

    buildTypes {
        release {
            isMinifyEnabled = false
            signingConfigs.findByName("release")?.let { signingConfig = it }
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
}

dependencies {
    // Trusted Web Activity: opens the FoodTrack web app full screen, using Chrome (or a WebView as a fallback)
    implementation("com.google.androidbrowserhelper:androidbrowserhelper:2.5.0")
}
