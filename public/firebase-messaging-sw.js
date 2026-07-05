importScripts(
    "https://www.gstatic.com/firebasejs/11.0.1/firebase-app-compat.js"
);

importScripts(
    "https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging-compat.js"
);

firebase.initializeApp({
    apiKey: "AIzaSyA3OPdR1Q9nkFYpfREr6T2IuzsEsQYGy8E",
    authDomain: "ukrida-affiliate-dashboard.firebaseapp.com",
    projectId: "ukrida-affiliate-dashboard",
    storageBucket: "ukrida-affiliate-dashboard.firebasestorage.app",
    messagingSenderId: "827014141025",
    appId: "1:827014141025:web:375327f9a557561fe98620",
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log("Background Message:", payload);

    self.registration.showNotification(
        payload.notification.title,
        {
            body: payload.notification.body,
            icon: "/favicon.ico",
        }
    );
});
