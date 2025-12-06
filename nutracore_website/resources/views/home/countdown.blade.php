@php
    $product_countdowns = DB::table('product_countdowns')->where('id',1)->first();
    $is_show = 0;
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    if (!empty($product_countdowns) && $product_countdowns->status == 1) {
        $current = now(); // current date + time
        // Combine date + time
        $start = \Carbon\Carbon::parse($product_countdowns->start_date . ' ' . $product_countdowns->start_time);
        $end   = \Carbon\Carbon::parse($product_countdowns->end_date . ' ' . $product_countdowns->end_time);
        // Check if current time is between start & end
        if ($current->between($start, $end)) {
            $is_show = 1;
        }
    }
@endphp
<style>
    /* --------------------------------- */
    /* --- Base Styles --- */
    /* --------------------------------- */
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .sale-banner {
        background-color: #00a8a8; /* Coral/Red background */
        color: #ffffff;
        padding: 8px 15px; /* Slightly reduced padding */
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        min-height: 50px; /* Ensure a minimum height */
    }

    .content-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        max-width: 1200px;
    }

    /* --- Text Area Styles --- */
    .text-area {
        display: flex;
        flex-direction: column;
        margin-right: 15px;
        /* Ensures it takes only necessary space and allows timer to float right */
        flex-shrink: 1;
        min-width: 0; /* Prevents overflow */
    }

    .main-message {
        font-size: 18px; /* Slightly reduced font size for space */
        font-weight: bold;
        color: black;
        /*text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);*/
        white-space: nowrap; /* Keep text on one line if possible */
    }

    .secondary-message {
        font-size: 12px; /* Smaller secondary message */
        margin-top: 2px;
        white-space: nowrap;
    }

    /* --- Countdown Timer Styles --- */
    .countdown-timer {
        display: flex;
        align-items: center;
        margin: 0 15px; /* Reduced margin */
        flex-shrink: 0; /* Important: Prevents timer from shrinking */
    }

    .timer-unit {
        display: flex;
        flex-direction: column;
        align-items: center;
        line-height: 1;
    }

    .timer-value {
        font-size: 24px; /* Reduced font size for single row */
        font-weight: 900;
        color: white;
        margin: 0 1px;
    }

    .timer-label {
        font-size: 8px;
        text-transform: uppercase;
        margin-top: 2px;
        color: #ffffff;
    }

    .separator {
        font-size: 24px;
        font-weight: 900;
        color: white;
        align-self: flex-start;
        margin: 0 2px;
    }

    /* --- CTA Button Styles --- */
    .cta-button {
        background-color: black;
        color: #ffffff;
        border: none;
        padding: 8px 15px; /* Reduced padding */
        font-size: 14px; /* Reduced font size */
        font-weight: bold;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        white-space: nowrap;
        flex-shrink: 0; /* Important: Prevents button from shrinking */
        margin-left: auto; /* Pushes the button to the far right */
    }

    .cta-button:hover {
        background-color: #37b777;
    }

    /* --------------------------------- */
    /* --- Responsive CSS (Media Query) --- */
    /* --------------------------------- */
    @media (max-width: 600px) {
        .sale-banner {
            padding: 5px 10px; /* Even smaller padding */
        }

        .content-wrapper {
            /* Allow content to wrap if necessary */
            flex-wrap: wrap;
            justify-content: center;
        }

        .text-area {
            /* Center the text and make it full width */
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
        }

        .main-message {
            font-size: 16px;
        }

        .secondary-message {
            display: none; /* Hide the secondary message to save space */
        }

        .countdown-timer {
            /* Center timer under the text */
            order: 2; /* Place timer after text */
            margin: 5px 0 10px 0;
        }

        .cta-button {
            /* Make button full width and place at the bottom */
            order: 3;
            width: 100%;
            margin: 0;
        }
    }
</style>

<div class="sale-banner" style="display:  {{$is_show == 0?"none":""}}">
    <div class="content-wrapper">
        <div class="text-area">
            <div class="main-message">{{$product_countdowns->title??''}}</div>
            <div class="secondary-message">{{$product_countdowns->description??''}}</div>
        </div>

        <div class="countdown-timer" id="countdown">
            <div class="timer-unit">
                <span class="timer-value" id="days">00</span>
                <span class="timer-label">Days</span>
            </div>
            <span class="separator">:</span>
            <div class="timer-unit">
                <span class="timer-value" id="hours">00</span>
                <span class="timer-label">Hrs</span>
            </div>
            <span class="separator">:</span>
            <div class="timer-unit">
                <span class="timer-value" id="minutes">00</span>
                <span class="timer-label">Mins</span>
            </div>
            <span class="separator">:</span>
            <div class="timer-unit">
                <span class="timer-value" id="seconds">00</span>
                <span class="timer-label">Secs</span>
            </div>
        </div>

        <a href="{{url('collections/deals')}}" class="cta-button">Shop Now & Save</a>
    </div>
</div>

<script>
    // --- Countdown Timer Logic (10 minutes from now) ---

        const countdownStart = "{{ $start->format('Y-m-d H:i:s') }}";
        const countdownEnd = "{{ $end->format('Y-m-d H:i:s') }}";

    // Set the end date for the sale (e.g., 10 minutes from the time the script loads)
    const saleStartDate = new Date(countdownStart).getTime();
    const saleEndDate   = new Date(countdownEnd).getTime();

    // HTML elements
    const daysEl = document.getElementById('days');
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');
    const mainMessageEl = document.querySelector('.main-message');

    function updateCountdown() {

        const now = new Date().getTime();

        // If sale not started yet
        if (now < saleStartDate) {
            mainMessageEl.innerHTML = "Sale Starts Soon!";
            return;
        }

        const distance = saleEndDate - now;

        // Sale ended
        if (distance < 0) {
            clearInterval(timerInterval);
            daysEl.innerHTML = hoursEl.innerHTML = minutesEl.innerHTML = secondsEl.innerHTML = "00";
            mainMessageEl.innerHTML = "Sale Ended!";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const pad = (num) => (num < 10 ? "0" + num : num);

        daysEl.innerHTML = pad(days);
        hoursEl.innerHTML = pad(hours);
        minutesEl.innerHTML = pad(minutes);
        secondsEl.innerHTML = pad(seconds);
    }

    const timerInterval = setInterval(updateCountdown, 1000);
    updateCountdown();
</script>

