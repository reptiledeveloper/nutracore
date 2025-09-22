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
    @if(!empty($cart_products) && count($cart_products) > 0)
        <div class="col-lg-8">
            <div class="table-responsive shopping-summery">
                <table class="table table-wishlist">
                    <thead>
                    <tr class="main-heading">
                        <th class="custome-checkbox start pl-30">

                        </th>
                        <th scope="col" colspan="2">Product</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col" class="end">Remove</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_product_price = 0;
                        $delivery_charge = 0;
                        $total_price = 0;

                        ?>
                    @foreach ($cart_products as $cart_product)
                        @php
                            $varients = $cart_product->varients ?? '';
                            $selectedVarient = isset($varients[0]) ? (object) $varients[0] : (object)[];
                            $prototal_price = (int)$selectedVarient->qty * $selectedVarient->selling_price;
                            $total_product_price += $prototal_price;
                            $images = $selectedVarient->images ??'';
$defaultImage = $images[0]['image'] ??url('public/assets/images/default.png');
                        @endphp
                        <tr class="pt-30">
                            <td class="custome-checkbox pl-30">

                            </td>
                            <td class="image product-thumbnail pt-40"><img src="{{ $defaultImage }}"
                                                                           alt="#"></td>
                            <td class="product-des product-name">
                                <h6 class="mb-5"><a class='product-name mb-10 text-heading'
                                                    onclick="window.location.href='{{ url('products/' . $cart_product->slug ?? '') }}'">{{$cart_product->name ?? ''}}</a>
                                </h6>
                                <span id="varient_name">{{ $selectedVarient->unit ?? '' }}</span>
                                <div class="product-rate-cover">
                                    <div class="product-rate d-inline-block">
                                        <div class="product-rating" style="width:90%">
                                        </div>
                                    </div>
                                    <span class="font-small ml-5 text-muted"> (4.0)</span>
                                </div>
                            </td>
                            <td class="price" data-title="Price">
                                <h4 class="text-body">₹ {{ $selectedVarient->selling_price ?? '' }}</h4>
                            </td>
                            <td class="text-center detail-info" data-title="Stock">
                                <div class="detail-extralink mr-15">
                                    <div class="detail-qty border radius">
                                        <a onclick="updateCart('{{ $cart_product->id }}','{{ $selectedVarient->id }}','minus')"
                                           class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                        <input type="text" name="quantity" id="cart_quantity{{ $selectedVarient->id }}"
                                               class="qty-val"
                                               value="{{(isset($selectedVarient->qty) && $selectedVarient->qty) > 0 ? $selectedVarient->qty : 1}}"
                                               min="1">
                                        <a onclick="updateCart('{{ $cart_product->id }}','{{ $selectedVarient->id }}','plus')"
                                           class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                                    </div>
                                </div>
                            </td>
                            <td class="price" data-title="Price">
                                <h4 class="text-brand">₹ {{ $prototal_price }} </h4>
                            </td>
                            <td class="action text-center" data-title="Remove"><a
                                    onclick="DeleteCart('{{$cart_product->id}}','{{$selectedVarient->id}}')"
                                    class="text-body"><i
                                        class="fi-rs-trash"></i></a></td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

        </div>

            <?php


            $total_price = $delivery_charge + $total_product_price;
            $user = Auth::user();
            $user_address = $user->addresses ?? '';
            $selectedAddress = \App\Models\UserAddress::where('id', $user->addressID)->first();
            ?>
        <div class="col-lg-4">
            <div class="border rounded p-3 shadow-sm" style="background: #fff;">
                <h6>Choose Address</h6>
                <!-- Address Section -->
                <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-3 mt-3"
                     style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#addressModal">
                    <div id="selectedAddress">
                        <div class="fw-bold">{{ $selectedAddress->address_type ?? "Others" }}</div>
                        <small>{{ $selectedAddress->flat_no ?? '' }}, {{ $selectedAddress->building_name ?? '' }}
                            , {{ $selectedAddress->landmark ?? '' }} - {{ $selectedAddress->pincode ?? '' }}</small><br>
                        <small>{{ $selectedAddress->location ?? '' }}</small>

                    </div>
                    <i class="fi-rs-angle-right fs-5 text-muted"></i> <!-- vertically centered -->
                </div>
                <!-- NutraPass Box -->
                <div class="bg-light-yellow p-3 rounded mb-3 d-flex justify-content-between align-items-center"
                     style="background-color: #FFF8E1;">
                    <div>
                        <small>Save UPTO ₹450 with <strong style="color:#FFA726;">NutraPass</strong><br>Add for 3
                            months @ ₹149 only</small>
                    </div>
                    <button class="btn btn-sm btn-warning">Add</button>
                </div>

                <!-- Promo Section -->
                <div class="border rounded p-3 mb-3">
                    <h6 class="fw-bold mb-3">Bill Details</h6>

                    <div class="input-group mb-2">
                        <input type="text" class="form-control" placeholder="Add Promo">
                        <button class="btn btn-primary btn-sm">Apply</button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Get 20% off (200) on this product. Pay with card</small>

                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span><strong>₹{{$total_product_price}}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Delivery Fee</span><strong>₹{{ $delivery_charge }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax & Other Fees</span><strong>₹0</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span><strong>Total</strong></span><strong>₹{{$total_price}}</strong>
                    </div>
                </div>

                <!-- Savings Banner -->
                <div class="bg-success text-white p-2 rounded text-center mb-3" style="font-size: 14px;">
                    🎉 You Save: ₹900 | And Earn 420 NC Cash
                </div>

                <!-- Payment Method -->
                <div class="border rounded p-3 mb-3 d-flex justify-content-between align-items-center">
                    <span>💳 View Payment Method</span>
                    <i class="fi-rs-angle-right"></i>
                </div>

                <!-- Checkout Button -->
                <button class="btn btn-primary w-100 py-2" onclick="openRazorpay()">Proceed to Pay</button>

            </div>
        </div>

    @else
        <h3>No Products Found</h3>
    @endif

</div>


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    function openRazorpay() {
        var _token = '{{ csrf_token() }}';
        var total_price = '{{$total_price}}';
        $.ajax({
            url: "{{ url('createRazorpayOrder') }}",
            type: "POST",
            data: {total_price: total_price},
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': _token},
            cache: false,
            success: function (resp) {
                console.log(resp);
                var options = {
                    key: resp.razopayKeys.key, // Enter the Key ID generated from the Dashboard
                    "one_click_checkout": true,
                    order_id: resp.orderData.id, // This is a sample Order ID. Pass the `id` obtained in the response of Step 1; mandatory
                    "show_coupons": true, // default true; false if coupon widget should be hidden
                    "callback_url": "",
                    "redirect": "true",
                    "prefill": { // We recommend using the prefill parameter to auto-fill customer's emails information especially their phone number
                        "name": "Gaurav Kumar", // your customer's name
                        "email": "gaurav.kumar@example.com",
                        "contact": "9000090000", // Provide the customer's phone number for better conversion rates
                        "coupon_code": "500OFF" // any valid coupon code that gets auto-applied once magic opens
                    },
                };

                const rzp = new Razorpay(options);
                rzp.open();
            }
        });
    }
</script>
