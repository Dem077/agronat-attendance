
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Pre OT Request</h2>
            </div>

            <div class="card-body">
                <div>
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="row">
                      <div class="col">
                          <form>
                              <div class="row">
                                <div class="col-md-4">
                                    @livewire('partials.user-select')
                                </div>
                                <div class="col-md-4">
                                    @livewire('partials.attendance-period')
                                </div>
                              </div>
                              <div class="row">

                                <div class="col-sm-4">
                                    <a class="btn btn-primary" href="{{route('overtime.pre-ot-request.create')}}">
                                        Add
                                    </a>
                                  {{-- <button type="button" class="btn btn-success" wire:click.prevent="exportRecord()"><i class="fas fa-file-download"></i></button> --}}
                                </div>
                              </div>
                            </form>
                      </div>
                  </div>
                    
                    {{-- @include('livewire.users.update')
                    @include('livewire.users.create') --}}
                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>OT Minutes</th>
                                <th>status</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                </div>
                
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
    Livewire.on('userSelected', id => {
        @this.set('user_id', id);
    });
    Livewire.on('periodSelected', period => {
      @this.set('start_date', period['start']);
      @this.set('end_date', period['end']);
    });
</script>
@endpush
