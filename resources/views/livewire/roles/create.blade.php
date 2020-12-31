
<!-- Modal -->
<div wire:ignore.self class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <style>
            /* Hiding the checkbox, but allowing it to be focused */
    .badgebox
    {
        display: none;
    }

    .badgebox + .badge
    {
        /* Move the check mark away when unchecked */
        text-indent: 999999px;
        /* Makes the badge's width stay the same checked and unchecked */
        width: 27px;
        background: rgb(101, 131, 110);
        margin: 5px;
    }

    .badgebox:focus + .badge
    {
        /* Set something to make the badge looks focused */
        /* This really depends on the application, in my case it was: */
        
        /* Adding a light border */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }

    .badgebox:checked + .badge
    {
        /* Move the check mark back when checked */
        text-indent: 0;
    }
    </style>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
            <form>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Role Name" wire:model="name" />
                    @error('name') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <hr/>
                <div class="form-group">
                    <strong>Permission:</strong>
                    <br/>
                    <div class="row">
                        @foreach($permissions as $permission)
                            <div class="col-sm-3 form-check form-check-inline">
                                <label for="Checkbox{{$permission->id}}" class="btn btn-success btn-sm w-100 text-left"><input type="checkbox" id="Checkbox{{$permission->id}}" wire:model="permission.{{$permission->id}}" class="badgebox"><span class="badge">&check;</span>{{ $permission->name }}</label>
                                {{-- <input class="form-check-input" id="Checkbox{{$permission->id}}" type="checkbox" wire:model="permission.{{$permission->id}}">
                                <label class="form-check-label" for="Checkbox{{$permission->id}}">{{ $permission->name }}</label> --}}
                            </div>
                        <br/>
                        @endforeach
                    </div>

                    @error('permission') <span class="text-danger">{{ $message }}</span>@enderror
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
    $('#user_id').select2();

    $('#user_id').on('change', function (e) {
       @this.set('user_id', e.target.value);
    });
</script>
@endpush
