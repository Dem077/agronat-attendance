<table class="table">
    <thead>
      <tr>
          <th scope="col">Employee</th>
          <th scope="col">Weekday</th>
          <th scope="col">Holiday</th>
      </tr>
    </thead>
    <tbody>
          @foreach ($data as $item)
              <tr>
                  <td>{{$item['employee']}}</td>
                  <td>{{$item['weekday']}}</td>
                  <td>{{$item['holiday']}}</td>
              </tr>
          @endforeach
    </tbody>
</table>