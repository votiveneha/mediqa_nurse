/**
 * Test Notification Counter
 * Debug script to test notification system
 */

// Test 1: Check if counter is initialized
console.log('=== Notification Counter Debug ===');
console.log('Counter exists:', !!window.ChatNotificationCounter);
console.log('User ID:', window.ChatNotificationCounter?.userId);
console.log('Current count:', window.ChatNotificationCounter?.unreadCount);
console.log('Echo exists:', !!window.Echo);

// Test 2: Manually increment
console.log('\n=== Testing Manual Increment ===');
if (window.ChatNotificationCounter) {
    window.ChatNotificationCounter.incrementCount();
    console.log('Count after increment:', window.ChatNotificationCounter.unreadCount);
}

// Test 3: Check dropdown element
console.log('\n=== Checking Dropdown Element ===');
const messagesLink = document.querySelector('a.dropdown-item[href*="messages"]');
console.log('Dropdown element found:', !!messagesLink);
console.log('Current text:', messagesLink?.textContent);

// Test 4: Check badge
console.log('\n=== Checking Badge ===');
const bellIcon = document.querySelector('#dropdownNotify');
const badge = bellIcon?.querySelector('.notify-badge');
console.log('Badge exists:', !!badge);
console.log('Badge text:', badge?.textContent);

// Test 5: Check Pusher connection
console.log('\n=== Checking Pusher ===');
if (window.Echo) {
    console.log('Pusher state:', window.Echo.connector.pusher.connection.state);
    console.log('Channels:', window.Echo.channels());
}

// Test 6: Manually update display
console.log('\n=== Testing Display Update ===');
if (window.ChatNotificationCounter) {
    window.ChatNotificationCounter.updateCount(5);
    console.log('Display updated with count 5');
}

console.log('\n=== Debug Complete ===');
