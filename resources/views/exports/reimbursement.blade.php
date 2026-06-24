<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <td colspan="11">
                <h2>Laporan Reimbursement</h2>
            </td>
        </tr>
        <tr>
            <td>Company Name</td>
            <td colspan="10">: PT. Samana Jaya Propertindo</td>
        </tr>
        <tr>
            <td>User</td>
            <td colspan="10">: {{ $user->name }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td colspan="10">: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : '-' }} s/d
                {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Type</th>
                <th>KM Awal</th>
                <th>KM Akhir</th>
                <th>Total KM</th>
                <th>Tujuan Perjalanan</th>
                <th>Keterangan</th>
                <th>Metode Pembayaran</th>
                <th>Dana Masuk</th>
                <th>Dana Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reimbursements as $reimbursement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $reimbursement->created_at }}</td>
                    <td>{{ $reimbursement->type }}</td>
                    <td>{{ $reimbursement->km_awal ?? '0' }}</td>
                    <td>{{ $reimbursement->km_akhir ?? '0' }}</td>
                    <td>{{ $reimbursement->km_akhir - $reimbursement->km_awal ?? '0' }}</td>
                    <td>{{ $reimbursement->tujuan_perjalanan }}</td>
                    <td>{{ $reimbursement->keterangan }}</td>
                    <td>{{ $reimbursement->metode_pembayaran }}</td>
                    <td>{{ $reimbursement->dana_masuk ?? '0' }}</td>
                    <td>{{ $reimbursement->dana_keluar ?? '0' }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="9">Total</td>
                <td>{{ $reimbursements->sum('dana_masuk') }}</td>
                <td>{{ $reimbursements->sum('dana_keluar') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>