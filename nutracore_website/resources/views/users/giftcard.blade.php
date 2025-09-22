@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $user = Auth::user();
    $imageList = [
        url('public/assets/giftcard/BhaiDooj.png'),
        url('public/assets/giftcard/HappyAnniversary.png'),
        url('public/assets/giftcard/HappyBirthday.png'),
        url('public/assets/giftcard/HappyDiwali.png'),
        url('public/assets/giftcard/MerryChristmas.png'),
        url('public/assets/giftcard/RakshaBandhan.png'),
        url('public/assets/giftcard/ThankYou.png'),
    ];
    $typeList = [
        "BhaiDooj",
        "HappyAnniversary",
        "HappyBirthday",
        "HappyDiwali",
        "MerryChristmas",
        "RakshaBandhan",
        "ThankYou",
    ];

    ?>
    <style>

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .card-selection {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            width: 150px;
            cursor: pointer;
            transition: transform 0.2s, border-color 0.2s;
        }

        .card img {
            width: 100%;
            height: auto;
        }

        .card p {
            margin: 10px 0;
            font-weight: bold;
        }

        .card.selected {
            border-color: #00A8A8;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
            transform: scale(1.05);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group select,
        .form-group input {
            padding: 8px;
            width: 200px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .amount-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .amount-buttons button {
            padding: 10px 20px;
            border: 1px solid #00A8A8;
            background: white;
            color: #00A8A8;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .amount-buttons button.selected {
            background: #00A8A8;
            color: white;
        }

        .proceed-btn {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            padding: 15px;
            background: #00A8A8;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .proceed-btn:hover {
            background: #00A8A8;
        }

        .card-selection {
            display: flex;
            flex-wrap: wrap; /* Wrap to next line if needed */
            gap: 20px; /* Space between cards */
            justify-content: center;
        }

        .card-selection .card {
            flex: 1 1 250px; /* Grow/shrink, min width 250px */
            max-width: 300px; /* Maximum card width */
            background: transparent; /* No background */
            border: none; /* Remove border */
            padding: 0; /* Remove padding */
        }

        .card-selection img {
            width: 100%; /* Make image fill card */
            height: auto;
            display: block;
            border-radius: 12px; /* Optional: rounded corners */
            object-fit: cover; /* Maintain aspect ratio */
        }

        .giftcard-slider {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 0;
            scroll-snap-type: x mandatory;
        }

        .slide-card {
            flex: 0 0 auto;
            width: 160px;
            cursor: pointer;
            scroll-snap-align: start;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .slide-card img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
        }

        .slide-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .preview-img {
            max-width: 300px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3);
        }


    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> GiftCard
                </div>
            </div>
        </div>
        <div class="page-content pb-150">
            <div class="container">
                <div class="row justify-content-center">

                    <div class="giftcard-slider-container mt-4">
                        <!-- Slider -->
                        <div class="giftcard-slider">
                            @foreach($imageList as $index => $image)
                                <div class="slide-card" data-index="{{ $index }}">
                                    <img src="{{ $image }}" alt="Gift Card">
                                </div>
                            @endforeach
                        </div>

                        <!-- Selected Image Preview -->
                        <div class="selected-preview text-center mt-4" style="display:none;">
                            <img id="selectedImage" src="" alt="Selected Gift Card" class="preview-img">
                            <p id="selectedType" class="mt-2 fw-bold"></p>
                        </div>
                    </div>


                    <!-- Quantity -->
                    <div class="form-group mt-4 text-center">
                        <label for="qtySelect">Qty</label><br>
                        <select id="qtySelect" class="form-control d-inline-block" style="width: auto;">
                            <option value="">Select Quantity</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>


                    <!-- Amount -->
                    <div class="form-group mt-4">
                        <label>Amount</label>
                        <div class="amount-buttons">
                            <button type="button" data-value="200">₹200</button>
                            <button type="button" data-value="500">₹500</button>
                            <button type="button" data-value="1000">₹1000</button>
                            <button type="button" data-value="2000">₹2000</button>
                        </div>
                    </div>

                    <!-- Proceed Button -->
                    <div class="text-center mt-4">
                        <button class="proceed-btn btn btn-primary" onclick="proceed()" disabled>Proceed to Continue
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Hidden inputs to store selection -->
    <input type="hidden" id="amount" value="">
    <input type="hidden" id="type" value="">
    <input type="hidden" id="qty" value="">

    <script>

        const typeList = @json($typeList);
        const slideCards = document.querySelectorAll('.slide-card');
        const previewContainer = document.querySelector('.selected-preview');
        const selectedImage = document.getElementById('selectedImage');
        const selectedType = document.getElementById('selectedType');

        const amountButtons = document.querySelectorAll('.amount-buttons button');
        const qtySelect = document.getElementById('qtySelect');

        const amountInput = document.getElementById('amount');
        const typeInput = document.getElementById('type');
        const qtyInput = document.getElementById('qty');
        const proceedBtn = document.querySelector('.proceed-btn');

        // Handle card click (select gift card type + preview)
        slideCards.forEach(card => {
            card.addEventListener('click', () => {
                // highlight selected card
                slideCards.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');

                const index = parseInt(card.getAttribute('data-index'));
                const imgSrc = card.querySelector('img').src;
                const type = typeList[index];

                // update preview + hidden input
                selectedImage.src = imgSrc;
                selectedType.textContent = type;
                typeInput.value = type;

                previewContainer.style.display = "block";

                checkAllSelected();
            });
        });

        // Handle amount selection
        amountButtons.forEach(button => {
            button.addEventListener('click', () => {
                amountButtons.forEach(b => b.classList.remove('selected'));
                button.classList.add('selected');
                amountInput.value = button.getAttribute('data-value');
                checkAllSelected();
            });
        });

        // Handle quantity selection
        qtySelect.addEventListener('change', () => {
            qtyInput.value = qtySelect.value;
            checkAllSelected();
        });

        // Enable Proceed button if all selected
        function checkAllSelected() {
            if (typeInput.value && amountInput.value && qtyInput.value) {
                proceedBtn.disabled = false;
            } else {
                proceedBtn.disabled = true;
            }
        }

        // Proceed button click
        function proceed() {
            if (!typeInput.value || !amountInput.value || !qtyInput.value) {
                alert("Please fill all fields.");
                return;
            }
            alert(`Selected Type: ${typeInput.value}\nAmount: ₹${amountInput.value}\nQty: ${qtyInput.value}`);
        }
    </script>

@endsection
