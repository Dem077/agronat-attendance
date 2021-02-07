

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
                    <input type="hidden" wire:model="attendance_id">
                    <div class="form-group">
                        <label>name:</label>
                        {{$user_name}}
                    </div>
                    <div class="form-group">
                        <label>Date:</label>
                        {{$ck_date}}
                    </div>
                    <div class="form-inline">
                        <div class="form-group mt-1">
                            <label>Duty In:</label>

                        </div>
                        <div class="form-group mt-1">
                            <label>Duty Out:</label>
                        </div>
                    </div>

                    <div class="form-inline">
                        <div class="form-group mt-1">
                            <label>Check In:</label>

                        </div>
                        <div class="form-group mt-1">
                            <label>Check Out:</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Late min:</label>
                        {{$late_min}}
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="inlineFormInputName">Status</label>
                        <select type="text" class="form-control" id="inlineFormInputName" placeholder="Status" wire:bind="attendance_status" >
                            <option value=''>Select Status</option>
                            @foreach($attendance_statuses as $status)
                                <option value="{{$status}}" {{$attendance_status==$status?'SELECTED':''}}>{{$status}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button wire:click.prevent="update()" class="btn btn-dark" data-dismiss="modal">Update</button>
                <button wire:click.prevent="cancel()" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
       </div>
    </div>
</div>