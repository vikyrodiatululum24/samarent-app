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
            <td>{{ strtoupper($row->unit->nopol ?? '-') }}</td>
            <td>{{ strtoupper($row->no_kunci ?? '') }}</td>
            <td>{{ strtoupper($row->lokasi ?? '') }}</td>
            <td>{{ strtoupper($row->status_kunci ?? '') }}</td>
            <td>{{ $row->tanggal_masuk }}</td>
            <td>{{ $row->tanggal_keluar }}</td>
            <td>{{ ucwords(strtolower($row->diambil_oleh ?? '')) }}</td>
            <td>{{ $row->keterangan }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
