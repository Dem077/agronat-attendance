<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
            @if (session()->has('message'))
            <div
                class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3"
                role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <table class="table-fixed w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 w-20">No.</th>
                        <th class="px-4 py-2">User</th>
                        <th class="px-4 py-2">Duty Start</th>
                        <th class="px-4 py-2">Duty End</th>
                        <th class="px-4 py-2">Duty End</th>
                        <th class="px-4 py-2">Checkin</th>
                        <th class="px-4 py-2">Checkout</th>
                        <th class="px-4 py-2">Late min</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr>
                        <td class="border px-4 py-2">{{ $attendance->id }}</td>
                        <td class="border px-4 py-2">{{ $attendance->user->name }}</td>
                        <td class="border px-4 py-2">{{ $attendance->ck_date }}</td>
                        <td class="border px-4 py-2">{{ $attendance->scin }}</td>
                        <td class="border px-4 py-2">{{ $attendance->scout }}</td>
                        <td class="border px-4 py-2">{{ $attendance->in }}</td>
                        <td class="border px-4 py-2">{{ $attendance->out }}</td>
                        <td class="border px-4 py-2">{{ $attendance->late_fine }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{$attendances->links()}}
        </div>
    </div>
</div>