package com.saverio.fucina;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.ActivityNotFoundException;
import android.content.Intent;
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
 * Fucina for Android: the web app in a full-screen WebView, the same on every phone
 * (it does not depend on which browser is installed).
 *
 * Native parts, exposed to the page as window.FucinaAndroid (only on the Fucina pages):
 *  - "Continue with Google" with the system account picker (Credential Manager): Google sign-in
 *    does not work inside a WebView, so the app gets the ID token and hands it to the page;
 * Links outside the app (product pages, contact form…) open in the browser.
 */
public class MainActivity extends Activity {

    private static final int REQ_FILE = 3;
    private static final String PATH_PREFIX = "/projects/fucina/";

    private WebView web;
    private String host;
    private String launchUrl;
    private volatile boolean onAppPage = false;   // the bridge answers only on Fucina pages
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
        s.setUserAgentString(s.getUserAgentString() + " FucinaApp/Android");
        CookieManager.getInstance().setAcceptCookie(true);
        CookieManager.getInstance().setAcceptThirdPartyCookies(web, false);

        web.addJavascriptInterface(new Bridge(), "FucinaAndroid");
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

    /** Start URL: a link to the app, or the home page. */
    private String urlFrom(Intent intent) {
        Uri data = intent != null ? intent.getData() : null;
        return isAppUrl(data) ? data.toString() : launchUrl;
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        if (intent == null) return;
        if (intent.getData() != null) web.loadUrl(urlFrom(intent)); // link to a certificate
    }

    private void openExternal(Uri u) {
        try {
            startActivity(new Intent(Intent.ACTION_VIEW, u).addCategory(Intent.CATEGORY_BROWSABLE));
        } catch (ActivityNotFoundException e) { /* no app can open it */ }
    }

    /** Calls window.FucinaNative.result(kind, value, error) in the page. */
    private void sendResult(String kind, String value, String error) {
        String js = "window.FucinaNative&&FucinaNative.result(" + JSONObject.quote(kind) + ","
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
                    + "<button onclick='location.href=" + retry + "' style='margin-top:16px;padding:12px 26px;border:0;border-radius:999px;background:#B4235A;color:#fff;font-size:16px'>"
                    + getString(R.string.offline_retry) + "</button></body>";
            view.loadDataWithBaseURL(launchUrl, html, "text/html", "UTF-8", request.getUrl().toString());
        }
    }

    private class AppChrome extends WebChromeClient {
        @Override
        public void onPermissionRequest(PermissionRequest request) {
            runOnUiThread(request::deny); // Fucina needs no camera or microphone
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

    /** window.FucinaAndroid (methods run on a background thread). */
    private class Bridge {
        @JavascriptInterface
        public void googleSignIn(String webClientId) {
            if (!onAppPage) return;
            runOnUiThread(() -> MainActivity.this.googleSignIn(webClientId));
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
        web.destroy();
        super.onDestroy();
    }
}
