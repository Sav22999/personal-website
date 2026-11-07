package com.saverio.foodtrack;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.res.Configuration;
import android.graphics.Insets;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.CancellationSignal;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowInsets;
import android.view.WindowInsetsController;
import android.view.WindowManager;
import android.webkit.CookieManager;
import android.webkit.JavascriptInterface;
import android.webkit.PermissionRequest;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.FrameLayout;

import androidx.credentials.Credential;
import androidx.credentials.CredentialManager;
import androidx.credentials.CredentialManagerCallback;
import androidx.credentials.CustomCredential;
import androidx.credentials.GetCredentialRequest;
import androidx.credentials.GetCredentialResponse;
import androidx.credentials.exceptions.GetCredentialCancellationException;
import androidx.credentials.exceptions.GetCredentialException;

import com.google.android.libraries.identity.googleid.GetSignInWithGoogleOption;
import com.google.android.libraries.identity.googleid.GoogleIdTokenCredential;

import org.json.JSONObject;

import java.util.concurrent.Executor;

/**
 * FoodTrack for Android: the web app in a full-screen WebView, the same on every phone
 * (it does not depend on which browser is installed).
 *
 * Native parts, exposed to the page as window.FoodTrackAndroid (only on the FoodTrack site):
 *  - "Continue with Google" with the system account picker (Credential Manager): Google sign-in
 *    does not work inside a WebView, so the app gets the ID token and hands it to the page;
 *  - notifications for expiring products, and the background check of household activity (ActivityJobService);
 *  - camera permission for the barcode scanner, file picker for image uploads (admin portal).
 * Links outside the app (contact form, Open Food Facts, …) open in the browser.
 */
public class MainActivity extends Activity {

    private static final int REQ_CAMERA = 1;
    private static final int REQ_NOTIFICATIONS = 2;
    private static final int REQ_FILE = 3;
    private static final String CHANNEL_EXPIRY = "expiry";
    private static final String PATH_PREFIX = "/projects/foodtrack/";

    private WebView web;
    private String host;
    private String launchUrl;
    private volatile boolean onAppPage = false;   // the bridge answers only on FoodTrack pages
    private PermissionRequest pendingCameraRequest;
    private ValueCallback<Uri[]> fileCallback;
    private final Executor uiExecutor = this::runOnUiThread;

    @SuppressLint({"SetJavaScriptEnabled", "AddJavascriptInterface"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        host = getString(R.string.host_name);
        launchUrl = getString(R.string.launch_url);

        FrameLayout root = new FrameLayout(this);
        root.setBackgroundColor(getColor(R.color.background));
        web = new WebView(this);
        web.setBackgroundColor(getColor(R.color.background));
        root.addView(web, new FrameLayout.LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.MATCH_PARENT));
        setContentView(root);
        setupSystemBars(root);

        WebSettings s = web.getSettings();
        s.setJavaScriptEnabled(true);
        s.setDomStorageEnabled(true);
        s.setDatabaseEnabled(true);
        s.setMediaPlaybackRequiresUserGesture(false);
        s.setAllowFileAccess(false);
        s.setAllowContentAccess(false);
        s.setUserAgentString(s.getUserAgentString() + " FoodTrackApp/Android");
        CookieManager.getInstance().setAcceptCookie(true);
        CookieManager.getInstance().setAcceptThirdPartyCookies(web, false);

        web.addJavascriptInterface(new Bridge(), "FoodTrackAndroid");
        web.setWebViewClient(new AppClient());
        web.setWebChromeClient(new AppChrome());

        setupBack();
        if (savedInstanceState != null && web.restoreState(savedInstanceState) != null) return;
        web.loadUrl(urlFrom(getIntent()));
    }

