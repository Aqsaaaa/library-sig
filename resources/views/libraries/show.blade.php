<x-layout :title="'Library Details - ' . $library->name">
    <div class="container mx-auto space-x-6">
        <div class="flex-1">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-semibold mb-4">{{ $library->name }}</h1>
                    <p class="mb-2"><strong>Address:</strong> {{ $library->address }}</p>
                </div>
                <div class="w-96 p-4 border border-gray-300 rounded">
                    <h2 class="text-xl font-semibold mb-2">Route Details</h2>
                    <div id="route-info" class="text-sm text-gray-700">
                        Loading route information...
                    </div>
                </div>
            </div>
            
            <div id="map" style="height: 500px; width: 100%; border: 1px solid #ccc;" class="mb-4"></div>
            <p id="distance" class="mt-2"></p>
        </div>
        
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
        crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

    <script>
        var map = L.map('map').setView([{{ $library->latitude }}, {{ $library->longitude }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var marker = L.marker([{{ $library->latitude }}, {{ $library->longitude }}]).addTo(map);
        marker.bindPopup('<b>{{ $library->name }}</b><br>{{ $library->address }}').openPopup();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var userLat = position.coords.latitude;
                var userLon = position.coords.longitude;

                var latlngs = [
                    [userLat, userLon],
                    [{{ $library->latitude }}, {{ $library->longitude }}]
                ];
                var polyline = L.polyline(latlngs, {color: 'blue'}).addTo(map);

                var control = L.Routing.control({
                    waypoints: [
                        L.latLng(userLat, userLon),
                        L.latLng({{ $library->latitude }}, {{ $library->longitude }})
                    ],
                    lineOptions: {
                        styles: [{color: 'red', opacity: 0.8, weight: 5}]
                    },
                    createMarker: function() { return null; },
                    addWaypoints: false,
                    routeWhileDragging: false,
                    draggableWaypoints: false,
                    fitSelectedRoutes: true,
                    showAlternatives: false
                }).addTo(map);

                control.on('routesfound', function(e) {
                    var routes = e.routes;
                    var summary = routes[0].summary;
                    var distanceKm = (summary.totalDistance / 1000).toFixed(2);
                    var timeMin = Math.round(summary.totalTime / 60);
                    document.getElementById('route-info').innerHTML =
                    '<p><strong>Straight Lines (Blue):</strong> ' + distance.toFixed(2) + ' km</p>' +
                    '<p><strong>Route Lines (Red):</strong> ' + distanceKm + ' km</p>' +
                    '<p><strong>Estimated time:</strong> ' + timeMin + ' minutes</p>';
                });

                function toRad(x) {
                    return x * Math.PI / 180;
                }
                var R = 6371; // km
                var dLat = toRad({{ $library->latitude }} - userLat);
                var dLon = toRad({{ $library->longitude }} - userLon);
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(toRad(userLat)) * Math.cos(toRad({{ $library->latitude }})) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                var distance = R * c;
            }, function(error) {
                document.getElementById('distance').textContent = 'Unable to retrieve your location for distance calculation.';
                document.getElementById('route-info').textContent = 'Unable to retrieve your location for route calculation.';
            });
        } else {
            document.getElementById('distance').textContent = 'Geolocation is not supported by your browser.';
            document.getElementById('route-info').textContent = 'Geolocation is not supported by your browser.';
        }
    </script>
</x-layout>
