
<form>
    @if (session()->has('message'))
        <div class="alert alert-success">
        {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-group">
        <label for="">Location</label>
        <select class="form-control" wire:model='location'>
            <option value="">Select Location</option>
            @foreach ($locations as $location)
                <option value="{{$location->id}}">{{$location->name}}</option>
            @endforeach
        </select>
        @error('location') <span class="error">{{ $message }}</span> @enderror

    </div>
    <div class="form-group">
        <label for="">Sheet</label>
        <input type="file" wire:model="sheet">

        @error('sheet') <span class="error">{{ $message }}</span> @enderror
    </div>

    <button type="button" wire:click="logImport"  id="logImport-button" class="btn btn-primary">
    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
    Import</button>
</form>


@push('js-bottom')
<script type="text/javascript">
      $('#logImport-button').on('click',()=>{
        $('#logImport-button').attr('disabled','disabled');
        $('#logImport-button span').show();
      });
      window.livewire.on('logImported', () => {
        $('#logImport-button').removeAttr('disabled');
        $('#logImport-button span').hide();
    });


</script>
@endpush
