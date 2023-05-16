<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th scope="col">Schema</th>
            <th scope="col">Table</th>
            <th scope="col">Column</th>
            <th scope="col">Type</th>
            <th scope="col">Length</th>
            <th scope="col">Nullable</th>
            <th scope="col">Default</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $items)
            <tr>
                <td>
                    <input type="checkbox" class="checkbox form-check-input mt-0" value="{{ $key }}">
                </td>
                @foreach ($items as $item)
                    <td>{{ $item }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
