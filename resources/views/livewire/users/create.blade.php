

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
                    {{-- <label for="department_id">department:</label>
                    <input type="text" class="form-control" id="department_id" wire:model="department_id" placeholder="department_id"/>
                    @error('department_id') <span class="text-danger">{{ $message }}</span>@enderror --}}

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
                    <input type="text" class="form-control" id="emp_no" wire:model="emp_no" placeholder="emp no"/>
                    @error('emp_no') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        <label for="mobile">mobile:</label>
                        <input type="text" class="form-control" id="mobile" wire:model="mobile" placeholder="mobile"/>
                        @error('mobile') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="phone">phone:</label>
                        <input type="text" class="form-control" id="phone" wire:model="phone" placeholder="phone"/>
                        @error('phone') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
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
