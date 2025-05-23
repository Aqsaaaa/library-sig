<x-layout title="Add New Library">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Add New Library</h1>
        <form action="{{ route('admin.libraries.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div id="map" style="height: 500px; width: 100%; border: 1px solid #ccc" class="mb-4"></div>
            <div>
                <label for="name" class="block mb-1 font-medium">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded p-2" />
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="address" class="block mb-1 font-medium">Address</label>
                <textarea name="address" id="address" class="w-full border border-gray-300 rounded p-2">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="latlong" class="block mb-1 font-medium">Latitude, Longitude</label>
                <input type="text" name="latlong" id="latlong" value="{{ old('latlong') }}" placeholder="e.g. -6.200000, 106.816666" class="w-full border border-gray-300 rounded p-2"/>
                @error('latlong')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="image" class="block mb-1 font-medium">Library Image</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full border border-gray-300 rounded p-2" />
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.libraries.index') }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Add Library</button>
            </div>
        </form>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
        crossorigin=""></script>
    <script>
        var map = L.map('map').setView([-6.200000, 106.816666], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var marker = L.marker([-6.200000, 106.816666], {draggable:true}).addTo(map);

        function updatePopup() {
            var name = document.getElementById('name').value || 'Unnamed Library';
            var address = document.getElementById('address').value || 'No address provided';
            marker.bindPopup('<b>' + name + '</b><br>' + address).openPopup();
        }

        marker.on('dragend', function(e) {
            var latlng = marker.getLatLng();
            document.getElementById('latlong').value = latlng.lat.toFixed(6) + ', ' + latlng.lng.toFixed(6);
            updatePopup();
        });

        document.getElementById('address').addEventListener('change', function() {
            var address = this.value;
            if (address.length > 5) {
                geocodeAddress(address);
            }
            updatePopup();
        });


        // Create the button element with improved styling for better appearance
        var generateAddressBtn = document.createElement('button');
        generateAddressBtn.type = 'button';
        generateAddressBtn.textContent = 'search';
        generateAddressBtn.className = 'absolute right-2 top-1 transform -translate-y-1/2 px-4 bg-blue-600 text-white rounded shadow-md hover:bg-blue-700 transition duration-300 ease-in-out';

        // Insert the button inside the name input container
        var nameInput = document.getElementById('name');
        var nameInputContainer = nameInput.parentNode;
        nameInputContainer.style.position = 'relative';
        nameInputContainer.appendChild(generateAddressBtn);

        // Add click event to the button to trigger geocoding
        generateAddressBtn.addEventListener('click', function() {
            var name = nameInput.value;
            if (name.length > 3) {
                geocodeAddress(name);
            } else {
                alert('Please enter at least 4 characters in the name field to generate address.');
            }
        });

        // Keep the updatePopup call on name input for popup update only
        document.getElementById('name').addEventListener('input', function() {
            updatePopup();
        });

        function geocodeAddress(address) {
            fetch('https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q=' + encodeURIComponent(address))
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var result = data[0];
                        var lat = parseFloat(result.lat);
                        var lon = parseFloat(result.lon);
                        marker.setLatLng([lat, lon]);
                        map.setView([lat, lon], 13);
                        document.getElementById('latlong').value = lat.toFixed(6) + ', ' + lon.toFixed(6);

                        // Parse address components
                        var addr = result.address || {};
                        var formattedAddress = '';
                        if (addr.road) formattedAddress += addr.road + ', ';
                        if (addr.house_number) formattedAddress +=  'no.' + addr.house_number + ', ';
                        if (addr.suburb) formattedAddress += addr.suburb + ', ';
                        if (addr.city) formattedAddress += addr.city + ', ';
                        if (addr.state) formattedAddress += addr.state + ', ';
                        if (addr.country) formattedAddress += addr.country + ', ';
                        if (addr.postcode) formattedAddress += addr.postcode ;

                        // Remove trailing comma and space
                        formattedAddress = formattedAddress.replace(/, $/, '');

                        // Update address textarea with formatted address
                        document.getElementById('address').value = formattedAddress;

                        updatePopup();
                    } else {
                        alert('Alamat tidak ditemukan. Silakan periksa kembali atau masukkan koordinat secara manual.');
                    }
                })
                .catch(error => {
                    console.error('Error saat melakukan geocoding:', error);
                    alert('Terjadi kesalahan saat mencari alamat. Silakan coba lagi nanti.');
                });
        }

        updatePopup();
    </script>
</x-layout>
