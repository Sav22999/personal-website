package com.saverio.thinkhop;

import android.Manifest;
import android.app.Activity;
import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Build;
import android.webkit.CookieManager;

import com.google.firebase.FirebaseApp;
import com.google.firebase.messaging.FirebaseMessaging;

import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

/**
 * Notifications with the app closed, through Firebase Cloud Messaging (a WebView cannot receive Web Push).
 *
 * The page asks for the device token (window.ThinkHopAndroid.pushToken) and registers it on the server as the push
 * subscription "fcm:<token>". A message carries no content: it only wakes PushService, which asks the API what is
 * new with the session of the WebView, exactly like the service worker does in the browser.
 *
 * Without app/google-services.json the Firebase plugin is not applied and Firebase is not set up: available() is
 * false and the page says that notifications are not supported here.
 */
final class Push {

    static final int REQ_NOTIFICATIONS = 41;
    private static final String CHANNEL = "news";

    /** True while the app is on screen: whoever is using it doesn't need a notification. */
    static volatile boolean appVisible = false;

    interface Result {
        void send(String kind, String value, String error);
    }

    private Push() {
    }

    static boolean available(Context c) {
        return !FirebaseApp.getApps(c).isEmpty();
    }

    /** "granted", "denied", or "default" when Android 13+ has not been asked yet. */
    static String permission(Context c) {
        if (Build.VERSION.SDK_INT >= 33 && c.checkSelfPermission(Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
            return "default";
        }
        NotificationManager nm = c.getSystemService(NotificationManager.class);
        return nm != null && nm.areNotificationsEnabled() ? "granted" : "denied";
    }

    /** Asks for the permission when needed, then gives the token to the page: result("push", token, error). */
    static void requestToken(Activity a, Result result) {
        if (!available(a)) {
            result.send("push", null, "unavailable");
            return;
        }
        if ("default".equals(permission(a))) {
            a.requestPermissions(new String[]{Manifest.permission.POST_NOTIFICATIONS}, REQ_NOTIFICATIONS);
            return;
        }
        sendToken(a, result);
    }

    /** Called by the activity when the user has answered the permission request. */
    static void onPermissionResult(Activity a, Result result) {
        sendToken(a, result);
    }

    private static void sendToken(Context c, Result result) {
        if (!"granted".equals(permission(c))) {
            result.send("push", null, "denied");
            return;
        }
        FirebaseMessaging.getInstance().getToken().addOnCompleteListener(task -> {
            if (task.isSuccessful() && task.getResult() != null) result.send("push", task.getResult(), null);
            else result.send("push", null, "token");
        });
    }

    /** GET of an API path with the session of the WebView. Null when signed out. */
    static JSONObject get(Context c, String path) throws Exception {
        String base = c.getString(R.string.launch_url);
        String cookie = CookieManager.getInstance().getCookie(base);
        if (cookie == null) return null;
        HttpURLConnection conn = (HttpURLConnection) new URL(base + path).openConnection();
        conn.setConnectTimeout(8000);
        conn.setReadTimeout(8000);
        conn.setRequestProperty("Cookie", cookie);
        try {
            int status = conn.getResponseCode();
            if (status == 401) return null;
            if (status != 200) throw new IllegalStateException("HTTP " + status);
            return new JSONObject(read(conn.getInputStream()));
        } finally {
            conn.disconnect();
        }
    }

    private static String read(InputStream in) throws Exception {
        ByteArrayOutputStream out = new ByteArrayOutputStream();
        byte[] buffer = new byte[4096];
        for (int n; (n = in.read(buffer)) > 0; ) out.write(buffer, 0, n);
        return out.toString("UTF-8");
    }

    /** Shows a notification; tapping it opens the app on page ("./#/…", relative to the app). */
    static void show(Context c, String tag, String text, String page) {
        NotificationManager nm = c.getSystemService(NotificationManager.class);
        if (nm == null || !"granted".equals(permission(c)) || text == null || text.isEmpty()) return;
        Notification.Builder b;
        if (Build.VERSION.SDK_INT >= 26) {
            nm.createNotificationChannel(new NotificationChannel(CHANNEL, c.getString(R.string.channel_news), NotificationManager.IMPORTANCE_DEFAULT));
            b = new Notification.Builder(c, CHANNEL);
        } else {
            //noinspection deprecation
            b = new Notification.Builder(c);
        }
        String base = c.getString(R.string.launch_url);
        String url = page == null || page.isEmpty() ? base : base + (page.startsWith("./") ? page.substring(2) : page);
        Intent open = new Intent(c, MainActivity.class)
                .setData(Uri.parse(url))
                .setFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP | Intent.FLAG_ACTIVITY_CLEAR_TOP);
        int id = tag.hashCode();
        PendingIntent pi = PendingIntent.getActivity(c, id, open, PendingIntent.FLAG_IMMUTABLE | PendingIntent.FLAG_UPDATE_CURRENT);
        b.setSmallIcon(R.drawable.ic_notification)
                .setColor(c.getColor(R.color.primary))
                .setContentTitle(c.getString(R.string.app_name))
                .setContentText(text)
                .setStyle(new Notification.BigTextStyle().bigText(text))
                .setContentIntent(pi)
                .setAutoCancel(true);
        nm.notify(tag, 1, b.build());
    }
}
