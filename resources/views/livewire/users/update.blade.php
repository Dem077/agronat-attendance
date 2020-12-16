

<!-- Modal -->
<div wire:ignore.self class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" wire:model="post_id">
                    <div class="form-group">
                        <label for="name">fullname:</label>
                        <input type="text" class="form-control" id="fullname" wire:model="name" placeholder="fullanme"/>
                        @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="designation">designation:</label>
                        <input type="text" class="form-control" id="designation" wire:model="designation" placeholder="designation"/>
                        @error('designation') <span class="text-danger">{{ $message }}</span>@enderror
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
                <button wire:click.prevent="update()" class="btn btn-dark" data-dismiss="modal">Update</button>
                <button wire:click.prevent="cancel()" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
       </div>
    </div>
</div>