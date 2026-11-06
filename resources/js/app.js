import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const page = document.body;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const notificationList = document.querySelector('[data-store-notifications]');
const riderOrderList = document.querySelector('[data-nearby-order-list]');
const pusherKey = page.dataset.foodstorePusherKey;
const broadcastEnabled = page.dataset.foodstoreBroadcastEnabled === 'true';

function appendStoreNotification(notification) {
    if (!notificationList) return;

    notificationList.querySelector(':scope > p')?.remove();

    const item = document.createElement('article');
    item.className = 'flex items-start gap-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4';

    const content = document.createElement('div');
    content.className = 'min-w-0 flex-1';

    const title = document.createElement('p');
    title.className = 'font-bold text-gray-900';
    title.textContent = notification.title || 'Order update';

    const message = document.createElement('p');
    message.className = 'mt-1 text-sm text-gray-600';
    message.textContent = notification.message || '';

    content.append(title, message);
    item.append(content);
    notificationList.prepend(item);

    const unreadCount = document.querySelector('[data-store-unread-count]');
    if (unreadCount) {
        const count = Number(unreadCount.dataset.storeUnreadCount || 0) + 1;
        unreadCount.dataset.storeUnreadCount = String(count);
        unreadCount.textContent = `${count} unread`;
    }
}

function showNotification(notification) {
    const toast = document.createElement('aside');
    toast.className = 'fixed inset-x-3 bottom-20 z-[80] mx-auto max-w-md rounded-xl border border-yellow-200 bg-white p-4 text-gray-900 shadow-xl sm:inset-x-auto sm:right-4 sm:bottom-4';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');

    const title = document.createElement('p');
    title.className = 'font-bold';
    title.textContent = notification.title || 'FoodStore update';

    const message = document.createElement('p');
    message.className = 'mt-1 text-sm text-gray-600';
    message.textContent = notification.message || '';

    toast.append(title, message);
    document.body.append(toast);
    window.setTimeout(() => toast.remove(), 6000);
}

function handleNotification(notification) {
    showNotification(notification);
    appendStoreNotification(notification);

    const orderId = String(notification.order_id || '');
    const trackedOrder = document.querySelector('[data-order-tracking]');
    const isStatusUpdate = notification.type?.startsWith('order_status_');
    const isRiderAssignmentUpdate = [
        'order_assigned',
        'order_assignment_changed',
        'order_cancelled',
    ].includes(notification.type);
    const isStoreDispatchUpdate = notification.type === 'order_assignment_accepted';

    if (
        (trackedOrder && trackedOrder.dataset.orderId === orderId)
        || (isStatusUpdate && document.querySelector('[data-customer-dashboard]'))
        || (isRiderAssignmentUpdate && document.querySelector('[data-rider-dashboard]'))
        || (isStoreDispatchUpdate && document.querySelector('[data-store-dashboard]'))
    ) {
        window.setTimeout(() => window.location.reload(), 1200);
    }
}

if (broadcastEnabled && pusherKey) {
    const echoOptions = {
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: page.dataset.foodstorePusherCluster,
        forceTLS: page.dataset.foodstorePusherScheme !== 'http',
        authEndpoint: '/broadcasting/auth',
        auth: { headers: { 'X-CSRF-TOKEN': csrfToken } },
    };
    const pusherHost = page.dataset.foodstorePusherHost;
    const pusherPort = Number(page.dataset.foodstorePusherPort);

    if (pusherHost) {
        echoOptions.wsHost = pusherHost;
        echoOptions.wssHost = pusherHost;
        echoOptions.wsPort = pusherPort || 80;
        echoOptions.wssPort = pusherPort || 443;
    }

    const echo = new Echo(echoOptions);

    window.Echo = echo;

    const userId = page.dataset.foodstoreUserId;
    if (userId) {
        echo.private(`App.Models.User.${userId}`).notification(handleNotification);
    }

    const riderId = page.dataset.foodstoreRiderId;
    if (riderId) {
        echo.private(`App.Models.Rider.${riderId}`).notification(handleNotification);
    }

    if (riderOrderList) {
        echo.private(`riders.${riderOrderList.dataset.riderId}`)
            .listen('.nearby.order.created', ({ order }) => {
                if ([...riderOrderList.querySelectorAll('[data-order-id]')].some((item) => item.dataset.orderId === String(order.id))) return;

                const item = document.createElement('article');
                item.dataset.orderId = order.id;
                item.className = 'rounded-xl border border-orange-100 bg-orange-50/60 p-4';

                const heading = document.createElement('h3');
                heading.className = 'font-black text-gray-900';
                heading.textContent = `Order #${order.id} - ${order.store_name} · ${order.distance_km} km away`;

                const pickup = document.createElement('p');
                pickup.className = 'mt-2 text-sm text-gray-700';
                pickup.textContent = `Pickup: ${order.store_address}`;

                const dropoff = document.createElement('p');
                dropoff.className = 'mt-1 text-sm text-gray-700';
                dropoff.textContent = `Drop-off: ${order.customer_address}`;

                const orderItems = document.createElement('p');
                orderItems.className = 'mt-2 text-sm text-gray-600';
                orderItems.textContent = order.items_description;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/rider/order/${encodeURIComponent(order.id)}/bid`;
                form.className = 'mt-3 flex items-end gap-2';

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = csrfToken || '';

                const offer = document.createElement('input');
                offer.type = 'number';
                offer.name = 'amount';
                offer.min = '0';
                offer.max = '1000000';
                offer.step = '0.01';
                offer.value = order.delivery_fee;
                offer.required = true;
                offer.className = 'mt-1 w-full rounded-lg border-gray-200 px-3 py-2';

                const label = document.createElement('label');
                label.className = 'flex-1 text-xs font-bold text-gray-700';
                label.textContent = 'Delivery offer (₦)';
                label.append(offer);

                const button = document.createElement('button');
                button.type = 'submit';
                button.className = 'rounded-lg bg-gray-900 px-4 py-2.5 text-xs font-black text-white hover:bg-orange-600';
                button.textContent = 'Request assignment';

                form.append(token, label, button);
                item.append(heading, pickup, dropoff, orderItems, form);
                riderOrderList.prepend(item);
                riderOrderList.querySelector('[data-nearby-empty]')?.remove();

                const count = document.querySelector('[data-nearby-count]');
                if (count) count.textContent = String(Number(count.textContent) + 1);
            });
    }
}
