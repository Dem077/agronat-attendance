
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
                                @if (count($this->users)>1)
                                <div class="col-md-4">
                                    @livewire('partials.user-select')
                                </div>
                                @endif
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
                  <div class="table-responsive table-hover">
                    <table class="table mt-5">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Date</th>
                                <th>Duaration</th>
                                <th>OT Minutes</th>
                                <th>Purpose</th>
                                <th>status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->ot_requests as $item)
                                <tr>
                                    <td>{{$item->employee}}{!!$item->user_id!=$item->requested_user_id?"<br/><small>requested by: {$item->requested_by}</small>":""!!}</td>
                                    <td>{{$item->ot_date}} <br/><small>[ {{date_format(date_create($item->ot_date),'D')}} ]</small></td>
                                    <td>{{date('H:i',strtotime($item->start_time))}} - {{date('H:i',strtotime($item->end_time))}}</td>
                                    <td>{{$item->mins}}</td>
                                    <td>{{$item->purpose}}</td>
                                    <td>{{$item->status}}{!!$item->status=='approved'?"<br/><small>[ {$item->approved_by} ]</small>":""!!}
                                        @can('overtime.pre-ot-approve')
                                            @if($item->status=='pending')
                                                <button class="btn btn-outline text-success" onclick="decision({{$item->id}},'approved')">✔</button>
                                                <button class="btn btn-outline text-danger" onclick="decision({{$item->id}},'rejected')">❌</button>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{$this->ot_requests->links()}}

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

    function decision(id,status){
      if (confirm('Are you sure?')) {
          Livewire.emit('updateStatus',id,status);
      }
    }
</script>
@endpush
