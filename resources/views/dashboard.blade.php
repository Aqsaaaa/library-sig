<x-layout title="Dashboard">
    <div class="flex flex-col items-center justify-center space-y-8">
        <div class="w-full max-w-6xl bg-white rounded-lg shadow-md p-4 border border-gray-300 dark:border-gray-500">
            <h2 class="text-xl font-semibold mb-4">Library Locations Map</h2>
            <div id="map" class="w-full h-96 rounded"></div>
            <div class="mt-4 flex space-x-4">
                <button id="gpsToggleBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">GPS On</button>
                <button id="toggleLinesBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" data-active="false">Show All Lines</button>
            </div>
            <div id="libraryList" class="mt-6 max-w-full overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-md mb-1">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">Image</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Name Of Library</th>
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
                    id: {{ $library->id }},
                    image: '<img src="@if($library->image){{ asset('storage/' . $library->image) }}@else{{ asset('icon/library-building-icon.svg') }}@endif" alt="Library Image" class="h-20 mx-auto mb-2" />',
                    name: "{{ $library->name }}",
                    latitude: {{ $library->latitude }},
                    longitude: {{ $library->longitude }}
                },
            @endforeach
            ]
        // Store markers in a map for panning by library id
        const libraryMarkersMap = new Map();
        libraries.forEach(library => {
            const marker = L.marker([library.latitude, library.longitude]).addTo(map)
                .bindPopup(`<b>${library.name}</b>`);
            libraryMarkersMap.set(library.id, marker);
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
                    <td class="border border-gray-300 px-4 py-2">${library.image}</td>
                    <td class="border border-gray-300 px-4 py-2">${library.name}</td>
                    <td class="border border-gray-300 px-4 py-2">${library.distance.toFixed(2)} Km</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button onclick="panToMarker(${library.id})" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 mr-2">Go to Marker</button>
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

        function panToMarker(libraryId) {
            const marker = libraryMarkersMap.get(libraryId);
            if (marker) {
                map.setView(marker.getLatLng(), 15);
                marker.openPopup();
            }
        }

        toggleLinesBtn.addEventListener('click', () => {
            if (toggleLinesBtn.dataset.active === 'true') {
                clearLines();
                toggleLinesBtn.dataset.active = 'false';
                toggleLinesBtn.textContent = 'Show All Lines';
            } else {
                drawLines();
                toggleLinesBtn.dataset.active = 'true';
                toggleLinesBtn.textContent = 'Hide All Lines';
            }
        });

        startGPS();
    </script>

    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
            gap: 0.5rem;
        }
        .pagination button {
            padding: 0.25rem 0.5rem;
            border: 1px solid #ccc;
            background-color: white;
            cursor: pointer;
            border-radius: 0.25rem;
        }
        .pagination button.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .pagination button:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
    </style>

    <script>
        // Generic client-side pagination for tables
        let paginationInstances = {};
        function paginateTable(tableId, rowsPerPage = 5) {
            const table = document.getElementById(tableId);
            if (!table) return;

            if (paginationInstances[tableId]) {
                const oldPagination = paginationInstances[tableId].paginationElement;
                if (oldPagination && oldPagination.parentNode) {
                    oldPagination.parentNode.removeChild(oldPagination);
                }
            }

            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const totalRows = rows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);

            let currentPage = 1;

            function renderPage(page) {
                currentPage = page;
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });

                renderPaginationControls();
            }

            function renderPaginationControls() {
                let pagination = table.nextElementSibling;
                if (!pagination || !pagination.classList.contains('pagination')) {
                    pagination = document.createElement('div');
                    pagination.className = 'pagination';
                    table.parentNode.insertBefore(pagination, table.nextSibling);
                }
                pagination.innerHTML = '';

                const prevBtn = document.createElement('button');
                prevBtn.textContent = 'Prev';
                prevBtn.disabled = currentPage === 1;
                prevBtn.addEventListener('click', () => renderPage(currentPage - 1));
                pagination.appendChild(prevBtn);

                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    if (i === currentPage) btn.classList.add('active');
                    btn.addEventListener('click', () => renderPage(i));
                    pagination.appendChild(btn);
                }

                const nextBtn = document.createElement('button');
                nextBtn.textContent = 'Next';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.addEventListener('click', () => renderPage(currentPage + 1));
                pagination.appendChild(nextBtn);
            }

            renderPage(1);

            paginationInstances[tableId] = {
                renderPage,
                paginationElement: table.nextElementSibling
            };
        }


        // Apply pagination to tables
        document.addEventListener('DOMContentLoaded', () => {
            paginateTable('libraryListBody'.replace('Body', ''), 5); // dashboard table id: libraryList
            paginateTable('librariesTable', 5); // admin libraries index table id
            paginateTable('booksTable', 5); // admin books index table id
        });

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
                    <td class="border border-gray-300 px-4 py-2">${library.image}</td>
                    <td class="border border-gray-300 px-4 py-2">${library.name}</td>
                    <td class="border border-gray-300 px-4 py-2">${library.distance.toFixed(2)} Km</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button onclick="panToMarker(${library.id})" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 mr-2">Go to Marker</button>
                        <a href="https://www.google.com/maps/search/?api=1&query=${library.latitude},${library.longitude}" target="_blank" class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">Open Maps</a>
                    </td>
                </tr>`;
            }
            libraryListBody.innerHTML = rows;

            // Reapply pagination after updating the table rows
            paginateTable('libraryList', 5);
        }
    </script>
</x-layout>


