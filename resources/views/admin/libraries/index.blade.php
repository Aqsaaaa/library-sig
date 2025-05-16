<x-layout title="Manage Libraries">
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Manage Libraries</h1>
            <a href="{{ route('admin.libraries.create') }}" class="px-4 py-2 bg-[#f53003] text-white rounded hover:bg-red-600">Add New Library</a>
        </div>
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        <table id="librariesTable" class="min-w-full bg-white border border-gray-200 rounded">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 border-b">Image</th>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Address</th>
                    <th class="py-2 px-4 border-b">LatLong</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($libraries as $library)
                <tr>
                    <td class="py-2 px-4 border-b">
                        @if($library->image)
                            <img src="{{ asset('storage/' . $library->image) }}" alt="Library Image" class="mx-auto max-h-16 rounded">
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b">{{ $library->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $library->address ?? '-' }}</td>
                    <td class="py-2 px-4 border-b">{{ $library->latitude . ', ' . $library->longitude ?? '-' }}</td>
                    <td class="py-2 px-4 border-b space-x-2">
                        <a href="{{ route('admin.libraries.edit', $library) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('admin.libraries.destroy', $library) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this library?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($libraries->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-4">No libraries found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <script>
        // Generic client-side pagination for tables
        let paginationInstances = {};
        function paginateTable(tableId, rowsPerPage = 5) {
            const table = document.getElementById(tableId);
            if (!table) return;

            // Clear previous pagination if exists
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

        document.addEventListener('DOMContentLoaded', () => {
            paginateTable('librariesTable', 5);
        });
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
</x-layout>
