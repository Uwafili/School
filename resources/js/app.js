import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const notificationList = document.querySelector('[data-store-notifications]');
const riderOrderList = document.querySelector('[data-nearby-order-list]');
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if ((notificationList || riderOrderList) && pusherKey) {
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
	const echo = new Echo({
		broadcaster: 'pusher',
		key: pusherKey,
		cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
		forceTLS: true,
		authEndpoint: '/broadcasting/auth',
		auth: { headers: { 'X-CSRF-TOKEN': csrfToken } },
	});

	if (notificationList) {
		echo.private(`stores.${notificationList.dataset.storeUserId}`)
			.listen('.store.order.created', (event) => {
			const item = document.createElement('article');
			item.className = 'rounded-xl border border-yellow-200 bg-yellow-50 p-4';

			const title = document.createElement('p');
			title.className = 'font-bold text-gray-900';
			title.textContent = event.notification.title;

			const message = document.createElement('p');
			message.className = 'mt-1 text-sm text-gray-600';
			message.textContent = event.notification.message;

			item.append(title, message);
			notificationList.prepend(item);
			});
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

			const items = document.createElement('p');
			items.className = 'mt-2 text-sm text-gray-600';
			items.textContent = order.items_description;

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
			item.append(heading, pickup, dropoff, items, form);
			riderOrderList.prepend(item);
			riderOrderList.querySelector('[data-nearby-empty]')?.remove();

			const count = document.querySelector('[data-nearby-count]');
			if (count) count.textContent = String(Number(count.textContent) + 1);
			});
	}
}
