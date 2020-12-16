

<!-- Modal -->
<div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
            <form>
                <div class="form-group">
                    <label for="name">fullname:</label>
                    <input type="text" class="form-control" id="name" wire:model="name" placeholder="name"/>
                    @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="position">position:</label>
                    <input type="text" class="form-control" id="position" wire:model="position" placeholder="position"/>
                    @error('position') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="email">email:</label>
                    <input type="email" class="form-control" id="email" wire:model="email" placeholder="email"/>
                    @error('email') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="emp_no">emp no:</label>
                    <input type="text" class="form-control" id="emp_no" wire:model="external_id" placeholder="emp no"/>
                    @error('external_id') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                {{-- <div class="form-group">
                    <label for="password">password:</label>
                    <input type="password" class="form-control" id="password" wire:model="password" placeholder="password"/>
                    @error('password') <span class="text-danger">{{ $message }}</span>@enderror
                </div> --}}
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="store()" class="btn btn-primary close-modal">Save changes</button>
            </div>
        </div>
    </div>
</div>
