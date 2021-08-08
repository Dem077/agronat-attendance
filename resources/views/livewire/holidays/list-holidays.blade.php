
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Holiday Manage</h2>
            </div>

            <div class="card-body">
                <div>
                    <div class="bg-light p-3">
                        @livewire('holidays.add-holiday', key('add-holiday'))

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <form>
                                <div class="form-row align-items-center">
                                  <div class="col-sm-3 my-1">
                                      @livewire('partials.attendance-period')
                                  </div>
                                </div>
                              </form>
                        </div>
                    </div>

                  <div class="table-responsive">
                    <table class="table table-bordered mt-5">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->holidays as $holiday)
                            <tr>
                                <td>{{$holiday->h_date}}</td>
                                <td>{{date('D', strtotime($holiday->h_date))}}</td>
                                <td>{{$holiday->description}}</td>
                                <td width="20px"><span class="btn text-danger" onclick="deleteHoliday({{$holiday->id}})">&times;</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$this->holidays->links()}}
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@push('js-bottom')
<script type="text/javascript">
    Livewire.on('periodSelected', period => {
      @this.set('start_date', period['start']);
      @this.set('end_date', period['end']);
    });
    function deleteHoliday(id){
      if (confirm('Are you sure?')) {
          @this.delete(id);
      }
    }

</script>
@endpush
