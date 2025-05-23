<x-layout title="Edit Library">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6 text-center">Edit Library</h1>
        <div id="map" style="height: 500px; width: 100%; border: 1px solid #ccc" class="mb-4"></div>
        <form action="{{ route('admin.libraries.update', $library) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block mb-1 font-medium">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $library->name) }}" required class="w-full border border-gray-300 rounded p-2" />
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="address" class="block mb-1 font-medium">Address</label>
                <textarea name="address" id="address" class="w-full border border-gray-300 rounded p-2">{{ old('address', $library->address) }}</textarea>
                @error('address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="latlong" class="block mb-1 font-medium">Latitude, Longitude</label>
                <input type="text" name="latlong" id="latlong" value="{{ old('latlong', $library->latitude . ', ' . $library->longitude) }}" placeholder="e.g. -6.200000, 106.816666" class="w-full border border-gray-300 rounded p-2"/>
                @error('latlong')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="image" class="block mb-1 font-medium">Library Image</label>
                @if($library->image)
                    <img src="{{ asset('storage/' . $library->image) }}" alt="Library Image" class="mb-2 max-h-40">
                @endif
                <input type="file" name="image" id="image" accept="image/*" class="w-full border border-gray-300 rounded p-2" />
                @error('image')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.libraries.index') }}" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Update Library</button>
            </div>
        </form>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
        integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
        crossorigin=""></script>
    <script>
        var latlongValue = "{{ old('latlong', $library->latitude . ', ' . $library->longitude) }}";
        var initialLatLng = latlongValue ? latlongValue.split(',').map(Number) : [-6.200000, 106.816666];

        var map = L.map('map').setView(initialLatLng, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var marker = L.marker(initialLatLng, {draggable:true}).addTo(map);

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

        document.getElementById('name').addEventListener('input', function() {
            updatePopup();
        });

        function geocodeAddress(address) {
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        marker.setLatLng([lat, lon]);
                        map.setView([lat, lon], 13);
                        document.getElementById('latlong').value = lat.toFixed(6) + ', ' + lon.toFixed(6);
                        updatePopup();
                    }
                });
        }

        updatePopup();
    </script>
</x-layout>
