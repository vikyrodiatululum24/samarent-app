<table>
    <thead>
    <tr>
        <th>No.</th>
        <th>Unit (Nopol)</th>
        <th>No. Kunci</th>
        <th>Lokasi</th>
        <th>Status Kunci</th>
        <th>Tanggal Masuk</th>
        <th>Tanggal Keluar</th>
        <th>Diambil Oleh</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $index => $row)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $row->unit->nopol ?? '-' }}</td>
            <td>{{ $row->no_kunci }}</td>
            <td>{{ $row->lokasi }}</td>
            <td>{{ $row->status_kunci }}</td>
            <td>{{ $row->tanggal_masuk }}</td>
            <td>{{ $row->tanggal_keluar }}</td>
            <td>{{ $row->diambil_oleh }}</td>
            <td>{{ $row->keterangan }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
