<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">

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
        <label for="">Sheet</label>
        <input type="file" class="form-control" wire:model="sheet">
    </div>

    <div class="form-group">
        <label>Columns: empno,date,time</label><br/>
        <label>format: empno= integer, date=2023-01-31, time=20:00:00</label>

    </div>

    <button type="button" wire:click="logImport"  id="logImport-button" class="btn btn-primary" wire:loading.attr="disabled" wire:target="sheet">
    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>Import</button>
            </div>
        </div>
    </div>
</div>

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
