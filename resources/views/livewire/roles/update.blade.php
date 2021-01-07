
<!-- Modal -->
<div wire:ignore.self class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
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
                <h5 class="modal-title" id="createModalLabel">Update Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
            <form>
                <div class="form-group">
                    <label for="name"><strong>Role:</strong> {{$name}}</label>
                    <input type="hidden" class="form-control" id="role_id" placeholder="Employee" wire:model="role_id" >
                </div>
                <hr/>
                <div class="form-group">
                    <strong>Permission:</strong>
                    <br/>
                    @foreach($permissions as $i=>$r)
                        <div class="col-sm-3 form-check form-check-inline">
                            <label for="Checkbox{{$r->id}}" class="btn btn-success btn-sm w-100 text-left">
                                <input type="checkbox" id="Checkbox{{$r->id}}" wire:model="permission.{{$r->id}}" class="badgebox" {{in_array($r->id,$permission)?'checked':''}}>
                                <span class="badge">&check;</span>
                                {{ $r->name }}
                            </label>
                        </div>
                    @endforeach

                    @error('permission') <span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button wire:click.prevent="update()" class="btn btn-success" data-dismiss="modal">Update</button>

            </div>
        </div>
    </div>
</div>
