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

    @endphp



    @if(!empty($cart_products) && count($cart_products) > 0)
          <div class="col-lg-8">
            <div class="table-responsive shopping-summery">
                <table class="table table-wishlist">

                    <tbody>
                        <?php
                        $total_product_price = 0;


                        ?>
                    @foreach ($cart_products as $cart_product)
                        @php
                            $varients = $cart_product->varients ?? '';
                            $selectedVarient = isset($varients[0]) ? (object) $varients[0] : (object)[];
                            $prototal_price = (int)$selectedVarient->qty * $selectedVarient->selling_price;
                            $total_product_price += $prototal_price;
                            $images = $selectedVarient->images ??'';
                            $defaultImage = $images[0]['image'] ?? url('public/assets/images/default.png');
                        @endphp
                        <div class="cart-item">
                            <div class="cart-row">

                          <div class="Cart_new_section_main">
                              <!-- Product Image -->
                              <div class="cart-image">
                                  <img src="{{ $defaultImage }}" alt="{{ $cart_product->name ?? '' }}">
                              </div>

                              <!-- Product Details -->
                              <div class="cart-details">
                                  <h6 class="product-name" onclick="window.location.href='{{ url('products/' . $cart_product->slug ?? '') }}'">
                                      {{ $cart_product->name ?? '' }}
                                  </h6>
                                  <span class="variant-name">{{ $selectedVarient->unit ?? '' }}</span>
                                  <div class="rating">
                                      <div class="rating-bar"></div>
                                      <span class="rating-text">(4.0)</span>
                                  </div>

                                  <div class="cart-quantity">
                                      <div class="qty-control">
                                          <button onclick="updateCart('{{ $cart_product->id }}','{{ $selectedVarient->id }}','minus')">-</button>
                                          <input type="text" name="quantity" id="cart_quantity{{ $selectedVarient->id }}"
                                                 value="{{(isset($selectedVarient->qty) && $selectedVarient->qty) > 0 ? $selectedVarient->qty : 1}}">
                                          <button onclick="updateCart('{{ $cart_product->id }}','{{ $selectedVarient->id }}','plus')" style="margin-right: 10px">+</button>
                                      </div>
                                  </div>
                              </div>



                          </div>

                                <!-- Quantity Control -->
                              <div class="Name_Car_new">
                                    <div class="New_Mobile_view">
                                        <!-- Total Price -->
                                        <div class="cart-total">
                                           <del> ₹ {{ $prototal_price }}</del>
                                        </div>
                                        <div class="cart-price" style="font-size: 20px;">
                                            ₹ {{ $selectedVarient->selling_price ?? '' }}
                                        </div>

                                        <!-- Remove Action -->
                                        <div class="cart-remove">
                                            <a onclick="DeleteCart('{{ $cart_product->id }}','{{ $selectedVarient->id}}')"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </div>


                              </div>

                            </div>

                            <!-- Optional Delivery Info (Mobile View) -->
                            <div class="cart-delivery">
                                <span>🚚 Delivery by 10 PM Tomorrow</span>
                            </div>
                        </div>

                    @endforeach

                    </tbody>
                </table>
            </div>

        </div>

            <?php
            $user = Auth::user();
            $user_address = $user->addresses ?? '';
            $selectedAddress = \App\Models\UserAddress::where('id', $user->addressID)->first();
            $envia_data = [];
            if(!empty($selectedAddress)){
                $envia_data = json_decode($selectedAddress->envia_data)??'';
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
                    <span>Delivery to <strong >{{ $selectedAddress->pincode ?? '' }}, {{$envia_data->state->name??''}}</strong></span>
                    <a href="#" style="color: #00A8A8; text-decoration: none; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#addressModal">Change</a>
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
                        <small><strong style="color:#FFA726;">NC Cash</strong><br> Available Balance : ₹{{$user->cashback_wallet??0}}<br> Max Apply : ₹{{$max_applied_cashback}} </small>
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


    $(document).on("click", ".delivery-option", function() {
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


