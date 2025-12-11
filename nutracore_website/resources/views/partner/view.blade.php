@extends('home.layout')
@section('content')
    <?php

    use App\Helpers\CustomHelper;

    $name = $partner_data->full_name ?? '';
    $initials = collect(explode(' ', $name))
        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
        ->join('');

    ?>

    <style>
        .status-card {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .title {
            font-size: 22px;
            font-weight: bold;
        }


        .badge {
            padding: 8px 16px;
            border-radius: 30px;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
        }


        .pending {
            background: #f5a623;
        }

        .approved {
            background: #00a8a8;
        }

        .rejected {
            background: #dc3545;
        }

        .ban {
            background: #6c757d;
        }

        :root {
            --bg: #ffffff;
            --card-bg: #ffffff;
            --accent1: #00a8a8;
            --accent2: #b7e8e8;
            --muted: #555;
            --border: #e5e5e5;
            --card-radius: 14px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        body {
            background: #ffffff !important;
        }

        .wrap {
            width: 100%;
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 24px;
            padding: 20px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--card-radius);
            padding: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .profile {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .avatar {
            width: 86px;
            height: 86px;
            border-radius: 18px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--accent1), var(--accent2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 28px;
            color: #fff;
        }

        .name {
            font-size: 20px;
            font-weight: 700;
        }

        .role {
            font-size: 13px;
            color: var(--muted);
        }

        .field {
            background: #fafafa;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            border: 1px solid var(--border);
        }

        .field small {
            display: block;
            color: #888;
            font-size: 11px;
        }

        .copy-btn {
            background: #fff;
            border: 1px solid var(--border);
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-primary {
            padding: 10px 14px;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
            color: #fff;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .btn-ghost {
            padding: 10px 14px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid var(--border);
            color: #555;
            cursor: pointer;
        }

        .analytics-title {
            font-weight: 700;
        }

        .small-muted {
            color: #777;
            font-size: 13px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .metric {
            background: #fff;
            border: 1px solid var(--border);
            padding: 14px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        @media (max-width: 880px) {
            .wrap {
                grid-template-columns:1fr;
            }
        }

        @media (max-width: 520px) {
            .metrics {
                grid-template-columns:1fr;
            }

            .avatar {
                width: 72px;
                height: 72px;
            }
        }
    </style>
    <style>
        .pager {
            padding-left: 0;
            margin: 20px 0;
            text-align: center;
            list-style: none;
        }

        .pager li {
            display: inline;
        }

        .pager li > a,
        .pager li > span {
            display: inline-block;
            padding: 5px 14px;
            background-color: #fff;
            border: 1px solid #00a8a8;
            border-radius: 15px;
            color: black;
        }

        .pager li > span {
            background: #00a8a8;
        }

        .cke_notification_warning {
            /*display: none;*/
        }


    </style>
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span>Partner Dashboard
                </div>
            </div>
        </div>
        <div class="page-content">
            <div class="container">
                @if($partner_data->status == 'Pending Review')
                    <div class="status-card">
                        <div class="title">Pending Review</div>
                        <div class="badge pending">Pending</div>
                    </div>
                @elseif($partner_data->status == 'Rejected')
                    <div class="status-card">
                        <div class="title">Rejected</div>
                        <div class="badge rejected">Rejected</div>
                    </div>
                @elseif($partner_data->status == 'Ban')
                    <div class="status-card">
                        <div class="title">Ban</div>
                        <div class="badge ban">Banned</div>
                    </div>
                @else
                    <div class="wrap">

                        <!-- PROFILE -->
                        <aside class="card profile" aria-label="Profile card">
                            <div style="justify-content:space-between;align-items:center">
                                <div style="display:flex;gap:12px;align-items:center">
                                    <div class="avatar" id="avatar">{{$initials}}</div>
                                    <div>
                                        <div class="name" id="userName">{{$partner_data->full_name??''}}</div>
                                        <div class="actions mt-2">
                                            <button class="btn-primary" id="promoBtn">{{$tier->title??''}}</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card p-3 mb-3" style="background:#fff; border-radius:12px;font-size: 10px">
                                <h4>Tier Progress</h4>
                                <p>Current Earnings: ₹ {{ number_format($totalEarnings, 2) }}</p>

                                @if($currentTier)
                                    <p>Current Tier: <strong>{{ $currentTier->title }}</strong> (Cashback: {{ $currentTier->cashback }}%)</p>
                                @endif

                                @if($nextTier)
                                    <p>Remaining to reach <strong>{{ $nextTier->title }}</strong>: ₹ {{ number_format($remaining, 2) }}</p>
                                @else
                                    <p>You are at the highest tier! 🎉</p>
                                @endif

                                <!-- Tier Progress Bar -->
                                <div style="display:flex; gap:8px; margin-top:10px;">
                                    @foreach($tiers as $tier)
                                        @php
                                            $width = 0; // default width

                                            if(isset($tier->to_amount) && isset($tier->from_amount)){
                                                // If from_amount is 0 and totalEarnings >= to_amount
                                                if($totalEarnings >= $tier->to_amount){
                                                    $width = 100;
                                                }
                                                // If totalEarnings <= from_amount
                                                elseif($totalEarnings <= $tier->from_amount){
                                                    $width = 0;
                                                }
                                                // Normal calculation
                                                else {
                                                    // Prevent division by zero if from_amount == to_amount
                                                    $range = $tier->to_amount - $tier->from_amount;
                                                    $width = $range > 0 ? (($totalEarnings - $tier->from_amount) / $range) * 100 : 100;
                                                }
                                            }
                                        @endphp
                                        <div style="flex:1; background:#eee; border-radius:6px; overflow:hidden; position:relative; height:24px;">
                                            <div style="width: {{ $width }}%; background: linear-gradient(90deg, #00a8a8, #f4ae53); height:100%;"></div>
                                            <span style="position:absolute; left:50%; top:50%; transform:translate(-50%, -50%); font-size:12px; font-weight:600; color:#111;">
                    {{ $tier->title }}
                </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="profile-details">
                                <div class="field" title="Email">
                                    <div>
                                        <small>Email</small>
                                        <div id="userEmail">{{$partner_data->email??''}}</div>
                                    </div>
                                </div>

                                <div class="field mt-2" title="Phone">
                                    <div>
                                        <small>Phone</small>
                                        <div id="userPhone">+91 {{$partner_data->mobile_number??''}}</div>
                                    </div>
                                </div>

                                <div class="field coupon mt-2" title="Coupon code">
                                    <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                        <div>
                                            <small style="display:block;line-height:1;">Coupon Code</small>
                                            <span id="couponCode">{{$partner_data->coupon_code ?? ''}}</span>
                                        </div>

                                        <button class="copy-btn" id="copyCoupon">Copy</button>
                                    </div>
                                </div>
                                <div class="field coupon mt-2" title="Coupon code">
                                    <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                                        <div>
                                            <small style="display:block;line-height:1;">Wallet</small>
                                            <span id="couponCode">₹ {{$partner_data->wallet ?? ''}}</span>
                                        </div>

                                        <button class="copy-btn" >Withdraw</button>
                                    </div>
                                </div>
                                <div class="field mt-2" title="Phone">
                                    <div>
                                        <small>Uses Tips</small>
                                        <div id="userPhone"></div>
                                    </div>
                                </div>
                                <div class="actions mt-2">
                                    <button class="btn-primary" id="promoBtn">Share Coupon Code</button>
                                </div>
                            </div>

                            <div
                                style="margin-top:auto;display:flex;gap:10px;justify-content:space-between;align-items:center">
                                <div class="small-muted">Member since
                                    <strong>{{date('M Y',strtotime($partner_data->created_at))}}</strong></div>
                            </div>
                        </aside>

                        <!-- ANALYTICS -->
                        <section class="card analytics" aria-label="Analytics">
                            <div class="analytics-top d-flex justify-content-between">
                                <div>
                                    <div class="analytics-title">Overview</div>
                                    <div class="small-muted">Snapshot of latest activity</div>
                                </div>
                                <div class="dashboard-cta">
                                    <button class="btn-ghost" id="viewDashboard">View Withdrawal Status</button>
                                </div>
                            </div>

                            <div class="metrics mt-3" id="metrics">
                                <div class="metric">
                                    <h3 id="totalOrders">{{$currentMonthOrders}}</h3>
                                    <p>Current Month Orders</p>
                                </div>
                                <div class="metric">
                                    <h3 id="totalEarnings">₹ {{$currentMonthData??0}}</h3>
                                    <p>Current Month Earnings</p>
                                </div>
                                <div class="metric">
                                    <h3 id="totalPayouts">₹ {{$lastMonthEarnings??0}}</h3>
                                    <p>Last Month Earnings</p>
                                </div>
                                <div class="metric">
                                    <h3 id="totalPayouts">₹ {{$lifetimeEarnings??0}}</h3>
                                    <p>Lifetime Earnings</p>
                                </div>
                            </div>

                            <div class="mt-3">
                                <h5>Orders</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped mb-0" >
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User Details</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Commission</th>
                                            <th>Settlement Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($orders as $order)
                                            @php
                                                $final_total = (int)$order->total_amount + (int)$order->delivery_charges - (int)$order->applied_cashback - (int)$order->flatDiscountValue;
                                                $partner_commissions = DB::table('partner_commissions')->where('order_id', $order->id)->first();
                                                $user = $order->customer_name ? null : \App\Helpers\CustomHelper::getUserDetails($order->userID);
                                            @endphp
                                            <tr>
                                                <td># {{ $order->unique_id ?? '' }}</td>
                                                <td>
                                                    @if(!empty($order->customer_name))
                                                        <strong>{{ $order->customer_name }}</strong><br>{{ $order->contact_no ?? '' }}
                                                    @else
                                                        <strong>{{ $user->name ?? '' }}</strong><br>{{ $user->phone ?? '' }}
                                                    @endif
                                                </td>
                                                <td>₹ {{ $final_total }}</td>
                                                <td>{!! \App\Helpers\CustomHelper::getOrderStatus($order->id) !!}</td>
                                                <td>{{ date('d M Y', strtotime($order->created_at)) }}</td>
                                                <td>({{ $partner_commissions->commission_percent ?? 0 }}%) - ₹{{ $partner_commissions->commission ?? 0 }}</td>
                                                <td>{{ $partner_commissions->is_setteled == 1 ? 'Settled' : 'Not Settled' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    {{ $orders->appends(request()->input())->links('pagination') }}
                                </div>
                            </div>


                        </section>


                    </div>
                @endif


            </div>
        </div>
    </main>
    <script>
        // Simple interactivity: copy coupon, regenerate, dashboard click
        const couponEl = document.getElementById('couponCode');
        const copyBtn = document.getElementById('copyCoupon');
        const regenBtn = document.getElementById('regenCoupon');
        const viewDashboard = document.getElementById('viewDashboard');


        copyBtn.addEventListener('click', async () => {
            const text = couponEl.textContent.trim();
            try {
                await navigator.clipboard.writeText(text);
                copyBtn.textContent = 'Copied!';
                setTimeout(() => copyBtn.textContent = 'Copy', 1600);
            } catch (e) {
// fallback
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
                copyBtn.textContent = 'Copied!';
                setTimeout(() => copyBtn.textContent = 'Copy', 1600);
            }
        });


        regenBtn.addEventListener('click', () => {
            const newCode = 'CP' + Math.random().toString(36).substring(2, 8).toUpperCase();
            couponEl.textContent = newCode;
            regenBtn.textContent = 'Done';
            setTimeout(() => regenBtn.textContent = 'Regenerate', 1200);
        });


        viewDashboard.addEventListener('click', () => {
// In real app this would route to dashboard
            alert('Opening dashboard... (implement navigation)');
        });


        // small responsive tweak: initials from name
        const nameEl = document.getElementById('userName');
        const avatar = document.getElementById('avatar');

        function setInitials() {
            const name = nameEl.textContent.trim();
            const parts = name.split(' ');
            const initials = (parts[0]?.[0] || '') + (parts[1]?.[0] || '');
            avatar.textContent = initials.toUpperCase();
        }

        setInitials();


        // Example: animate numbers (basic)
        function animateNumber(id, start, end, duration) {
            const el = document.getElementById(id);
            const range = end - start;
            let startTime = null;

            function step(ts) {
                if (!startTime) startTime = ts;
                const progress = Math.min((ts - startTime) / duration, 1);
                const value = Math.floor(start + range * progress);
                el.textContent = value.toLocaleString();
                if (progress < 1) window.requestAnimationFrame(step);
            }

            window.requestAnimationFrame(step);
        }


        // Use sample values (in a real app fetch from API)
        setTimeout(() => {
            animateNumber('totalOrders', 0, 1256, 900);
            document.getElementById('totalEarnings').textContent = '₹ ' + (189420).toLocaleString();
            document.getElementById('totalPayouts').textContent = '₹ ' + (172000).toLocaleString();
        }, 300);


    </script>
@endsection
