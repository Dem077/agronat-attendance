<!-- Modal -->
<div wire:ignore.self class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="changeModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 900px!important" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeModalLabel">Histroy</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true close-btn">×</span>
                </button>
            </div>
           <div class="modal-body">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif
                @if($changeData)
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Created At</th>
                        <th>Adjusted Date</th>
                        <th>Adjusted Time</th>
                        <th>Changed By</th>
                        <th>Reason</th>
                        <th>Type</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $count = 1; @endphp
                    @foreach($changeData as $item)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ $item['list']['created_at'] }}</td>
                            <td>{{ $item['date']}}</td>
                            <td>{{ $item['time']}}</td>
                            <td>{{ $item['list']['changed_by'] }}</td>
                            <td>{{ $item['list']['reason'] }}</td>
                            <td>{{ $item['list']['type'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>