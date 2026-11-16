package com.saverio.foodtrack;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

/**
 * Woken by Firebase when another member changed something (the message itself carries nothing):
 * asks the API what is new and tells it, like the service worker does in the browser.
 */
public class PushService extends FirebaseMessagingService {

    @Override
    public void onMessageReceived(RemoteMessage message) {
        if (Push.appVisible) return; // The open app tells the user by itself
        try {
            Thread.sleep(4000); // Lets a burst of changes end up in one notification
            ActivityJobService.check(this);
        } catch (Exception e) { /* no connection: the 15-minute check or the next opening will tell */
        }
    }

    @Override
    public void onNewToken(String token) {
        // The page registers the new token the next time the app opens (the old one stops working and is removed)
    }
}
