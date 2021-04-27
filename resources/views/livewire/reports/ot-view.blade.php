@foreach ($data as $i=>$log)
<h6>week {{$i+1}}</h6>
<table class="table">
    <thead>
      <tr>
          <th scope="col">Date</th>
          <th scope="col">In</th>
          <th scope="col">Out</th>
          <th scope="col">Weekday</th>
          <th scope="col">Holiday</th>
      </tr>
    </thead>
    <tbody>
          @foreach ($log['data'] as $item)
              <tr>
                  <td>{{$item['date']}}</td>
                  <td>{{$item['in']}}</td>
                  <td>{{$item['out']}}</td>
                  <td>{{$item['weekday']}}</td>
                  <td>{{$item['holiday']}}</td>
              </tr>
          @endforeach
        <tr>
            <td colspan="3" class="text-right">SubTotal</td>
            <td>{{$log['weekday']}}</td>
            <td>{{$log['holiday']}}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">Deduct</td>
            <td>{{$log['weekly_hours']}}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">Total</td>
            <td>{{$log['weekday']}}</td>
            <td>{{$log['holiday']}}</td>
        </tr>
    </tbody>
</table>
@endforeach