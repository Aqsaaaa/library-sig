<x-layout title="Dashboard">
    <div class="flex flex-col items-center justify-center space-y-8">
        <div class="w-full max-w-6xl bg-white rounded-lg shadow-md p-4 border border-gray-300 dark:border-gray-500">
            <h2 class="text-xl font-semibold mb-4">Library Locations Map</h2>
            <div id="map" class="w-full h-96 rounded"></div>
            <div class="mt-4 flex space-x-4">
                <button id="gpsToggleBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">GPS On</button>
                <button id="toggleLinesBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" data-active="false">Show Lines</button>
            </div>
            <div id="libraryList" class="mt-6 max-w-full overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-md mb-1">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">Library</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Distance (km)</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody id="libraryListBody" class="bg-white">
                        <tr><td colspan="4" class="text-center p-4">Waiting for GPS location...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
        integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
        crossorigin=""></script>
    <script>
        const map = L.map('map').setView([-25.363, 131.044], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            subdomains: ['a', 'b', 'c']
        }).addTo(map);

        const libraries = [
            @foreach ($libraries as $library)
                {
                    name: "{{ $library->name }}",
                    latitude: {{ $library->latitude }},
                    longitude: {{ $library->longitude }}
                },
            @endforeach
        ];

        // Add markers for libraries
        libraries.forEach(library => {
            L.marker([library.latitude, library.longitude]).addTo(map)
                .bindPopup(`<b>${library.name}</b>`);
        });

        let userMarker = null;
        let userLat = null;
        let userLng = null;
        let lines = [];
        let gpsEnabled = true;

        const gpsToggleBtn = document.getElementById('gpsToggleBtn');
        const toggleLinesBtn = document.getElementById('toggleLinesBtn');
        const libraryListBody = document.getElementById('libraryListBody');

        function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the earth in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const d = R * c; // Distance in km
            return d;
        }

        function updateLibraryList() {
            if (userLat === null || userLng === null) {
                libraryListBody.innerHTML = '<tr><td colspan="4" class="text-center p-4 mb-4">Waiting for GPS location...</td></tr>';
                return;
            }
            // Calculate distances and sort libraries by distance ascending
            libraries.forEach(library => {
                library.distance = getDistanceFromLatLonInKm(userLat, userLng, library.latitude, library.longitude);
            });
            libraries.sort((a, b) => a.distance - b.distance);

            let rows = '';
            for (let i = 0; i < libraries.length; i++) {
                const library = libraries[i];
                let distanceToNext = '';
                if (i < libraries.length - 1) {
                    distanceToNext = getDistanceFromLatLonInKm(
                        library.latitude, library.longitude,
                        libraries[i + 1].latitude, libraries[i + 1].longitude
                    ).toFixed(2);
                }
                rows += `<tr>
                    <td class="border border-gray-300 px-4 py-2">${library.name}</td>
                    <td class="border border-gray-300 px-4 py-2">${library.distance.toFixed(2)} Km</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button onclick="panToMarker(${i})" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 mr-2">Go to Marker</button>
                        <a href="https://www.google.com/maps/search/?api=1&query=${library.latitude},${library.longitude}" target="_blank" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">Open Maps</a>
                    </td>
                </tr>`;
            }
            libraryListBody.innerHTML = rows;
        }

        function drawLines() {
            clearLines();
            if (userLat === null || userLng === null) return;
            libraries.forEach(library => {
                const line = L.polyline([[userLat, userLng], [library.latitude, library.longitude]], {color: 'blue'}).addTo(map);
                lines.push(line);
            });
        }

        function clearLines() {
            lines.forEach(line => map.removeLayer(line));
            lines = [];
        }

        function onLocationFound(position) {
            userLat = position.coords.latitude;
            userLng = position.coords.longitude;

            if (userMarker) {
                userMarker.setLatLng([userLat, userLng]);
            } else {
                userMarker = L.marker([userLat, userLng], {icon: L.icon({
                    iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                })}).addTo(map).bindPopup('Your Location').openPopup();
            }

            map.setView([userLat, userLng], 12);
            updateLibraryList();
            if (toggleLinesBtn.dataset.active === 'true') {
                drawLines();
            }
        }

        function onLocationError(error) {
            console.error('Geolocation error:', error);
        }

        let watchId = null;

        function startGPS() {
            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(onLocationFound, onLocationError, {enableHighAccuracy: true});
                gpsEnabled = true;
                gpsToggleBtn.textContent = 'GPS On';
            } else {
                console.error('Geolocation is not supported by this browser.');
            }
        }

        function stopGPS() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            gpsEnabled = false;
            gpsToggleBtn.textContent = 'GPS Off';
        }

        gpsToggleBtn.addEventListener('click', () => {
            if (gpsEnabled) {
                stopGPS();
                if (userMarker) {
                    map.removeLayer(userMarker);
                    userMarker = null;
                }
                clearLines();
                libraryListBody.innerHTML = '<tr><td colspan="2" class="text-center p-4">GPS is off</td></tr>';
            } else {
                startGPS();
            }
        });

        toggleLinesBtn.addEventListener('click', () => {
            if (toggleLinesBtn.dataset.active === 'true') {
                clearLines();
                toggleLinesBtn.dataset.active = 'false';
                toggleLinesBtn.textContent = 'Show Lines';
            } else {
                drawLines();
                toggleLinesBtn.dataset.active = 'true';
                toggleLinesBtn.textContent = 'Hide Lines';
            }
        });

        startGPS();
    </script>
</x-layout>

