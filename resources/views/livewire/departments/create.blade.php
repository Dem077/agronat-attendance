<div wire:ignore.self class="modal fade" id="department-create-modal" tabindex="-1" role="dialog"
    aria-labelledby="department-create-modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="department-create-modalLabel">Department Manage</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                    @endif
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="department-create-title">Title</label>
                            <input type="text" class="form-control mt-1" id="department-create-title" 
                                wire:model.defer="department.name" autocomplete="off" required>
                            @error('department.name') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="department-create-supervisor">Supervisor</label>
                            <select class="form-control mt-1" id="department-create-supervisor" 
                                wire:model.defer="department.supervisor_id">
                                <option value="">Select a supervisor</option>
                                @foreach ($this->users as $user)
                                    <option value="{{$user->id}}" {{$user->id==$department->supervisor_id?'SELECTED':''}}>{{$user->name}}</option>
                                @endforeach
                            </select>
                            @error('department.supervisor_id') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-check-label" for="activeCheck">
                                Work On Saturday:
                            </label>
                            <div class="form-check">

                            <input class="form-check-input" type="checkbox" id="activeCheck"  wire:model="department.work_on_saturday">

                            </div>
                      </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                @if ($update)
                <button type="button" wire:click="update" id="department-update-button" class="btn btn-primary">
                    Update</button>    
                @else
                <button type="button" wire:click="store" id="department-create-button" class="btn btn-primary">
                    Save</button>
                @endif

                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
</script>
@endpush
