package com.saverio.foodtrack;

import android.app.Activity;
import android.content.Context;

import com.google.firebase.FirebaseApp;
import com.google.firebase.messaging.FirebaseMessaging;

/**
 * Household activity with the app closed, right away, through Firebase Cloud Messaging (a WebView cannot receive
 * Web Push). The page asks for the device token (window.FoodTrackAndroid.pushToken) and registers it on the server
 * as the push subscription "fcm:<token>". A message carries no content: it only wakes PushService, which runs the
 * same check as ActivityJobService. While Firebase works the page turns that 15-minute check off.
 *
 * Without app/google-services.json the Firebase plugin is not applied: available() is false and the app keeps
 * the 15-minute check.
 */
final class Push {

    /** True while the app is on screen: the open app tells the user by itself. */
    static volatile boolean appVisible = false;

    interface Result {
        void send(String kind, String value, String error);
    }

    private Push() {
    }

    static boolean available(Context c) {
        return !FirebaseApp.getApps(c).isEmpty();
    }

    /** Gives the token to the page: result("push", token, error). The page asks for the permission first. */
    static void requestToken(Activity a, String permission, Result result) {
        if (!available(a)) {
            result.send("push", null, "unavailable");
            return;
        }
        if (!"granted".equals(permission)) {
            result.send("push", null, "denied");
            return;
        }
        FirebaseMessaging.getInstance().getToken().addOnCompleteListener(task -> {
            if (task.isSuccessful() && task.getResult() != null) result.send("push", task.getResult(), null);
            else result.send("push", null, "token");
        });
    }
}
