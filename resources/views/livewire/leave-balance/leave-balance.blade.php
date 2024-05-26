<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2>Leave Balance</h2>
            </div>
            
            <div class="card-body">
                <div class="form-group">
                    <label for="user-select">Select Staff</label>
                    <select id="user-select" class="form-control" wire:model="selectedUser">
                        <option value="">-- Select Staff --</option>
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                @if($leaveBalances)
                    <div class="mt-3">
                        <h4>Leave Details</h4>
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
                                    @if( $balance['leave_type_id']==3 && $balance['is_annual_applicable']==false)
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
