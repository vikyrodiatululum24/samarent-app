<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Print Images</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            width: 33.33%;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .page {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @foreach (array_chunk($paths, 9) as $chunk)
        <div class="page">
            <table>
                @foreach (array_chunk($chunk, 3) as $row)
                    <tr>
                        @foreach ($row as $path)
                            <td>
                                <img src="file://{{ $path }}" alt="Image">
                            </td>
                        @endforeach
                        @for ($i = count($row); $i < 3; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
</body>

</html>
