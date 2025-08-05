@extends('home.layout')
@section('content')

    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='index.html' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Stores

                </div>
            </div>
        </div>
        <div class="container mb-80 mt-50">
            <div class="row">

                <!-- Map Banner -->
                <div class="mb-4">
                    <img src="{{ url('public/assets/images/map.png') }}" class="img-fluid rounded" alt="Map Banner"
                        style="width: 100%; max-height: 420px; object-fit: cover;">
                </div>

                <!-- Heading and City Filter -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h4 class="mb-0">NutraCore Store in India</h4>
                    <select class="form-select w-auto" id="cityFilter">
                        <option value="all">All City</option>
                        <option value="mumbai">Mumbai</option>
                        <option value="delhi">Delhi</option>
                        <option value="chennai">Chennai</option>
                        <option value="kolkata">Kolkata</option>
                        <option value="hyderabad">Hyderabad</option>
                        <option value="pune">Pune</option>
                    </select>
                </div>

                <!-- Store Cards -->
                <div class="row" id="storeCards">
                    <!-- Store: Mumbai -->
                    <div class="col-lg-4 col-md-6 mb-4 store-card" data-city="mumbai">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">Mumbai</h5>
                                    <span class="badge bg-success">Nearest</span>
                                </div>
                                <p class="mb-1"><strong>Store Name:</strong> NC Wellness</p>
                                <p class="mb-1"><strong>Address:</strong> Andheri West, Mumbai, MH</p>
                                <p class="mb-1"><strong>Contact:</strong> 99999 11111</p>
                            </div>
                            <img src="{{ url('public/assets/images/stores.png') }}" class="card-img-bottom"
                                style="height: 160px; object-fit: cover;" alt="Mumbai Store">
                        </div>
                    </div>

                    <!-- Store: Delhi -->
                    <div class="col-lg-4 col-md-6 mb-4 store-card" data-city="delhi">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">Delhi</h5>
                                    <span class="badge bg-success">Nearest</span>
                                </div>
                                <p class="mb-1"><strong>Store Name:</strong> NC Center</p>
                                <p class="mb-1"><strong>Address:</strong> Connaught Place, Delhi</p>
                                <p class="mb-1"><strong>Contact:</strong> 88888 22222</p>
                            </div>
                            <img src="{{ url('public/assets/images/stores.png') }}" class="card-img-bottom"
                                style="height: 160px; object-fit: cover;" alt="Delhi Store">
                        </div>
                    </div>

                    <!-- Store: Chennai -->
                    <div class="col-lg-4 col-md-6 mb-4 store-card" data-city="chennai">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">Chennai</h5>
                                    <span class="badge bg-success">Nearest</span>
                                </div>
                                <p class="mb-1"><strong>Store Name:</strong> NC Health Spot</p>
                                <p class="mb-1"><strong>Address:</strong> T Nagar, Chennai</p>
                                <p class="mb-1"><strong>Contact:</strong> 77777 33333</p>
                            </div>
                            <img src="{{ url('public/assets/images/stores.png') }}" class="card-img-bottom"
                                style="height: 160px; object-fit: cover;" alt="Chennai Store">
                        </div>
                    </div>

                    <!-- Store: Kolkata -->
                    <div class="col-lg-4 col-md-6 mb-4 store-card" data-city="kolkata">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">Kolkata</h5>
                                    <span class="badge bg-success">Nearest</span>
                                </div>
                                <p class="mb-1"><strong>Store Name:</strong> NC Shop</p>
                                <p class="mb-1"><strong>Address:</strong> Salt Lake, Kolkata</p>
                                <p class="mb-1"><strong>Contact:</strong> 66666 44444</p>
                            </div>
                            <img src="{{ url('public/assets/images/stores.png') }}" class="card-img-bottom"
                                style="height: 160px; object-fit: cover;" alt="Kolkata Store">
                        </div>
                    </div>

                    <!-- Store: Hyderabad -->
                    <div class="col-lg-4 col-md-6 mb-4 store-card" data-city="hyderabad">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">Hyderabad</h5>
                                    <span class="badge bg-success">Nearest</span>
                                </div>
                                <p class="mb-1"><strong>Store Name:</strong> NC Zone</p>
                                <p class="mb-1"><strong>Address:</strong> Banjara Hills, Hyderabad</p>
                                <p class="mb-1"><strong>Contact:</strong> 55555 66666</p>
                            </div>
                            <img src="{{ url('public/assets/images/stores.png') }}" class="card-img-bottom"
                                style="height: 160px; object-fit: cover;" alt="Hyderabad Store">
                        </div>
                    </div>

                    <!-- Store: Pune -->
                    <div class="col-lg-4 col-md-6 mb-4 store-card" data-city="pune">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1">Pune</h5>
                                    <span class="badge bg-success">Nearest</span>
                                </div>
                                <p class="mb-1"><strong>Store Name:</strong> NC Point</p>
                                <p class="mb-1"><strong>Address:</strong> FC Road, Pune</p>
                                <p class="mb-1"><strong>Contact:</strong> 44444 77777</p>
                            </div>
                            <img src="{{ url('public/assets/images/stores.png') }}" class="card-img-bottom"
                                style="height: 160px; object-fit: cover;" alt="Pune Store">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>


    <!-- JS Filter -->
    <script>
        document.getElementById('cityFilter').addEventListener('change', function () {
            const selected = this.value;
            const cards = document.querySelectorAll('.store-card');
            cards.forEach(card => {
                const city = card.getAttribute('data-city');
                card.style.display = (selected === 'all' || selected === city) ? 'block' : 'none';
            });
        });
    </script>

@endsection