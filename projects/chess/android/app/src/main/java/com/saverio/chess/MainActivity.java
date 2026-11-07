package com.saverio.chess;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.content.res.Configuration;
import android.graphics.Insets;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowInsets;
import android.view.WindowInsetsController;
import android.view.WindowManager;
import android.webkit.CookieManager;
import android.webkit.JavascriptInterface;
import android.webkit.PermissionRequest;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.FrameLayout;

import org.json.JSONObject;

/**
 * Chess for Android: the web app in a full-screen WebView, the same on every phone.
 * Links outside the app open in the browser. Room invite links open in the game.
 */
public class MainActivity extends Activity {

    private static final String PATH_PREFIX = "/projects/chess/";

    private WebView web;
    private View rootView;
    private String host;
    private String launchUrl;
    private boolean retriedLoad = false;

    @SuppressLint("SetJavaScriptEnabled")
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
        s.setUserAgentString(s.getUserAgentString() + " ChessApp/Android");
        CookieManager.getInstance().setAcceptCookie(true);
        CookieManager.getInstance().setAcceptThirdPartyCookies(web, false);

        web.addJavascriptInterface(new ThemeBridge(), "AndroidTheme");
        web.setWebViewClient(new AppClient());
        web.setWebChromeClient(new AppChrome());

        setupBack();
        if (savedInstanceState != null && web.restoreState(savedInstanceState) != null) return;
        web.loadUrl(urlFrom(getIntent()));
    }

    /** Edge-to-edge on Android 11+: the page is padded by the system bars. */
    private void setupSystemBars(View root) {
        rootView = root;
        if (Build.VERSION.SDK_INT >= 30) {
            getWindow().setDecorFitsSystemWindows(false);
            root.setOnApplyWindowInsetsListener((v, insets) -> {
                Insets i = insets.getInsets(WindowInsets.Type.systemBars() | WindowInsets.Type.ime() | WindowInsets.Type.displayCutout());
                v.setPadding(i.left, i.top, i.right, i.bottom);
                return WindowInsets.CONSUMED;
            });
        } else {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_RESIZE);
        }
        // Until the page says which theme it shows, follow the phone
        applyBars((getResources().getConfiguration().uiMode & Configuration.UI_MODE_NIGHT_MASK) == Configuration.UI_MODE_NIGHT_YES);
    }

    /** Colours of the window and of the system bars, for the theme shown by the page. */
    @SuppressWarnings("deprecation")
    private void applyBars(boolean dark) {
        int color = getColor(dark ? R.color.background_dark : R.color.background_light);
        rootView.setBackgroundColor(color);
        web.setBackgroundColor(color);
        getWindow().setStatusBarColor(color);
        getWindow().setNavigationBarColor(color);
        if (Build.VERSION.SDK_INT >= 30) {
            WindowInsetsController c = getWindow().getInsetsController();
            if (c != null) {
                int light = WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS | WindowInsetsController.APPEARANCE_LIGHT_NAVIGATION_BARS;
                c.setSystemBarsAppearance(dark ? 0 : light, light);
            }
        } else {
            View decor = getWindow().getDecorView();
            int light = View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR;
            if (Build.VERSION.SDK_INT >= 26) light |= View.SYSTEM_UI_FLAG_LIGHT_NAVIGATION_BAR;
            int flags = decor.getSystemUiVisibility();
            decor.setSystemUiVisibility(dark ? flags & ~light : flags | light);
        }
    }

    /** Called by theme.js whenever the theme of the page changes. */
    private class ThemeBridge {
        @JavascriptInterface
        public void setDark(boolean dark) {
            runOnUiThread(() -> applyBars(dark));
        }
    }

    /** Back goes back in the web app; at the root it leaves the app. */
    private void setupBack() {
        if (Build.VERSION.SDK_INT >= 33) {
            getOnBackInvokedDispatcher().registerOnBackInvokedCallback(android.window.OnBackInvokedDispatcher.PRIORITY_DEFAULT, this::goBack);
        }
    }

    private void goBack() {
        if (web.canGoBack()) web.goBack();
        else moveTaskToBack(true);
    }

    /** Android 12 and older only: from 13 the callback in setupBack() handles keys and gestures. */
    @SuppressLint("GestureBackNavigation")
    @SuppressWarnings("deprecation")
    @Override
    public void onBackPressed() {
        goBack();
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
        if (intent == null) return;
        if (intent.getData() != null) web.loadUrl(urlFrom(intent));
    }

    private void openExternal(Uri u) {
        try {
            startActivity(new Intent(Intent.ACTION_VIEW, u).addCategory(Intent.CATEGORY_BROWSABLE));
        } catch (ActivityNotFoundException e) { /* ignore */ }
    }

    private class AppClient extends WebViewClient {
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            Uri u = request.getUrl();
            if (isAppUrl(u)) return false;
            openExternal(u);
            return true;
        }

        @Override
        public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
            if (!request.isForMainFrame()) return;
            // After a cold start the first load misses the service worker: once it is up the game opens offline too
            if (!retriedLoad) {
                retriedLoad = true;
                String url = request.getUrl().toString();
                view.postDelayed(() -> view.loadUrl(url), 400);
                return;
            }
            String retry = JSONObject.quote(request.getUrl().toString());
            String html = "<!doctype html><meta name=viewport content='width=device-width,initial-scale=1'>"
                    + "<body style=\"font-family:sans-serif;display:flex;flex-direction:column;align-items:center;justify-content:center;height:90vh;text-align:center;padding:24px;background:#0C110E;color:#9BB0A3\">"
                    + "<h2 style='color:#E8F0EA;margin:0 0 8px'>" + getString(R.string.offline_title) + "</h2><p>" + getString(R.string.offline_text) + "</p>"
                    + "<button onclick='location.href=" + retry + "' style='margin-top:16px;padding:12px 26px;border:0;border-radius:999px;background:#1E9E5E;color:#fff;font-size:16px;font-weight:bold'>"
                    + getString(R.string.offline_retry) + "</button></body>";
            applyBars(true);
            view.loadDataWithBaseURL(launchUrl, html, "text/html", "UTF-8", request.getUrl().toString());
        }
    }

    private class AppChrome extends WebChromeClient {
        @Override
        public void onPermissionRequest(PermissionRequest request) {
            runOnUiThread(request::deny);
        }
    }

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
