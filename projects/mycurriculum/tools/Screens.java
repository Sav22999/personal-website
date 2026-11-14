/*
 * MyCurriculum — store and manifest screenshots, taken from the running app with Chrome headless.
 *
 *   java tools/Screens.java <app url> <session cookie> <output dir> <public user>
 *   e.g. java tools/Screens.java http://localhost:8098/projects/mycurriculum/ 3f…c1 store/screens giulia-ferri
 *
 * The cookie is the value of "mycurriculum_auth" of an account whose CV should appear (a demo account on a test
 * database, never a real one). It drives Chrome through the DevTools protocol (java.net.http's WebSocket, no
 * libraries): phone shots are 360×640 at 3× (1080×1920, what Google Play asks for), in Italian and English, plus one
 * desktop shot (1440×900) for the web app manifest.
 */

import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.net.http.WebSocket;
import java.nio.file.Files;
import java.nio.file.Path;
import java.util.Base64;
import java.util.Map;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.CompletionStage;
import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class Screens {
    static final String CHROME = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome";
    static final int PORT = 9339;

    static WebSocket ws;
    static final AtomicInteger seq = new AtomicInteger();
    static final Map<Integer, CompletableFuture<String>> waiting = new ConcurrentHashMap<>();

    public static void main(String[] args) throws Exception {
        String app = args[0], cookie = args[1], user = args[3];
        Path out = Path.of(args[2]);
        Files.createDirectories(out.resolve("it"));
        Files.createDirectories(out.resolve("en"));
        Path profile = Files.createTempDirectory("mc-chrome");
        Process chrome = new ProcessBuilder(CHROME, "--headless=new", "--remote-debugging-port=" + PORT, "--user-data-dir=" + profile,
                "--hide-scrollbars", "--no-first-run", "--no-default-browser-check", "--force-color-profile=srgb", "about:blank").redirectErrorStream(true).start();
        try {
            connect();
            URI u = URI.create(app);
            send("Network.enable", "{}");
            send("Network.setCookie", "{\"name\":\"mycurriculum_auth\",\"value\":\"" + cookie + "\",\"domain\":\"" + u.getHost() + "\",\"path\":\"" + u.getPath() + "\"}");
            send("Emulation.setEmulatedMedia", "{\"features\":[{\"name\":\"prefers-color-scheme\",\"value\":\"light\"}]}");
            for (String lang : new String[]{"it", "en"}) {
                phone();
                // 1. the editor, personal details
                open(app, lang, "");
                shot(out.resolve(lang + "/1-editor.png"), "document.querySelector('.panel').scrollTop = 0");
                // 2. the CV preview
                shot(out.resolve(lang + "/2-preview.png"), "document.querySelector('.topbar [aria-pressed]').click()");
                // 3. the templates
                open(app, lang, "");
                shot(out.resolve(lang + "/3-templates.png"), "document.querySelectorAll('.tab')[1].click(); document.querySelector('.panel-body').scrollIntoView()");
                // 4. sections: drag and drop, ↑ ↓, collapse
                shot(out.resolve(lang + "/4-sections.png"), "document.querySelectorAll('.tab')[0].click(); requestAnimationFrame(() => window.scrollTo(0, document.querySelector('.sections').getBoundingClientRect().top + scrollY - 120))");
                // 5. the public page with its language menu
                open(app + "?user=" + user + "&lang=" + lang, lang, "");
                shot(out.resolve(lang + "/5-public.png"), "document.querySelector('.lang-btn').click()");
            }
            // desktop: the editor with the outline (preview hidden)
            send("Emulation.setDeviceMetricsOverride", "{\"width\":1440,\"height\":900,\"deviceScaleFactor\":1,\"mobile\":false}");
            open(app, "en", "");
            shot(out.resolve("wide-editor.png"), "document.querySelector('.panel').scrollTop = 0");
        } finally {
            try {
                ws.sendClose(WebSocket.NORMAL_CLOSURE, "").get(2, TimeUnit.SECONDS);
            } catch (Exception ignored) { }
            chrome.destroy();
            chrome.waitFor(5, TimeUnit.SECONDS);
        }
        System.out.println("screenshots written to " + out);
    }

    static void phone() throws Exception {
        send("Emulation.setDeviceMetricsOverride", "{\"width\":360,\"height\":640,\"deviceScaleFactor\":3,\"mobile\":true}");
        send("Emulation.setTouchEmulationEnabled", "{\"enabled\":true}");
    }

    /** Opens a page with the app language set (and the preview hidden), and waits for it to settle. */
    static void open(String url, String lang, String js) throws Exception {
        send("Page.navigate", "{\"url\":\"" + url + "\"}");
        Thread.sleep(1500);
        eval("localStorage.setItem('mc_lang','" + lang + "'); localStorage.removeItem('mc_preview'); " + js);
        send("Page.reload", "{}");
        Thread.sleep(2500);
        // in the English shots the editor shows the English version of the CV too
        if (lang.equals("en")) eval("[...document.querySelectorAll('.lang-chips .chip')].find((c) => c.textContent.includes('English'))?.click()");
        eval("document.fonts.ready.then(() => true)");
    }

    /** Runs js (if any), waits, and saves a PNG of the viewport. */
    static void shot(Path file, String js) throws Exception {
        if (!js.isEmpty()) eval(js);
        Thread.sleep(1200);
        String res = send("Page.captureScreenshot", "{\"format\":\"png\"}");
        Matcher m = Pattern.compile("\"data\":\"([^\"]+)\"").matcher(res);
        if (!m.find()) throw new IOException("no screenshot: " + res);
        Files.write(file, Base64.getDecoder().decode(m.group(1)));
        System.out.println("  " + file);
    }

    static String eval(String js) throws Exception {
        String quoted = "\"" + js.replace("\\", "\\\\").replace("\"", "\\\"") + "\"";
        return send("Runtime.evaluate", "{\"expression\":" + quoted + ",\"awaitPromise\":true}");
    }

    // ------------------------------------------------------------------ DevTools protocol over a WebSocket

    static void connect() throws Exception {
        HttpClient http = HttpClient.newHttpClient();
        String list = null;
        for (int i = 0; i < 50 && list == null; i++) {
            try {
                list = http.send(HttpRequest.newBuilder(URI.create("http://127.0.0.1:" + PORT + "/json/list")).build(), HttpResponse.BodyHandlers.ofString()).body();
            } catch (IOException e) {
                Thread.sleep(200);
            }
        }
        if (list == null) throw new IOException("Chrome did not start");
        Matcher m = Pattern.compile("\"type\":\\s*\"page\"[\\s\\S]*?\"webSocketDebuggerUrl\":\\s*\"([^\"]+)\"").matcher(list);
        if (!m.find()) throw new IOException("no page target: " + list);
        ws = http.newWebSocketBuilder().buildAsync(URI.create(m.group(1)), new WebSocket.Listener() {
            final StringBuilder buf = new StringBuilder();

            @Override
            public CompletionStage<?> onText(WebSocket w, CharSequence data, boolean last) {
                buf.append(data);
                if (last) {
                    String msg = buf.toString();
                    buf.setLength(0);
                    Matcher id = Pattern.compile("^\\{\"id\":(\\d+)").matcher(msg);
                    if (id.find()) {
                        CompletableFuture<String> f = waiting.remove(Integer.parseInt(id.group(1)));
                        if (f != null) f.complete(msg);
                    }
                }
                w.request(1);
                return null;
            }
        }).get(10, TimeUnit.SECONDS);
    }

    static String send(String method, String params) throws Exception {
        int id = seq.incrementAndGet();
        CompletableFuture<String> f = new CompletableFuture<>();
        waiting.put(id, f);
        ws.sendText("{\"id\":" + id + ",\"method\":\"" + method + "\",\"params\":" + params + "}", true);
        return f.get(30, TimeUnit.SECONDS);
    }
}