    /** Edge-to-edge on Android 11+: the page is padded by the system bars and the keyboard. */
    private void setupSystemBars(View root) {
        boolean night = (getResources().getConfiguration().uiMode & Configuration.UI_MODE_NIGHT_MASK) == Configuration.UI_MODE_NIGHT_YES;
        if (Build.VERSION.SDK_INT >= 30) {
            getWindow().setDecorFitsSystemWindows(false);
            WindowInsetsController c = getWindow().getInsetsController();
            if (c != null) {
                int light = WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS | WindowInsetsController.APPEARANCE_LIGHT_NAVIGATION_BARS;
                c.setSystemBarsAppearance(night ? 0 : light, light);
            }
            root.setOnApplyWindowInsetsListener((v, insets) -> {
                Insets i = insets.getInsets(WindowInsets.Type.systemBars() | WindowInsets.Type.ime() | WindowInsets.Type.displayCutout());
                v.setPadding(i.left, i.top, i.right, i.bottom);
                return WindowInsets.CONSUMED;
            });
        } else {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE);
        }
    }

    /** Back goes back in the web app (sheets, screens); at the start it leaves the app. */
    private void setupBack() {
        if (Build.VERSION.SDK_INT >= 33) {
            getOnBackInvokedDispatcher().registerOnBackInvokedCallback(android.window.OnBackInvokedDispatcher.PRIORITY_DEFAULT, this::goBack);
        }
    }

    private void goBack() {
        if (web.canGoBack()) web.goBack();
        else moveTaskToBack(true);
    }

    @SuppressWarnings("deprecation")
    @Override
    public void onBackPressed() {
        goBack(); // Android 12 and older (13+ use the callback above)
    }

    private boolean isAppUrl(Uri u) {
        return u != null && "https".equals(u.getScheme()) && host.equalsIgnoreCase(u.getHost())
                && u.getPath() != null && u.getPath().startsWith(PATH_PREFIX);
    }

    private String urlFrom(Intent intent) {
        Uri data = intent != null ? intent.getData() : null;
        return isAppUrl(data) ? data.toString() : launchUrl;
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        if (intent != null && intent.getData() != null) web.loadUrl(urlFrom(intent)); // e.g. a household invitation link
    }

    private void openExternal(Uri u) {
        try {
            startActivity(new Intent(Intent.ACTION_VIEW, u).addCategory(Intent.CATEGORY_BROWSABLE));
        } catch (ActivityNotFoundException e) { /* no app can open it */ }
    }

    /** Calls window.FoodTrackNative.result(kind, value, error) in the page. */
    private void sendResult(String kind, String value, String error) {
        String js = "window.FoodTrackNative&&FoodTrackNative.result(" + JSONObject.quote(kind) + ","
                + (value == null ? "null" : JSONObject.quote(value)) + ","
                + (error == null ? "null" : JSONObject.quote(error)) + ")";
        runOnUiThread(() -> web.evaluateJavascript(js, null));
    }

    // ------------------------------------------------------------------ WebView clients

    private class AppClient extends WebViewClient {
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            Uri u = request.getUrl();
            if (isAppUrl(u)) return false;
            openExternal(u);
            return true;
        }

        @Override
        public void onPageStarted(WebView view, String url, android.graphics.Bitmap favicon) {
            onAppPage = isAppUrl(Uri.parse(url));
        }

        @Override
        public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
            if (!request.isForMainFrame()) return;
            // Offline and not cached yet: a simple page with "Try again"
            String retry = JSONObject.quote(request.getUrl().toString());
            String html = "<!doctype html><meta name=viewport content='width=device-width,initial-scale=1'>"
                    + "<body style=\"font-family:sans-serif;display:flex;flex-direction:column;align-items:center;justify-content:center;height:90vh;text-align:center;padding:24px;background:transparent;color:#5C6459\">"
                    + "<h2 style='color:#1A1C19;margin:0 0 8px'>" + getString(R.string.offline_title) + "</h2><p>" + getString(R.string.offline_text) + "</p>"
                    + "<button onclick='location.href=" + retry + "' style='margin-top:16px;padding:12px 26px;border:0;border-radius:999px;background:#3D8B40;color:#fff;font-size:16px'>"
                    + getString(R.string.offline_retry) + "</button></body>";
            view.loadDataWithBaseURL(launchUrl, html, "text/html", "UTF-8", request.getUrl().toString());
        }
    }

    private class AppChrome extends WebChromeClient {
        @Override
        public void onPermissionRequest(PermissionRequest request) {
            runOnUiThread(() -> {
                boolean wantsCamera = false;
                for (String r : request.getResources()) if (PermissionRequest.RESOURCE_VIDEO_CAPTURE.equals(r)) wantsCamera = true;
                Uri origin = request.getOrigin();
                if (!wantsCamera || origin == null || !"https".equals(origin.getScheme()) || !host.equalsIgnoreCase(origin.getHost())) { request.deny(); return; }
                if (checkSelfPermission(Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED) {
                    request.grant(new String[]{PermissionRequest.RESOURCE_VIDEO_CAPTURE});
                } else {
                    if (pendingCameraRequest != null) pendingCameraRequest.deny();
                    pendingCameraRequest = request;
                    requestPermissions(new String[]{Manifest.permission.CAMERA}, REQ_CAMERA);
                }
            });
        }

        @Override
        public boolean onShowFileChooser(WebView view, ValueCallback<Uri[]> callback, FileChooserParams params) {
            if (fileCallback != null) fileCallback.onReceiveValue(null);
            fileCallback = callback;
            try {
                startActivityForResult(params.createIntent(), REQ_FILE);
                return true;
            } catch (ActivityNotFoundException e) {
                fileCallback = null;
                return false;
            }
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == REQ_FILE && fileCallback != null) {
            fileCallback.onReceiveValue(WebChromeClient.FileChooserParams.parseResult(resultCode, data));
            fileCallback = null;
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        boolean granted = grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED;
        if (requestCode == REQ_CAMERA && pendingCameraRequest != null) {
            if (granted) pendingCameraRequest.grant(new String[]{PermissionRequest.RESOURCE_VIDEO_CAPTURE});
            else pendingCameraRequest.deny();
            pendingCameraRequest = null;
        } else if (requestCode == REQ_NOTIFICATIONS) {
            sendResult("notify", notificationState(), null);
        }
    }

    // ------------------------------------------------------------------ notifications

    private String notificationState() {
        if (Build.VERSION.SDK_INT >= 33 && checkSelfPermission(Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
            return "default"; // not asked yet (or refused): the page can ask again
        }
        NotificationManager nm = getSystemService(NotificationManager.class);
        return nm != null && nm.areNotificationsEnabled() ? "granted" : "denied";
    }

    private void showNotification(String title, String body) {
        if (!"granted".equals(notificationState())) return;
        NotificationManager nm = getSystemService(NotificationManager.class);
        if (nm == null) return;
        Notification.Builder b;
        if (Build.VERSION.SDK_INT >= 26) {
            NotificationChannel ch = new NotificationChannel(CHANNEL_EXPIRY, getString(R.string.channel_expiry), NotificationManager.IMPORTANCE_DEFAULT);
            nm.createNotificationChannel(ch);
            b = new Notification.Builder(this, CHANNEL_EXPIRY);
        } else {
            //noinspection deprecation
            b = new Notification.Builder(this);
        }
        Intent open = new Intent(this, MainActivity.class).setFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP | Intent.FLAG_ACTIVITY_CLEAR_TOP);
        PendingIntent pi = PendingIntent.getActivity(this, 0, open, PendingIntent.FLAG_IMMUTABLE | PendingIntent.FLAG_UPDATE_CURRENT);
        b.setSmallIcon(R.drawable.ic_notification_icon)
                .setColor(getColor(R.color.primary))
                .setContentTitle(title)
                .setContentText(body)
                .setStyle(new Notification.BigTextStyle().bigText(body))
                .setContentIntent(pi)
                .setAutoCancel(true);
        nm.notify(1, b.build());
    }

    // ------------------------------------------------------------------ Google sign-in

    private void googleSignIn(String webClientId) {
        if (webClientId == null || webClientId.isEmpty()) { sendResult("google", null, "not configured"); return; }
        // The ID token is issued for the web client: the server verifies it exactly as on the website
        GetSignInWithGoogleOption option = new GetSignInWithGoogleOption.Builder(webClientId).build();
        GetCredentialRequest request = new GetCredentialRequest.Builder().addCredentialOption(option).build();
        CredentialManager.create(this).getCredentialAsync(this, request, new CancellationSignal(), uiExecutor,
                new CredentialManagerCallback<GetCredentialResponse, GetCredentialException>() {
                    @Override
                    public void onResult(GetCredentialResponse response) {
                        Credential c = response.getCredential();
                        if (c instanceof CustomCredential && GoogleIdTokenCredential.TYPE_GOOGLE_ID_TOKEN_CREDENTIAL.equals(c.getType())) {
                            try {
                                sendResult("google", GoogleIdTokenCredential.createFrom(c.getData()).getIdToken(), null);
                            } catch (Exception e) {
                                sendResult("google", null, "invalid credential");
                            }
                        } else {
                            sendResult("google", null, "unexpected credential");
                        }
                    }

                    @Override
                    public void onError(GetCredentialException e) {
                        if (e instanceof GetCredentialCancellationException) sendResult("google", null, "cancelled");
                        else sendResult("google", null, e.getClass().getSimpleName());
                    }
                });
    }

    // ------------------------------------------------------------------ bridge for the page

    /** window.FoodTrackAndroid (methods run on a background thread). */
    private class Bridge {
        @JavascriptInterface
        public void googleSignIn(String webClientId) {
            if (!onAppPage) return;
            runOnUiThread(() -> MainActivity.this.googleSignIn(webClientId));
        }

        @JavascriptInterface
        public String notificationPermission() {
            return onAppPage ? notificationState() : "denied";
        }

        @JavascriptInterface
        public void requestNotificationPermission() {
            if (!onAppPage) return;
            runOnUiThread(() -> {
                if (Build.VERSION.SDK_INT >= 33 && checkSelfPermission(Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                    requestPermissions(new String[]{Manifest.permission.POST_NOTIFICATIONS}, REQ_NOTIFICATIONS);
                } else {
                    sendResult("notify", notificationState(), null);
                }
            });
        }

        @JavascriptInterface
        public void setBackgroundActivity(boolean on, String after, String lang) {
            if (!onAppPage) return;
            ActivityJobService.configure(MainActivity.this, on, after, lang);
        }

        @JavascriptInterface
        public void notify(String title, String body) {
            if (!onAppPage) return;
            runOnUiThread(() -> showNotification(title, body));
        }
    }

    // ------------------------------------------------------------------ lifecycle

    @Override
    protected void onSaveInstanceState(Bundle outState) {
        super.onSaveInstanceState(outState);
        web.saveState(outState);
    }

    @Override
    protected void onPause() {
        super.onPause();
        CookieManager.getInstance().flush();
        web.onPause();
    }

    @Override
    protected void onResume() {
        super.onResume();
        web.onResume();
    }

    @Override
    protected void onDestroy() {
        if (pendingCameraRequest != null) pendingCameraRequest.deny();
        web.destroy();
        super.onDestroy();
    }
}
