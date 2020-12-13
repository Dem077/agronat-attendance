

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
                    <label for="user_id">Employee</label>
                    <select type="text" class="form-control" id="user_id" placeholder="Employee" wire:bind="user_id" >
                        <option>Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{$employee->id}}" {{$user_id==$employee->id?'SELECTED':''}}>{{$employee->fullname}}</option>
                        @endforeach
                     </select>
                    @error('user_id') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="punch">Punch DateTime</label>
                    <input type="text" class="form-control" placeholder="YYYY-MM-DD HH:mm" wire:model="punch">
                    @error('start_date') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
                <button type="button" wire:click.prevent="store()" class="btn btn-primary close-modal">Save changes</button>
            </div>
        </div>
    </div>
</div>
@push('js-bottom')
<script type="text/javascript">
    var options={
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
      };
    $('#punch').datepicker(options);

    $('#user_id').select2();

    $('#punchTime').on('change', function (e) {
       @this.set('punch', e.target.value);
    });
    $('#user_id').on('change', function (e) {
       @this.set('user_id', e.target.value);
    });
</script>
@endpush
