

<!-- Modal -->
<div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" wire:model="post_id">
                    <div class="form-group">
                        <label for="name">fullname:</label>
                        <input type="text" class="form-control" id="fullname" wire:model="name" placeholder="fullanme" readonly="readonly"/>
                        @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="department_id">department:</label>
                        <select name="department_id" id="department_id" class="form-control" wire:bind="department_id" disabled>
                            <option value="">Select department</option>
                            @foreach ($departments as $department)
                                <option value="{{$department->id}}" {{$department->id==$department_id?'SELECTED':''}}>{{$department->name}}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-danger">{{ $message }}</span>@enderror
    
                        <label for="designation">designation:</label>
                        <input type="text" class="form-control" id="designation" wire:model="designation" placeholder="designation" readonly="readonly"/>
                        @error('designation') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="location_id">location:</label>
                            <select name="location_id" id="location_id" class="form-control" wire:model="location_id" disabled>
                                <option value="">Select location</option>
                                @foreach ($locations as $location)
                                    <option value="{{$location->id}}" >{{$location->name}}</option>
                                @endforeach
                            </select>
                            @error('location_id') <span class="text-danger">{{ $message }}</span>@enderror
        
                    </div>
                    <div class="form-group">
                        <label for="email">email:</label>
                        <input type="email" class="form-control" id="email" wire:model="email" placeholder="email" readonly="readonly"/>
                        @error('email') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="emp_no">emp no:</label>
                        <input type="text" class="form-control" id="emp_no" wire:model="emp_no" placeholder="emp no" readonly="readonly"/>
                        @error('emp_no') <span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <div class="form-group mt-1">
                            <label for="mobile">mobile:</label>
                            <input type="text" class="form-control" id="mobile" wire:model="mobile" placeholder="mobile" readonly="readonly
"/>
                            @error('mobile') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group mt-1">
                            <label for="phone">phone:</label>
                            <input type="text" class="form-control" id="phone" wire:model="phone" placeholder="phone" readonly="readonly
"/>
                            @error('phone') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button wire:click.prevent="cancel()" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
       </div>
    </div>
</div>