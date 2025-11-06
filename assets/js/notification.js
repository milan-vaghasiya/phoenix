import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging.js";
import { firebaseConfig, vapidKey } from "./notification-config.js";

// Init Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Save token to server
async function saveTokenToServer(token) {
    try {
        console.log("Token : ", token);
        $("#web_push_token").val(token);
        // Uncomment & update for CodeIgniter
        /*
        await fetch("/notifications/save_token", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ token })
        });
        */
    } catch (err) {
        console.error("Error saving token:", err);
    }
}

// Request permission + get token
async function requestAndGetToken() {
    const permission = await Notification.requestPermission();
    if (permission === "granted") {
        try {
            const token = await getToken(messaging, { vapidKey });
            if (token) {
                //console.log("FCM Token:", token);
                await saveTokenToServer(token);
            } else {
                console.warn("No registration token received.");
            }
        } catch (err) {
            console.error("Error getting token:", err);
        }
    } else {
        console.warn("Notification permission denied.");
    }
}

// Auto request if not granted
document.addEventListener("DOMContentLoaded", async () => {
    if (Notification.permission === "default") {
        await requestAndGetToken();
    } else if (Notification.permission === "granted") {
        await requestAndGetToken();
    } else {
        console.warn("Notifications are blocked by user.");
    }
});

// Foreground messages
onMessage(messaging, (payload) => {
    console.log("Foreground message:", payload);
    const { title, body, icon } = payload.notification;
    const { link } = payload.data;

    if (Notification.permission === "granted") {
        const n = new Notification(title, { body, icon });
        n.onclick = () => checkAndOpen(link);
    }
});

// Focus or open
function checkAndOpen(url) {
    if (document.visibilityState === "visible") {
        if (!window.location.href.includes(url)) {
            window.location.href = url;
        }
        window.focus();
    } else {
        window.open(url, "_blank");
    }
}
