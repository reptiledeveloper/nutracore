<div class="row">
    <div class="row">
        <div class="col-lg-8 mb-40">
            <h1 class="heading-2 mb-10">Your Cart</h1>
            <div class="d-flex justify-content-between">
                <h6 class="text-body">There are <span class="text-brand">{{count($cart_products)}}</span> products in
                    your cart</h6>

            </div>
        </div>
    </div>
    @php

        $cartValue = $cart_data['cartValue']??'';
    //    print_r($cart_data);
        $total_price  =$cartValue['total_price']??0;
        $coupon_discount  =$cartValue['coupon_discount']??0;
        $message  =$cartValue['message']??'';

        $delivery_charge = $cartValue['delivery_charges']??0;
        $subscription_amount = $cartValue['subscription_amount']??0;
        $freebees_price = $cartValue['freebees_price']??0;
        $total_cashback = $cartValue['total_cashback']??0;
        $max_applied_cashback = $cartValue['max_applied_cashback']??0;
        $freebees_product = $cart_data['freebees_product']??'';
        $subscription_id = $cartValue['subscription_id']??'';
        $total_discount = $cartValue['total_discount']??'';
        $total_mrp_discount = $cartValue['total_mrp_discount']??'';
        $nc_coins = $cart_data['nc_coins']??'';
        $applied_cashback = $cartValue['applied_cashback']??'';

        $delivery_data = $cart_data['delivery_data']??'';
        $expressSlot = $delivery_data['expressSlot']??'';
        $normalSlot = $delivery_data['normalSlot']??'';
