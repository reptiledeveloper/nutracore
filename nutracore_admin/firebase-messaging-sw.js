// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
    apiKey: "AIzaSyCIhr1LtqHPqm0WzovXx8aU1VJcmsz86Oo",
    authDomain: "buybuycart-317d4.firebaseapp.com",
    databaseURL: "https://buybuycart-317d4-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "buybuycart-317d4",
    storageBucket: "buybuycart-317d4.firebasestorage.app",
    messagingSenderId: "655007432250",
    appId: "1:655007432250:web:b8eacab20fa8a63f2dbf03",
    measurementId: "G-LJ3L7WBMWG"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
    console.log("Message received Background. ", payload);
    const title = payload.notification.title;
    const options = {
        body: payload.notification.body,
        icon: payload.notification.icon,
    };
    
    self.registration.showNotification(title, options);
});
