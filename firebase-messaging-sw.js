// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    apiKey: "AIzaSyA8fXYZre4nOBnWvV6tYttjR_laBzFSJE4",
    authDomain: "nativebit-175ba.firebaseapp.com",
    projectId: "nativebit-175ba",
    storageBucket: "nativebit-175ba.appspot.com",
    messagingSenderId: "695494499148",
    appId: "1:695494499148:web:0be6573f7b88f1f67a0de9",
    measurementId: "G-GERRDB3419"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();


// If you would like to customize notifications that are received in the
// background (Web app is closed or not in browser focus) then you should
// implement this optional method.
// [START background_handler]
messaging.setBackgroundMessageHandler(function (payload) {
    //console.log('[firebase-messaging-sw.js] Set background message ', payload);
	
    // Customize notification here
    payload = JSON.parse(payload.data.data);
    var notificationTitle = payload.title;
    var notificationOptions = {
        body : payload.message,
        icon : payload.image,
        tag : payload.onclick,
    };
    return self.registration.showNotification(notificationTitle,notificationOptions);  
});
// [END background_handler]

messaging.onBackgroundMessage((payload) => {
    //console.log('[firebase-messaging-sw.js] Received background message ', payload);
	
    // Customize notification here
    payload = JSON.parse(payload.data.data);
    //console.log('[firebase-messaging-sw.js] Received background message ', payload);
    var notificationTitle = payload.title;
    var notificationOptions = {
        body : payload.message,
        icon : payload.image,
        tag : payload.onclick,
    }; 
    return self.registration.showNotification(notificationTitle,notificationOptions);    
});

self.addEventListener('notificationclick', function(event) {
    var redirectUrl = event.notification.tag;
    //console.log('On notification click: ', event.notification);
    event.notification.close();

    if (redirectUrl) {       
        event.waitUntil(async function () {
            var allClients = await clients.matchAll({
                includeUncontrolled: true
            });
            var chatClient;            
            for (var i = 0; i < allClients.length; i++) {
                var client = allClients[i];                
                if (client['url'].indexOf(redirectUrl) >= 0) {
                    client.focus();
                    chatClient = client;
                    break;
                }
            }
            if (chatClient == null || chatClient == 'undefined') {
                chatClient = clients.openWindow(redirectUrl);
                return chatClient;
            }
        }());        
    }
});