package com.saverio.foodtrack;

import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.job.JobInfo;
import android.app.job.JobParameters;
import android.app.job.JobScheduler;
import android.app.job.JobService;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Build;
import android.webkit.CookieManager;

import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

/**
 * Tells the user what the other household members did while the app is closed.
 *
 * A WebView cannot receive Web Push, so the app asks the API about every 15 minutes (the shortest period
 * Android allows; it can be later to save battery). The request uses the session of the WebView.
 */
public class ActivityJobService extends JobService {

    private static final int JOB_ID = 1001;
    private static final int NOTIFICATION_ID = 2;
    private static final String CHANNEL_ACTIVITY = "activity";
    private static final String PREFS = "activity";
    private static final String KEY_AFTER = "after";
    private static final String KEY_LANG = "lang";

    /** Called by the page: starts or stops the check and remembers where the user has got to. */
    static void configure(Context context, boolean on, String after, String lang) {
        JobScheduler scheduler = context.getSystemService(JobScheduler.class);
        if (scheduler == null) return;
        context.getSharedPreferences(PREFS, MODE_PRIVATE).edit().putString(KEY_AFTER, after).putString(KEY_LANG, lang).apply();
        if (!on) {
            scheduler.cancel(JOB_ID);
            return;
        }
        if (scheduler.getPendingJob(JOB_ID) != null) return;
        scheduler.schedule(new JobInfo.Builder(JOB_ID, new ComponentName(context, ActivityJobService.class))
                .setPeriodic(15 * 60 * 1000L)
                .setRequiredNetworkType(JobInfo.NETWORK_TYPE_ANY)
                .setPersisted(true)
                .build());
    }

    @Override
    public boolean onStartJob(JobParameters params) {
        new Thread(() -> {
            boolean retry = false;
            try {
                check();
            } catch (Exception e) {
                retry = true; // No connection or server error: tried again later
            }
            jobFinished(params, retry);
        }).start();
        return true;
    }

    @Override
    public boolean onStopJob(JobParameters params) {
        return true;
    }

    private void check() throws Exception {
        SharedPreferences prefs = getSharedPreferences(PREFS, MODE_PRIVATE);
        String after = prefs.getString(KEY_AFTER, "0");
        String base = getString(R.string.launch_url);
        String cookie = CookieManager.getInstance().getCookie(base);
        if (cookie == null) return;

        HttpURLConnection c = (HttpURLConnection) new URL(base + "api/index.php?r=activity.pending&after=" + Uri.encode(after)).openConnection();
        c.setConnectTimeout(10000);
        c.setReadTimeout(10000);
        c.setRequestProperty("Cookie", cookie);
        c.setRequestProperty("X-Lang", prefs.getString(KEY_LANG, "it"));
        try {
            int status = c.getResponseCode();
            if (status == 401) {
                configure(this, false, after, prefs.getString(KEY_LANG, "it")); // Signed out
                return;
            }
            if (status != 200) throw new IllegalStateException("HTTP " + status);
            JSONObject d = new JSONObject(read(c.getInputStream()));
            long last = d.optLong("lastId", 0);
            if (last > 0) prefs.edit().putString(KEY_AFTER, String.valueOf(last)).apply();
            String text = d.isNull("text") ? "" : d.optString("text", "");
            if (!text.isEmpty()) showNotification(text);
        } finally {
            c.disconnect();
        }
    }

    private static String read(InputStream in) throws Exception {
        ByteArrayOutputStream out = new ByteArrayOutputStream();
        byte[] buffer = new byte[4096];
        for (int n; (n = in.read(buffer)) > 0; ) out.write(buffer, 0, n);
        return out.toString("UTF-8");
    }

    private void showNotification(String body) {
        NotificationManager nm = getSystemService(NotificationManager.class);
        if (nm == null || !nm.areNotificationsEnabled()) return;
        Notification.Builder b;
        if (Build.VERSION.SDK_INT >= 26) {
            nm.createNotificationChannel(new NotificationChannel(CHANNEL_ACTIVITY, getString(R.string.channel_activity), NotificationManager.IMPORTANCE_DEFAULT));
            b = new Notification.Builder(this, CHANNEL_ACTIVITY);
        } else {
            //noinspection deprecation
            b = new Notification.Builder(this);
        }
        Intent open = new Intent(this, MainActivity.class)
                .setData(Uri.parse(getString(R.string.launch_url) + "#/history"))
                .setFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP | Intent.FLAG_ACTIVITY_CLEAR_TOP);
        PendingIntent pi = PendingIntent.getActivity(this, 1, open, PendingIntent.FLAG_IMMUTABLE | PendingIntent.FLAG_UPDATE_CURRENT);
        b.setSmallIcon(R.drawable.ic_notification_icon)
                .setColor(getColor(R.color.primary))
                .setContentTitle(getString(R.string.app_name))
                .setContentText(body)
                .setStyle(new Notification.BigTextStyle().bigText(body))
                .setContentIntent(pi)
                .setAutoCancel(true);
        nm.notify(NOTIFICATION_ID, b.build());
    }
}
