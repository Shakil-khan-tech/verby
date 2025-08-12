<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
        <title>test</title>
    </head>
    <body>
        <div class="container mt-5">
            {{ $data }}
        </div>
        <div class="container mt-5">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th ><strong> #: </strong></th>
                        <th ><strong> type: </strong></th>
                        <th ><strong> employee_id: </strong></th>
                        <th ><strong> device_id: </strong></th>
                        <th ><strong> user_id: </strong></th>
                        <th ><strong> ipv4: </strong></th>
                        <th ><strong> time: </strong></th>
                        <th ><strong> record_id: </strong></th>
                        <th ><strong> action: </strong></th>
                        <th ><strong> perform: </strong></th>
                        <th ><strong> calendar_id: </strong></th>
                        <th ><strong> rooms: </strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($table as $key => $row)
                        @foreach($row as $key => $record)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ isset($record->test_type) ? $types[$record->test_type] : '-' }}</td>
                            <td>{{ $record->employee_id }} ({{ $record->employee->fullname }})</td>
                            <td>{{ $record->device_id }} ({{ $record->device->name }})</td>
                            {{-- <td>{{ $record->test_user_id }} ({{ $record->user->name }})</td> --}}
                            <td>{{ isset($record->test_user_id) ? \App\Models\User::find($record->test_user_id)->id . '(' . \App\Models\User::find($record->test_user_id)->name . ')' : '-' }}</td>
                            <td>{{ $record->test_ipv4 }}</td>
                            <td>{{ $record->time }}</td>
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->action }} ({{ Config::get('constants.actions')[$record->action] }})</td>
                            <td>{{ $record->perform }} ({{ Config::get('constants.performs')[$record->perform] }})</td>
                            <td>{{ $record->calendar_id }}</td>
                            <td>{{ $record->rooms }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>