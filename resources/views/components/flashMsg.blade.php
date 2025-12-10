@props(['msg', 'seconds' => 3]) <!-- default timer 10s -->

<p {{ $attributes->merge(['class' => 'flex items-center justify-between gap-2 text-sm font-medium text-white bg-green-600 px-4 py-2 rounded-md shadow-md']) }}>
    <span class="flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        {{ $msg }}
    </span>
    <span id="alert-timer-{{ $attributes->get('id') }}" class="font-semibold">{{ $seconds }}</span>s
</p>

<script>
    (function() {
        let timerId = "{{ $attributes->get('id') }}";
        let timeLeft = {{ $seconds }};
        let display = document.getElementById("alert-timer-" + timerId);

        let countdown = setInterval(() => {
            if(timeLeft <= 0) {
                clearInterval(countdown);
                // Optional: hide alert or take action
                if(display) display.parentElement.style.display = 'none';
            } else {
                display.innerText = timeLeft;
                timeLeft--;
            }
        }, 1000);
    })();
</script>
