

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
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <form>
                    <input type="hidden" wire:model="post_id">
                    <div class="form-group">
                        <label for="name">fullname:</label>
                        <input type="text" class="form-control" id="fullname" wire:model="name" placeholder="fullanme"/>
                        @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="department_id">department:</label>
                        <select name="department_id" id="department_id" class="form-control" wire:model="department_id">
                            <option value="">Select department</option>
                            @foreach ($departments as $department)
                                <option value="{{$department->id}}" >{{$department->name}}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-danger">{{ $message }}</span>@enderror
    
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
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="mobile">mobile:</label>
                            <input type="text" class="form-control" id="mobile" wire:model="mobile" placeholder="mobile"/>
                            @error('mobile') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">phone:</label>
                            <input type="text" class="form-control" id="phone" wire:model="phone" placeholder="phone"/>
                            @error('phone') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" wire:model="password" placeholder="Password"/>
                            @error('password') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password_confirmation">Password Confirmation:</label>
                            <input type="password" class="form-control" id="password_confirmation" wire:model="password_confirmation" placeholder="Password Confirmation"/>
                            @error('password_confirmation') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-check-label" for="activeCheck">
                            Active:
                          </label>
                        <div class="form-check">
                            
                          <input class="form-check-input" type="checkbox" id="activeCheck"  wire:model="active">

                        </div>
                      </div>

                </form>
            </div>
            <div class="modal-footer">
                <button wire:click.prevent="update()" class="btn btn-dark">Update</button>
                <button wire:click.prevent="cancel()" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
       </div>
    </div>
</div>