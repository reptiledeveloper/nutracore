@extends('home.layout')
@section('content')
@php

 @endphp
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href='' rel='nofollow'><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Stores

                </div>
            </div>
        </div>
        <div class="container mb-80 mt-50">
            <div class="row">
                <form action="" method="" id="store_form">
                    <input type="hidden" name="lat" value="{{$_GET['lat'] ??''}}" id="lat">
                    <input type="hidden" name="long" value="{{$_GET['long'] ??''}}" id="long">
                    <input type="text" class="form-control" value="{{$_GET['search_address'] ??''}}" name="search_address" id="search_address" placeholder="Search...">
                </form>




                <!-- Map Banner -->
                <div class="mb-4">
                    <div id="map" style="width: 100%; height: 420px;" class="rounded"></div>
                </div>
                <!-- Heading and City Filter -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h4 class="mb-0">NutraCore Store in India</h4>
                </div>

                <!-- Store Cards -->
                <div class="row" id="storeCards">
                    @foreach($sellers_list as $seller)
                        <div class="col-lg-4 col-md-6 mb-4 store-card"
                             data-lat="{{ $seller->latitude ?? 0 }}"
                             data-lng="{{ $seller->longitude ?? 0 }}">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="mb-1">{{$seller->name??''}}</h5>
                                    <p class="mb-1"><strong>Address:</strong> {{$seller->address??''}}</p>
                                    <p class="mb-1"><strong>Contact:</strong> {{$seller->phone??''}}</p>
                                    <p class="mb-1"><strong>Opening Time :</strong> {{$seller->open_time??''}} - {{$seller->close_time??''}}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>
        </div>
    </main>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCENCD7Uzd2YK0IJsUPgFI1gMNiHHPAuRA"></script>


    <script>
        let map;
        let marker;

        function initMap() {
            const defaultLocation = { lat: 17.449179626477967, lng: 78.30704459187352}; // Mumbai
            map = new google.maps.Map(document.getElementById("map"), {
                center: defaultLocation,
                zoom: 12,
            });

            // Attach click events after map is ready
            document.querySelectorAll('.store-card').forEach(card => {
                card.addEventListener('click', () => {
                    const lat = parseFloat(card.getAttribute('data-lat'));
                    const lng = parseFloat(card.getAttribute('data-lng'));

                    if (!lat || !lng) {
                        alert('Latitude or Longitude missing!');
                        return;
                    }

                    const position = { lat: lat, lng: lng };

                    // Remove previous marker
                    if (marker) marker.setMap(null);

                    // Add new marker
                    marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: card.querySelector('h5').innerText
                    });

                    // Center map on marker
                    map.setCenter(position);
                    map.setZoom(15);
                });
            });
        }

        // Load map when page is ready
        window.addEventListener('load', initMap);

    </script>


<script>
    var autocomplete;

    function initAutocomplete() {
        var input = document.getElementById('search_address');
        autocomplete = new google.maps.places.Autocomplete(input);

        var defaultLat = parseFloat(document.getElementById("lat").value) || 28.6139; // Delhi
        var defaultLng = parseFloat(document.getElementById("long").value) || 77.2090;
        var defaultLocation = { lat: defaultLat, lng: defaultLng };

        // Initialize map (default center)
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 12
        });

        // Set default marker
        marker = new google.maps.Marker({
            map: map,
            draggable: true,
            position: defaultLocation
        });

        // On place selection
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                alert("No details available for input: '" + place.name + "'");
                return;
            }

            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();

            // Set inputs
            document.getElementById("lat").value = lat;
            document.getElementById("long").value = lng;
            document.getElementById("search_address").value = place.formatted_address;

            // Move map & marker
            map.setCenter(place.geometry.location);
            map.setZoom(15);
            marker.setPosition(place.geometry.location);


            $('#store_form').submit();
            // Auto submit form (optional)
            // document.getElementById('store_form').submit();
        });

        // Update lat/lng when marker is dragged
        google.maps.event.addListener(marker, 'dragend', function (event) {
            var newLat = event.latLng.lat();
            var newLng = event.latLng.lng();

            document.getElementById("lat").value = newLat;
            document.getElementById("long").value = newLng;
        });
    }

    google.maps.event.addDomListener(window, 'load', initAutocomplete);
</script>


@endsection
