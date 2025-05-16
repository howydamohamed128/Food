// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
    apiKey: "AIzaSyAdBbKI_xZ8Y7CLj-kYiLAY7Z7NX2i3UPo",
    authDomain: "naeemalshemal-8e94c.firebaseapp.com",
    projectId: "naeemalshemal-8e94c",
    storageBucket: "naeemalshemal-8e94c.firebasestorage.app",
    messagingSenderId: "1025851800837",
    appId: "1:1025851800837:web:269d567293ac37601c0b6c",
    measurementId: "G-CJDK4YHQNR"
});


const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {

    const notificationTitle = payload?.data?.title;
    const notificationOptions = {
        body: payload?.data?.body,
        icon: 'https://naeemalshemal.com/storage/01J9SYJY797YKKSMYRSY0N96N1.png',
        silent: false,
        tag: [payload.data?.entity_type, payload.data?.entity_id],
        vibrate: [500],
        requireInteraction: true,
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
self.addEventListener('notificationclick', function (event) {
    const [entityType, entityID] = event.notification.tag.split(',')

    clients.openWindow(urlMapper(entityType, entityID));
});

const urlMapper = function (entityType, entityID) {
    const mapping = {
        order: `/admin/orders/${entityID}/view`,
        customer: `/admin/users/customers/${entityID}/view`,
    }
    return mapping[entityType];
};
