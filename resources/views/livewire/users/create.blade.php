

<!-- Modal -->
<div wire:ignore.self class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
            <form>
                <div class="form-group">
                    <label for="name">Fullname:*</label>
                    <input type="text" class="form-control" id="name" wire:model="name" placeholder="Fullname"/>
                    @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="name">National ID / Passport:*</label>
                    <input type="text" class="form-control" id="nid" wire:model="nid" placeholder="National ID / Passport"/>
                    @error('nid') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="department_id">Department:</label>
                        <select name="department_id" id="department_id" class="form-control" wire:model="department_id">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{$department->id}}" >{{$department->name}}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-danger">{{ $message }}</span>@enderror

                </div>
                <div class="form-group">
                    <label for="supervisor_id">Supervisor:</label>
                        <select name="supervisor_id" id="supervisor_id" class="form-control" wire:model="supervisor_id">
                            <option value="">Select Supervisor</option>
                            @foreach ($active_employees as $auser)
                                <option value="{{$auser->id}}" >{{$auser->name}} ({{$auser->emp_no}})</option>
                            @endforeach
                        </select>
                        @error('supervisor_id') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="location_id">Location:</label>
                        <select name="location_id" id="location_id" class="form-control" wire:model="location_id">
                            <option value="">Select Location</option>
                            @foreach ($locations as $location)
                                <option value="{{$location->id}}" >{{$location->name}}</option>
                            @endforeach
                        </select>
                        @error('location_id') <span class="text-danger">{{ $message }}</span>@enderror

                </div>
                <div class="form-group">
                    <label for="designation">Designation:*</label>
                    <input type="text" class="form-control" id="designation" wire:model="designation" placeholder="Designation"/>
                    @error('designation') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="joined_date">Joined Date:*</label>
                    <input type="date" class="form-control" id="joined_date" wire:model="joined_date" placeholder="Joined Date"/>
                    @error('joined_date') <span class="text-danger">{{ $message }}</span>@enderror
                </div> 
                <div class="form-group">
                    <label for="email">Email:*</label>
                    <input type="email" class="form-control" id="email" wire:model="email" autocomplete="off" placeholder="Email"/>
                    @error('email') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="emp_no">Emp No:*</label>
                    <input type="text" class="form-control" id="emp_no" wire:model="emp_no" placeholder="Emp no"/>
                    @error('emp_no') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="gender">Gender:*</label>
                    <select class="form-control" id="gender" wire:model="gender">
                        <option value="">Select Gender</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                    @error('gender') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="mobile">Mobile:</label>
                        <input type="text" class="form-control" id="mobile" wire:model="mobile" placeholder="Mobile"/>
                        @error('mobile') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone">Phone:</label>
                        <input type="text" class="form-control" id="phone" wire:model="phone" placeholder="Phone"/>
                        @error('phone') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="password">Password:*</label>
                        <input type="password" class="form-control" id="password" wire:model="password" autocomplete="off" placeholder="Password"/>
                        @error('password') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password_confirmation">Password Confirmation:</label>
                        <input type="password" class="form-control" id="password_confirmation" wire:model="password_confirmation" autocomplete="off" placeholder="Password Confirmation"/>
                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span>@enderror
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

