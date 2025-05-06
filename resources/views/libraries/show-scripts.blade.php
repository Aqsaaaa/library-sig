<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
    crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
    crossorigin=""></script>
<script>
    var map = L.map('map').setView([{{ $library->latitude }}, {{ $library->longitude }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    var marker = L.marker([{{ $library->latitude }}, {{ $library->longitude }}]).addTo(map);
    marker.bindPopup('<b>{{ $library->name }}</b><br>{{ $library->address }}').openPopup();

    // Draw a more accurate polyline track from user location to library
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLon = position.coords.longitude;

            var latlngs = [
                [userLat, userLon],
                [{{ $library->latitude }}, {{ $library->longitude }}]
            ];
            var polyline = L.polyline(latlngs, {color: 'blue'}).addTo(map);

            // Zoom the map to the polyline
            map.fitBounds(polyline.getBounds());

            // Haversine distance calculation
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

            document.getElementById('distance').textContent = 'Distance from your location: ' + distance.toFixed(2) + ' km';
        }, function(error) {
            document.getElementById('distance').textContent = 'Unable to retrieve your location for distance calculation.';
        });
    } else {
        document.getElementById('distance').textContent = 'Geolocation is not supported by your browser.';
    }
</script>
