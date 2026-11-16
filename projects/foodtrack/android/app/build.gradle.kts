import java.util.Properties

plugins {
    id("com.android.application")
}

// Notifications right away (Firebase Cloud Messaging): set up only when app/google-services.json is there, downloaded
// from the Firebase console. Without it the app builds the same and keeps the 15-minute check.
if (file("google-services.json").exists()) apply(plugin = "com.google.gms.google-services")

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
        versionCode = 7
        versionName = "1.3.0"
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
    // "Continue with Google" through the system account picker (Credential Manager):
    // Google sign-in does not run inside a WebView, so the app gets the ID token natively
    implementation("androidx.credentials:credentials:1.5.0")
    implementation("androidx.credentials:credentials-play-services-auth:1.5.0")
    implementation("com.google.android.libraries.identity.googleid:googleid:1.1.1")

    // Household activity right away with the app closed (a WebView cannot receive Web Push)
    implementation(platform("com.google.firebase:firebase-bom:33.7.0"))
    implementation("com.google.firebase:firebase-messaging")
}
