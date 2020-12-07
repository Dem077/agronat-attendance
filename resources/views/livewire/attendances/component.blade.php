
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Attendance</h2>
            </div>

            <div class="card-body">
                <div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        Add
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="exportRecord()">
                        Export
                    </button>
                    {{-- @include('livewire.users.update')
                    @include('livewire.users.create') --}}
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Duty Start</th>
                                <th>Duty End</th>
                                <th>Checkin</th>
                                <th>Checkout</th>
                                <th>Latefine</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->id }}</td>
                                <td>{{ $attendance->user->fullname }}</td>
                                <td>{{ $attendance->ck_date }}</td>
                                <td>08:00</td>
                                <td>16:00</td>
                                <td>{{ $attendance->in }}</td>
                                <td>{{ $attendance->out }}</td>
                                <td>{{ $attendance->late_fine }}</td>
                                <td>
                                    <button data-toggle="modal" data-target="#updateModal" wire:click="edit({{ $attendance->id }})" class="btn btn-primary btn-sm">Edit</button>
                                    <button wire:click="delete({{ $attendance->id }})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$attendances->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
    window.livewire.on('userStore', () => {
        $('#createModal').modal('hide');
    });
</script>
@endpush
