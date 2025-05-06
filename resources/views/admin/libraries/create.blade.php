<x-layout title="Add New Library">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Add New Library</h1>
        <form action="{{ route('admin.libraries.store') }}" method="POST" class="space-y-4">
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
                <input type="text" name="latlong" id="latlong" value="{{ old('latlong') }}" placeholder="e.g. -6.200000, 106.816666" class="w-full border border-gray-300 rounded p-2" readonly />
                @error('latlong')
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

        marker.on('dragend', function(e) {
            var latlng = marker.getLatLng();
            document.getElementById('latlong').value = latlng.lat.toFixed(6) + ', ' + latlng.lng.toFixed(6);
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
                    }
                });
        }

        document.getElementById('address').addEventListener('change', function() {
            var address = this.value;
            if (address.length > 5) {
                geocodeAddress(address);
            }
        });
    </script>
</x-layout>