$total_product_price = 0;
    @endphp

    <style>
        .cart-box {
            border-radius: 12px;
        }

        .highlight-box {
            font-size: 16px;
        }

        .btn-teal {
            background: #00a8a5;
        }

        .btn-teal:hover {
            background: #008f8c;
        }

        .quantity-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            border: 1px solid gray;
            border-radius: 3px;
        }


        .Right_sidr_section {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            gap: 20px;
        }

        .quantity-box span {
            font-size: 18px;
        }

        .cart_Images {
            border: 1px solid gray;
            height: 100px;
            width: 100px;
            border-radius: 5px;
        }

        .Product_Name_Price {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product_Detail_name h5 {
            font-size: 20px;
        }

        .product_Detail_name h6 {
            font-weight: 600;
        }

        .Product_image_section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .Product_Section_Main {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .Delete_button {
            display: none;
        }

        .desktop_main {
            display: flex;
            width: 100%;
            gap: 10px
        }

        .Cart_Card_section_right_side {
            height: 125px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            border: 1px solid rgb(110, 110, 110);
            border-radius: 10px;
            min-width: 250px;
            padding: 10px;
            flex-shrink: 0;
        }

        .Right_section_slider {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            scroll-behavior: smooth;
            width: 100%;
            white-space: nowrap;
        }

        .Right_section_slider::-webkit-scrollbar {
            height: 6px;
        }

        @media (max-width: 604px) {

            .Product_Section_Main {
                flex-wrap: wrap;
            }

            /* Align + - and Wishlist side-by-side */
            .Right_sidr_section {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                width: 100%;         /* full width */
                margin-top: 10px;
                gap: 10px;
            }

            /* Quantity box smaller for mobile */
            .quantity-box {
                width: 60%;
                padding: 6px 10px;
            }

            .product_Detail_name h5 {
                font-size: 15px !important;
            }

            .product_Detail_name h6 {
                font-size: 13px !important;
            }

            .cart_Images {
                height: 100px;
                width: 130px;
            }

            .Delete_button {
                display: block;
            }

            .Delete_desktop {
                display: none;
            }
        }

        /* Default (Desktop / Large Screens) */
        .Right_sidr_section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        /* Quantity box width stable across sizes */
        .quantity-box {
            min-width: 130px;
            justify-content: center;
        }

        /* -------------------------------------------- */
        /* MOBILE RESPONSIVE FIX (Corrected)            */
        /* -------------------------------------------- */
        @media (max-width: 604px) {

            /* Make quantity + wishlist side-by-side */
            .right-mobile-row {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                width: 100%;
                gap: 10px;
                margin-top: 12px;
            }

            /* Quantity box shrinks properly on mobile */
            .desktop_main {
                width: auto !important;
            }

            .quantity-box {
                padding: 6px 10px !important;
                min-width: 100%;
            }

            /* Wishlist button adjusts size */
            .btn-teal {
                padding: 6px 12px !important;
                white-space: nowrap;
            }
        }


    </style>

    @if(!empty($cart_products) && count($cart_products) > 0)
        <div class="col-md-8">
            @foreach ($cart_products as $cart_product)
                @php
                    $varients = $cart_product->varients ?? '';
                    $selectedVarient = isset($varients[0]) ? (object) $varients[0] : (object)[];
                    $prototal_price = (int)$selectedVarient->qty * $selectedVarient->selling_price;
                    $total_product_price += $prototal_price;
                    $images = $selectedVarient->images ??'';
                    $defaultImage = $images[0]['image'] ??url('public/assets/images/default.png');
                @endphp
                <div style="border:1px solid gray;padding:10px; border-radius: 10px;margin-top: 10px;">
                    <div class="Product_Section_Main">

                        <div class="Product_image_section">
                            <div class="cart_Images"><img src="{{ $defaultImage }}"
                                                          alt="{{ $cart_product->name ?? '' }}"></div>

                            <div class="product_Detail_name">
                                <h5 class="fw-bold" style="color:#008f8c;font-weight: 600;"
                                    onclick="window.location.href='{{ url('products/' . $cart_product->slug ?? '') }}'">  {{ $cart_product->name ?? '' }}</h5>
                                <h6 class="fw-bold" style="color: #898a8a;">{{ $selectedVarient->unit ?? '' }}</h6>

                                <div class="Product_Name_Price d-flex align-items-center gap-3">
                                    <h4 class="fw-bold text-dark mb-0" style="color: #575757;">   ₹ {{ $selectedVarient->selling_price ?? '' }}</h4>
                                    <del class="text-secondary" style="color: rgb(185, 185, 185);">₹ {{ $selectedVarient->mrp ?? '' }}</del>
                                    <span class="badge bg-success" style="background: #008f8c ;">{{$selectedVarient->discount_per?? 0}}% OFF</span>
                                </div>

                                <div class="d-flex gap-3 subscription-row mt-5">
                                    <div class="button-container w-50">
                                        <div class="nutrapass-circle">
                                            <img src="{{ url('public/assets/staricon.png') }}">
                                        </div>
                                        <div class="button-text">
                                            Get @ ₹ {{ $selectedVarient->subscription_price ?? 0 }}
                                        </div>
                                    </div>

                                    <div class="button-container1 w-50">
                                        <div class="nutrapass-circle1">
                                            <img src="{{ url('public/assets/staricon.png') }}">
                                        </div>
                                        <div class="button-text">
                                            Get {{ $selectedVarient->nc_cash ?? 0 }} Nc Cash
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="Right_sidr_section right-mobile-row">


                        <div class="desktop_main">
                                <div
                                    class="quantity-box d-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-pill">
                                    <a type="button" class="btn btn-sm btn-outline-primary rounded-circle"
                                            onclick="updateCart('{{ $cart_product->id }}','{{ $selectedVarient->id }}','minus')">
                                        −
                                    </a>
                                    <input type="hidden" name="quantity" id="cart_quantity{{ $selectedVarient->id }}"
                                           value="{{(isset($selectedVarient->qty) && $selectedVarient->qty) > 0 ? $selectedVarient->qty : 1}}">
                                    <span
                                        class="mx-3 fw-semibold">{{(isset($selectedVarient->qty) && $selectedVarient->qty) > 0 ? $selectedVarient->qty : 1}}</span>

                                    <a  type="button" class="btn btn-sm btn-outline-primary rounded-circle"
                                            onclick="updateCart('{{ $cart_product->id }}','{{ $selectedVarient->id }}','plus')">
                                        ＋
                                    </a>
                                </div>

                            </div>

                            <button style="color: white;" class="btn btn-teal text-white fw-bold py-2 rounded mt-2">
                                Move to Wishlist
                            </button>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

            <?php
            $user = Auth::user();
            $user_address = $user->addresses ?? '';
            $selectedAddress = \App\Models\UserAddress::where('id', $user->addressID)->first();
            $envia_data = [];
            if (!empty($selectedAddress)) {
                $envia_data = json_decode($selectedAddress->envia_data) ?? '';
            }

            ?>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body" style="
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
">
                    <span>Delivery to <strong>{{ $selectedAddress->pincode ?? '' }}, {{$envia_data->state->name??''}}</strong></span>
                    <a href="#" style="color: #00A8A8; text-decoration: none; font-weight: 600;" data-bs-toggle="modal"
                       data-bs-target="#addressModal">Change</a>
                </div>

            </div>


            <div class="border rounded p-3 shadow-sm mt-5" style="background: #fff;">
                <!-- NutraPass Box -->
                <div class="subscription-slider-container">
                    <div class="subscription-slider">
                        @foreach($subscription_plans_new as $key => $subscription_plan)
                            <div class="subscription-card">
                                <div class="subscription-box d-flex justify-content-between align-items-center gap-2">
                                    <small class="w-75">
                                        <div class="gold-card-premium">
                                            <strong>{{ $subscription_plan->duration }} Months Pass</strong>
                                        </div>


                                        <br>
                                        <div class="month_subs">
                                            Add for {{ $subscription_plan->duration }} months @
                                            ₹{{ $subscription_plan->price ?? 0 }} only
                                        </div>
                                    </small>

                                    <button type="button"
                                            class="btn btn-sm btn-warning subscription-btn action-btn"
                                            data-id="{{ $subscription_plan->id }}">
                                        Add
                                    </button>
                                </div>
                            </div>

                        @endforeach
                    </div>
                </div>


                <div class="bg-light-yellow p-3 rounded mb-3 d-flex justify-content-between align-items-center"
                     id="applyCashbackBox"
                     style="background-color: #FFF8E1;margin-top: 10px">
                    <div>
                        <small><strong style="color:#FFA726;">NC Cash</strong><br> Available Balance :
                            ₹{{$user->cashback_wallet??0}}<br> Max Apply : ₹{{$max_applied_cashback}} </small>
                    </div>
                    <button type="button" id="cashbackApplyBtn" class="btn btn-sm btn-warning">Apply</button>
                </div>

                <div class="freebies-slider-container">
                    <h4>Choose your Freebie</h4>
                    <div class="freebies-slider mt-5">
                        @foreach($freebees_product as $key => $freebee)
                            <div class="freebie-card">
                                <img src="{{ $freebee->image }}" class="freebie-img"/>
                                <div class="freebie-details">
                                    <strong>{{ $freebee->product_name ??'' }}</strong>
                                </div>
                                <button type="button" class="btn-freebie action-btn"
                                        data-id="{{ $freebee->id??'' }}">
                                    Add
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Promo Section -->
                <div class="border rounded p-3 mb-3 mt-5">
                    <h6 class="fw-bold mb-3">Bill Details</h6>

                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="coupon_code" id="coupon_codeval"
                               placeholder="Add Promo">
                        <button type="button" id="couponApplyBtn" class="btn btn-primary btn-sm">Apply</button>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span><strong>₹{{$total_product_price}}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Discount</span><strong>₹{{$total_discount}}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Coupon Discount </span><strong>₹{{$coupon_discount}}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Freebees Price</span><strong>₹{{$freebees_price}}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Delivery Fee</span><strong>₹{{ $delivery_charge }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subscription Amount</span><strong>₹{{$subscription_amount}}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>NC Cash</span><strong>₹{{$applied_cashback}}</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span><strong>Total</strong></span><strong>₹{{$total_price}}</strong>
                    </div>
                </div>

                <!-- Savings Banner -->
                <div class="bg-success text-white p-2 rounded text-center mb-3" style="font-size: 14px;">
                    🎉 You Save: ₹{{$total_mrp_discount}} | And Earn {{$nc_coins}} NC Cash
                </div>


                <div>
                    @if(!empty($expressSlot))
                        <div class="delivery-option" data-value="express">
                            <div>
                                <span class="delivery-title">2-Hour Xpress Delivery</span>
                                <span class="delivery-sub">(Prepaid only)</span>
                            </div>
                            <span
                                class="delivery-badge">{{!empty($expressSlot->delivery_charge)?$expressSlot->delivery_charge:"Free"}}</span>
                        </div>
                    @endif

                    <div class="delivery-option" data-value="normal">
                        <div>
                            <span class="delivery-title">Standard Delivery</span>
                        </div>
                        <span
                            class="delivery-badge">{{!empty($normalSlot->delivery_charge)?$normalSlot->delivery_charge:"Free"}}</span>
                    </div>
                </div>


                <!-- Payment Method -->
                <div class="payment-toggle" id="paymentToggle">
                    <span>💳 Select Payment Method</span>
                    <i class="fi-rs-angle-down"></i> <!-- Optional icon -->
                </div>
                <div class="payment-options" id="paymentSection">

                    <label class="payment-row">
                        <input type="radio" name="payment" value="online">
                        <div>
                            <div class="payment-title">Online</div>
                            <div class="payment-subtitle">Pay Online & save ₹25 on this order</div>
                        </div>
                    </label>

                    <label class="payment-row">
                        <input type="radio" name="payment" value="cod">
                        <div>
                            <div class="payment-title">Cash on Delivery (COD)</div>
                            <div class="payment-subtitle">COD has an extra ₹25 charge</div>
                        </div>
                    </label>
                </div>


                <!-- Checkout Button -->
                <!-- Checkout Button (STICKY for Desktop + Mobile) -->
                <div class="sticky-pay-btn">
                    <button class="btn btn-primary w-100 py-2" onclick="openRazorpay()">Proceed to Pay</button>
                </div>

            </div>
        </div>

    @else
        <h3>No Products Found</h3>
    @endif

</div>


<script>
    $("#paymentToggle").click(function () {
        $("#paymentSection").slideToggle(180);
    });

    $(document).on("change", "input[name='payment']", function () {
        $(".payment-row").removeClass("selected");
        $(this).closest(".payment-row").addClass("selected");
    });


    $(document).on("click", ".delivery-option", function () {
        $(".delivery-option").removeClass("selected");
        $(this).addClass("selected");

        let selectedValue = $(this).data("value");
        $("#delivery_type").val(selectedValue); // store value in hidden input

        if (selectedValue === "express") {
            // Hide COD and auto select Online
            $("input[name='payment'][value='cod']").closest(".payment-row").hide();
            $("input[name='payment'][value='online']").closest(".payment-row").show();
            $("input[name='payment'][value='online']").prop("checked", true).trigger("change");
        } else {
            // Show both Online + COD
            $(".payment-row").show();
        }
    });
    // Store selected payment method in hidden input
    $("input[name='payment']").change(function () {
        let selectedValue = $(this).val();
        $("#selected_payment").val(selectedValue);
    });


    $(document).off("click", ".subscription-btn").on("click", ".subscription-btn", function () {
        let selectedID = $(this).data("id");
        let subscriptionHidden = $("#subscription_id");

        if (subscriptionHidden.val() == selectedID) {
            subscriptionHidden.val("");
            $(this).removeClass("remove").text("Add");
        } else {
            subscriptionHidden.val(selectedID);
            $(".subscription-btn").removeClass("remove").text("Add");
            $(this).addClass("remove").text("Remove");
        }
        getCartHtml(); // refresh cart
    });


    // Prevent duplicate event binding
    $(document).off("click", "#couponApplyBtn").on("click", "#couponApplyBtn", async function () {

        let couponTextBox = $("#coupon_codeval");
        let couponHidden = $("#coupon_code");
        let couponBtn = $(this);

        // If REMOVE button clicked
        if (couponBtn.text().trim() === "Remove") {

            couponTextBox.prop("readonly", false).val("");
            couponHidden.val("");

            couponBtn.text("Apply")
                .removeClass("btn-danger")
                .addClass("btn-primary");

            await getCartHtml();
            return;
        }

        // APPLY button clicked
        let couponCode = couponTextBox.val().trim();
        if (couponCode === "") {
            return;
        }

        couponHidden.val(couponCode);

        let cartResponse = await getCartHtml();

        if (!cartResponse || parseInt(cartResponse.cart_data?.cartValue?.coupon_discount ?? 0) <= 0) {

            couponHidden.val("");
            couponTextBox.prop("readonly", false);

            couponBtn.text("Apply")
                .removeClass("btn-danger")
                .addClass("btn-primary");

            return;
        }

        // ✅ valid coupon
        couponTextBox.val(couponCode).prop("readonly", true);

        couponBtn.text("Remove")
            .removeClass("btn-primary")
            .addClass("btn-danger");
    });


    // Ensure click handler is attached only once
    $(document).off("click", ".btn-freebie").on("click", ".btn-freebie", function (e) {
        e.preventDefault();
        e.stopPropagation();

        console.log("hitttt----------------------------->");

        let selectedID = $(this).data("id");
        let freebeeHidden = $("#freebees_id");

        if (freebeeHidden.val() == selectedID) {
            freebeeHidden.val(""); // <-- missing earlier
            $(this).removeClass("remove").text("Add");
        } else {
            freebeeHidden.val(selectedID);
            $(".btn-freebie").removeClass("remove").text("Add");
            $(this).addClass("remove").text("Remove");
        }

        // getCartHtml();
    });

    // Always remove previous handler before binding
    $(document).off("click", "#cashbackApplyBtn").on("click", "#cashbackApplyBtn", function () {

        let cashbackHidden = $("#apply_cashback");
        let cashbackBtn = $(this);

        if (cashbackHidden.val() === "true") {

            cashbackHidden.val("false");
            cashbackBtn.text("Apply")
                .removeClass("btn-danger")
                .addClass("btn-warning");
        } else {

            cashbackHidden.val("true");
            cashbackBtn.text("Remove")
                .removeClass("btn-warning")
                .addClass("btn-danger");
        }

        getCartHtml(); // ✅ will call only once
    });


</script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    function openRazorpay() {
        let addressID = $("#selected_addressID").val();
        let deliveryType = $("#delivery_type").val();
        let paymentMethod = $("#selected_payment").val();

        // 🔍 Validation
        if (!addressID) {
            alert("Please select an address.");
            return;
        }

        if (!deliveryType) {
            alert("Please select a delivery option.");
            return;
        }

        if (!paymentMethod) {
            alert("Please select a payment method.");
            return;
        }


        var _token = '{{ csrf_token() }}';
        var total_price = '{{$total_price}}';
        $.ajax({
            url: "{{ url('place_order') }}",
            type: "POST",
            data: $('#cartSubmitForm').serialize(),
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                console.log(resp);
                if (resp.result === true) {

                    // ✅ If payment method is online (e.g. Razorpay)
                    if (paymentMethod === "ONLINE" || paymentMethod === "online") {
                        if (resp.online_payment && resp.online_payment.result === true) {
                            let rzpData = resp.online_payment;
                            var options = {
                                key: rzpData.key,  // Razorpay key from backend
                                currency: "INR",
                                order_id: rzpData.order_id,
                                name: "Your Store",
                                description: "Order Payment",
                                handler: function (response) {
                                    // ✅ Payment success callback - redirect
                                    window.location.href = "{{ url('my_orders') }}";
                                }
                            };

                            const rzp = new Razorpay(options);
                            rzp.open();
                        } else {
                            toastr.error("Unable to initialize payment gateway.");
                        }

                    } else {
                        // ✅ For COD or wallet payment
                        window.location.href = "{{ url('my_orders') }}";
                    }
                } else {
                    toastr.error(resp.message ?? "Something went wrong");
                }
                // var options = {
                //     key: resp.razopayKeys.key, // Enter the Key ID generated from the Dashboard
                //     "one_click_checkout": true,
                //     order_id: resp.orderData.id, // This is a sample Order ID. Pass the `id` obtained in the response of Step 1; mandatory
                //     "show_coupons": true, // default true; false if coupon widget should be hidden
                //     "callback_url": "",
                //     "redirect": "true",
                //     "prefill": { // We recommend using the prefill parameter to auto-fill customer's emails information especially their phone number
                //         "name": "Gaurav Kumar", // your customer's name
                //         "email": "gaurav.kumar@example.com",
                //         "contact": "9000090000", // Provide the customer's phone number for better conversion rates
                //         "coupon_code": "500OFF" // any valid coupon code that gets auto-applied once magic opens
                //     },
                // };
                //
                // const rzp = new Razorpay(options);
                // rzp.open();
            }
        });
    }
</script>


