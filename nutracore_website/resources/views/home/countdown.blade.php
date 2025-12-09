<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Responsive Single-Row Sale Banner</title>
    <style>
        /* --------------------------------- */
        /* --- Base Styles --- */
        /* --------------------------------- */
        body { margin: 0; font-family: Arial, sans-serif; }
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
        .cta-button:hover { background-color: #37b777; }

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
</head>
<body>
<div class="sale-banner">
    <div class="content-wrapper">
        <div class="text-area">
            <div class="main-message">Limited Time Only! Sale Ends In:</div>
            <div class="secondary-message">Hurry, before it's too late!</div>
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

        <button class="cta-button">Shop Now & Save</button>
    </div>
</div>

<script>
    // --- Countdown Timer Logic (10 minutes from now) ---

    // Set the end date for the sale (e.g., 10 minutes from the time the script loads)
    const saleEndDate = new Date().getTime() + (10 * 60 * 1000);

    // Get the display elements
    const daysEl = document.getElementById('days');
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');
    const mainMessageEl = document.querySelector('.main-message');


    function updateCountdown() {
        const now = new Date().getTime();
        const distance = saleEndDate - now;

        // Time calculations
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Function to pad single digits with a leading zero
        const pad = (num) => num < 10 ? '0' + num : num;

        // Display the result
        daysEl.innerHTML = pad(days);
        hoursEl.innerHTML = pad(hours);
        minutesEl.innerHTML = pad(minutes);
        secondsEl.innerHTML = pad(seconds);

        // If the countdown is finished
        if (distance < 0) {
            clearInterval(timerInterval); // Stop the timer
            daysEl.innerHTML = hoursEl.innerHTML = minutesEl.innerHTML = secondsEl.innerHTML = '00';
            mainMessageEl.innerHTML = "Sale Ended!";
            // Optionally hide the timer or change banner color
        }
    }

    // Update the count down every 1 second
    const timerInterval = setInterval(updateCountdown, 1000);

    // Initial call
    updateCountdown();
</script>
</body>
</html>
