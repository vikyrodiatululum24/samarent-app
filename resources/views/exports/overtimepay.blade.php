<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .no-border td {
            border: none;
            padding: 3px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    @php
        $monthLabel = 'N/A';
        if (! empty($month)) {
            if (is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
                $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('id')->translatedFormat('F Y');
            } elseif (is_numeric($month)) {
                $monthLabel = \Carbon\Carbon::create()->month((int) $month)->locale('id')->translatedFormat('F');
            } else {
                $monthLabel = $month;
            }
        }
    @endphp
    <table>
        <tr>
            <td colspan="2"><strong>OVERTIME PAY REPORT</strong></td>
            <td colspan="9"><strong>: LAPORAN UPAH LEMBUR PENGEMUDI (OVERTIME PAY REPORT FOR DRIVER)</strong></td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Nama Driver</strong>
                <p style="font-style: italic; margin: 2px 0;">Driver Name</p>
            </td>
            <td colspan="9">
                <p style="margin: 0;">: {{ $driverName ?? 'N/A' }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2"><strong>Bulan/Month</strong>
            </td>
            <td colspan="9">: {{ $monthLabel }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Shift</th>
                <th>From Time</th>
                <th>To Time</th>
                <th>Work Hours</th>
                <th>Normal Hours</th>
                <th>Calculated OT</th>
                <th>Amount/H</th>
                <th>OT Amount</th>
                <th>Out of Town</th>
                <th>Overnight</th>
                <th>Transport</th>
                <th>Monthly Allowance</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($overtimePays as $pay)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($pay->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($pay->tanggal)->locale('id')->translatedFormat('l') }}</td>
                    <td>{{ $pay->shift }}</td>
                    <td>{{ $pay->from_time }}</td>
                    <td>{{ $pay->to_time }}</td>
                    <td>{{ number_format($pay->worked_hours, 2) }}</td>
                    <td>{{ number_format($pay->normal_hours, 2) }}</td>
                    <td>{{ $pay->calculated_ot_hours }}</td>
                    <td>{{ number_format($pay->amount_per_hour, 2) }}</td>
                    <td>{{ number_format($pay->ot_amount, 2) }}</td>
                    <td>{{ number_format($pay->out_of_town, 2) }}</td>
                    <td>{{ number_format($pay->overnight, 2) }}</td>
                    <td>{{ number_format($pay->transport, 2) }}</td>
                    <td>{{ number_format($pay->monthly_allowance, 2) }}</td>
                    <td>{{ $pay->remarks }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="text-align: left;"><strong>Total Overtime Hours:</strong></td>
                <td><strong>{{ number_format($overtimePays->sum('calculated_ot_hours'), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Total Overtime Pay:</strong></td>
                <td>
                    <strong>{{ number_format($overtimePays->sum('ot_amount') + $overtimePays->sum('out_of_town') + $overtimePays->sum('overnight') + $overtimePays->sum('transport') + $overtimePays->sum('monthly_allowance'), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Out of Town:</strong></td>
                <td><strong>{{ number_format($overtimePays->sum('out_of_town'), 2) }}</strong></td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Overnight:</strong></td>
                <td><strong>{{ number_format($overtimePays->sum('overnight'), 2) }}</strong></td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Transport Allowance:</strong></td>
                <td><strong>{{ number_format($overtimePays->sum('transport'), 2) }}</strong></td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Monthly/Other Allowance:</strong></td>
                <td><strong>{{ number_format($overtimePays->sum('monthly_allowance'), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Grand Total:</strong></td>
                <td>
                    <strong>{{ number_format($overtimePays->sum('ot_amount') + $overtimePays->sum('overnight') + $overtimePays->sum('out_of_town') + $overtimePays->sum('transport') + $overtimePays->sum('monthly_allowance'), 2) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
