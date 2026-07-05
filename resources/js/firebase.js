import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";

const firebaseConfig = {
    apiKey: "AIzaSyA3OPdR1Q9nkFYpfREr6I2IuzsEsQYGy8E",
    authDomain: "ukrida-affiliate-dashboard.firebaseapp.com",
    projectId: "ukrida-affiliate-dashboard",
    storageBucket: "ukrida-affiliate-dashboard.firebasestorage.app",
    messagingSenderId: "827014141025",
    appId: "1:827014141025:web:375327f9a557561fe98620",
    measurementId: "G-NMPD7NL2BB",
};

const app = initializeApp(firebaseConfig);

console.log("Firebase loaded");

export const messaging = getMessaging(app);

// ===== TAMBAHKAN INI =====
console.log("Requesting notification permission...");

Notification.requestPermission()
    .then(async (permission) => {
        console.log("Permission:", permission);

        if (permission !== "granted") {
            return;
        }

        const token = await getToken(messaging, {
            vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY,
        });

        console.log("FCM TOKEN:", token);

        const response = await fetch("/save-fcm-token", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
                Accept: "application/json",
            },
            body: JSON.stringify({
                token: token,
            }),
        });

        console.log("SAVE FCM STATUS:", response.status);
        console.log("SAVE FCM RESPONSE:", await response.text());
    })
    .catch(console.error);
// =========================

// Listener ketika tab sedang dibuka
onMessage(messaging, (payload) => {
    console.log("Foreground message:", payload);

    const notification = new Notification(payload.notification.title, {
        body: payload.notification.body,
        icon: payload.notification.icon,
    });

    notification.onclick = () => {
        window.focus();

        const url = payload.fcmOptions?.link ?? payload.data?.url;

        if (url) {
            window.location.href = url;
        }
    };
});
