package com.saverio.thinkhop;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

import org.json.JSONArray;
import org.json.JSONObject;

/**
 * Woken by Firebase when there is something to tell (the message itself carries nothing):
 * asks the API what is new and shows it, like the service worker does in the browser.
 */
public class PushService extends FirebaseMessagingService {

    @Override
    public void onMessageReceived(RemoteMessage message) {
        if (Push.appVisible) return; // Whoever is playing doesn't need a notification
        try {
            JSONObject d = Push.get(this, "api/index.php?r=notify.pending");
            JSONArray items = d == null ? null : d.optJSONArray("items");
            if (items == null) return;
            for (int i = 0; i < items.length(); i++) {
                JSONObject n = items.getJSONObject(i);
                Push.show(this, "thinkhop-" + n.optString("type", "news"), n.optString("text", ""), "");
            }
        } catch (Exception e) { /* no connection: the news waits for the next time the app opens */
        }
    }

    @Override
    public void onNewToken(String token) {
        // The page registers the new token the next time the app opens (the old one stops working and is removed)
    }
}
