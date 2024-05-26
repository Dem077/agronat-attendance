<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Leave Manage</h2>
            </div>

            <div class="card-body">
                <div>
                    <!-- Create Button -->
                    <button type="button" class="btn btn-primary" wire:click="create" data-toggle="modal" data-target="#leaveType-edit-modal">
                        Create
                    </button>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="leaveType-edit-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" wire:ignore.self>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">{{ $leaveTypeId ? 'Edit' : 'Create' }} Leave Type</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form wire:submit.prevent="saveLeaveType">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" id="title" wire:model.defer="title">
                                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="allocated_days">Allocated Days</label>
                                            <input type="number" class="form-control" id="allocated_days" wire:model.defer="allocated_days">
                                            @error('allocated_days') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-secondary" wire:click="resetForm" data-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mt-5">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Title</th>
                                    <th>Allocated Days</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leave_types as $type)
                                <tr>
                                    <td>{{ $type->id }}</td>
                                    <td>{{ $type->title }}</td>
                                    <td>{{ $type->allocated_days }}</td>
                                    <td><button class="btn btn-warning" wire:click="edit({{ $type->id }})" data-toggle="modal" data-target="#leaveType-edit-modal">Edit</button></td>
                                    <td><button class="btn btn-danger" onclick="deleteLeave({{ $type->id }})">&times;</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $leave_types->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
    function deleteLeave(id){
        if (confirm('Are you sure?')) {
            Livewire.emit('deleteLeaveType', id);
        }
    }

    window.livewire.on('showEditModal', () => {
        $('#leaveType-edit-modal').modal('show');
    });

    window.livewire.on('hideEditModal', () => {
        $('#leaveType-edit-modal').modal('hide');
    });
</script>
@endpush
