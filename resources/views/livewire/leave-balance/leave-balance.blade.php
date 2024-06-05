<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Leave Balance</h2>
                <div class="text-right mt-3">
                    <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#exportModal">Export All to CSV</button>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label for="user-select">Select Staff</label>
                    <select id="user-select" class="form-control" wire:model="selectedUser" wire:ignore>
                        <option value="">Select an employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }} ( {{ $user['emp_no'] }} )</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="date-range-select">Select Date Range</label>
                    <select id="date-range-select" class="form-control" wire:model="selectedDateRange">
                        <option value="">Select a date range</option>
                        @foreach($dateRanges as $dateRange)
                            <option value="{{ $dateRange }}">{{ $dateRange }}</option>
                        @endforeach
                    </select>
                </div>

                <div wire:loading>
                    Loading leave balances...
                </div>
            
                @if($leaveBalances)
                <div class="card">
                    <div class="mt-3 card-body">
                        <h4 class="text-center mb-3">Leave Details</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Allocated Days</th>
                                    <th>Leave Taken</th>
                                    <th>Leave Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveBalances as $balance)
                                    @if($balance['joined_date_added'] == null)
                                    <tr>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                    @else
                                        @if($balance['user_gender'] === 'M' && $balance['leave_type_id'] == 7)
                                            @continue
                                        @endif 
                                        @if($balance['user_gender'] === 'F' && $balance['leave_type_id'] == 6)
                                            @continue 
                                        @endif
                                        @if($balance['leave_type_id'] == 3 && !$balance['is_annual_applicable'])
                                            <tr>
                                                <td>{{ $balance['leave_type'] }}</td>
                                                <td><strong style="color: red">NA</strong></td>
                                                <td><strong style="color: red">NA</strong></td>
                                                <td><strong style="color: red">NA</strong></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>{{ $balance['leave_type'] }}</td>
                                                <td>{{ $balance['allocated_days'] }}</td>
                                                <td>{{ $balance['leave_taken'] }}</td>
                                                <td>{{ $balance['leave_balance'] }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportModalLabel">Select Date for Export</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="date-select">Select End Date</label>
            <input type="date" id="date-select" class="form-control" wire:model="dateselected">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" wire:click="exportleave">Export</button>
      </div>
    </div>
  </div>
</div>

@push('js-bottom')
<script>
    document.addEventListener('livewire:load', function () {
        $('#user-select').select2();

        $('#user-select').on('change', function (e) {
            var data = $(this).val();
            @this.set('selectedUser', data);
            $('#date-range-select').val(null).trigger('change');
        });

        $('#date-range-select').on('change', function (e) {
            var data = $(this).val();
            @this.set('selectedDateRange', data);
        });
    });

    document.addEventListener('livewire:update', function () {
        $('#user-select').select2();
    });
</script>
@endpush
