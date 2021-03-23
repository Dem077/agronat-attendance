
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2>Overtime</h2>
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
                                  <button type="button" class="btn btn-success" wire:click.prevent="exportRecord()"><i class="fas fa-file-download"></i></button>

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
                                <th>No.</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>User</th>
                                <th>Checkin</th>
                                <th>Checkout</th>
                                <th>OT Minutes</th>
                                <th>status</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ots as $ot)
                            <tr>
                                <td>{{ $ot->id }}</td>
                                <td>{{ $ot->ck_date }}</td>
                                <td>{{ $ot->day }}</td>
                                <td>{{ $ot->user->name }}</td>
                                <td>{{ $ot->applied->in ?? $ot->in }}</td>
                                <td>{{ $ot->applied->out ?? $ot->out }}</td>
                                <td>{{ $ot->applied->ot ?? $ot->ot }}</td>
                                <td>Pending</td>
                                
                                <td>
                                  @can('overtime-create')
                                  <button class="btn btn-success btn-sm m-1" wire:click="create({{$ot}})" data-toggle="modal" data-target="#applyModal">Apply</button>
                                  <button class="btn btn-primary btn-sm m-1" wire:click="show({{$ot}})" data-toggle="modal" data-target="#applyModal">Show</button>
                                  @endcan
                                  @can('overtime-create')
                                  <button class="btn btn-primary btn-sm m-1" wire:click="edit({{$ot}})" data-toggle="modal" data-target="#applyModal">Update</button>
                                  @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$ots->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
    @include('livewire.overtime.apply')

</div>

@push('js-bottom')
<script type="text/javascript">
    window.livewire.on('.Store', () => {
        $('#createModal').modal('hide');
    });

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
