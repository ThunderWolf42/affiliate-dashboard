self.addEventListener("push", (event) => {
    console.log("[Service Worker] Push Event diterima.");

    let payload = {
        title: "Notifikasi Baru",
        body: "",
        icon: "/favicon.ico",
        badge: "/favicon.ico",
        tag: null,
        data: {},
        actions: [],
    };

    if (event.data) {
        try {
            const rawData = event.data.json();
            console.log("[Service Worker] Raw Data JSON:", rawData);

            // Antisipasi jika Laravel membungkus JSON di dalam key "payload"
            const extractedData = rawData.payload ? rawData.payload : rawData;
            console.log("[Service Worker] Extracted Data (setelah filter):", extractedData);

            payload = {
                ...payload,
                ...extractedData,
            };
        } catch (e) {
            console.warn("[Service Worker] Gagal parsing JSON, mencoba format teks biasa:", e);
            payload.body = event.data.text();
        }
    } else {
        console.warn("[Service Worker] Push event diterima tapi tidak ada data (event.data kosong).");
    }

    console.log("[Service Worker] Menampilkan Notifikasi:", payload);

    event.waitUntil(
        self.registration.showNotification(payload.title, {
            body: payload.body,
            icon: payload.icon,
            badge: payload.badge,
            tag: payload.tag,
            data: payload.data,
            actions: payload.actions,
        })
    );
});

self.addEventListener("notificationclick", (event) => {
    console.log("[Service Worker] Notifikasi diklik.");
    event.notification.close();

    // Mengambil target URL
    let targetUrl = event.notification.data?.url || "/admin/chat-room";
    console.log("[Service Worker] Target URL ditemukan:", targetUrl);

    // Konversi ke URL absolut
    const fullUrl = new URL(targetUrl, self.location.origin).href;
    console.log("[Service Worker] Full URL Absolut:", fullUrl);

    event.waitUntil(
        clients
            .matchAll({
                type: "window",
                includeUncontrolled: true,
            })
            .then((clientList) => {
                console.log(`[Service Worker] Jumlah tab terbuka: ${clientList.length}`);

                for (const client of clientList) {
                    // Cek apakah ada tab yang URL-nya cocok
                    if (client.url === fullUrl && "focus" in client) {
                        console.log("[Service Worker] Tab cocok ditemukan, memfokuskan tab...");
                        return client.focus();
                    }
                }

                // Jika tidak ada tab yang cocok, buka tab baru
                if (clients.openWindow) {
                    console.log("[Service Worker] Tidak ada tab cocok, membuka jendela baru.");
                    return clients.openWindow(fullUrl);
                }
            })
    );
});
