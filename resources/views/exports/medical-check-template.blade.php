<table>
    <thead>
        <tr>
            @foreach($columns as $column)
                <th>{{ $headerLabels[$column] ?? $column }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach($columns as $column)
                    <td>{{ $row[$column] ?? '' }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
