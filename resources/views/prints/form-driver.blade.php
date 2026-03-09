<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Data Driver</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            font-size: 10pt;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 12pt;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .form-section {
            margin-bottom: 10px;
        }

        .section-title {
            background-color: #003E77;
            color: white;
            padding: 5px 10px;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        td {
            padding: 4px 3px;
            vertical-align: top;
        }

        .form-label {
            width: 35%;
            font-weight: bold;
        }

        .form-separator {
            width: 3%;
            text-align: center;
        }

        .form-value {
            width: 62%;
            border-bottom: 1px solid #333;
            min-height: 20px;
        }

        .form-textarea {
            min-height: 35px;
        }

        .photo-box {
            border: 2px solid #333;
            width: 40mm;
            height: 30mm;
            text-align: center;
            margin: 0 auto;
            color: #999;
            font-style: italic;
            padding-top: 25mm;
            font-size: 9pt;
        }

        .radio-group {
            border-bottom: 1px solid #333;
            padding: 4px 3px;
        }

        .radio-option {
            display: inline-block;
            margin-right: 25px;
        }

        .radio-box {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 2px solid #333;
            margin-right: 6px;
            vertical-align: middle;
        }

        @media print {
            body {
                padding: 15px;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <img src="{{ public_path('images/header_samarent.jpg') }}" alt="header samarent" width="100%">
    <div class="header">
        <h1>Formulir Pendaftaran Driver</h1>
    </div>

    <!-- DATA PRIBADI -->
    <div class="form-section">
        <div class="section-title">A. DATA PRIBADI</div>

        <table>
            <tr>
                <td class="form-label">Nama Lengkap</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">NIK (KTP)</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Nomor SIM</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Tempat Lahir</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Tanggal Lahir</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Jenis Kelamin</td>
                <td class="form-separator">:</td>
                <td>
                    <div class="radio-group">
                        <div class="radio-option">
                            <span class="radio-box"></span>
                            <span>Laki-laki</span>
                        </div>
                        <div class="radio-option">
                            <span class="radio-box"></span>
                            <span>Perempuan</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="form-label">Agama</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Nomor WhatsApp</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Alamat</td>
                <td class="form-separator">:</td>
                <td class="form-value form-textarea"></td>
            </tr>
            <tr>
                <td class="form-label">RT / RW</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Kelurahan/Desa</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">Kecamatan</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
        </table>
    </div>

    <!-- DATA KEPEGAWAIAN -->
    <div class="form-section">
        <div class="section-title">B. DATA KEPEGAWAIAN (Hanya Diisi HRD)</div>

        <table>
            <tr>
                <td class="form-label">Project/Penempatan</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">End User</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>
            <tr>
                <td class="form-label">No. WhatsApp</td>
                <td class="form-separator">:</td>
                <td class="form-value"></td>
            </tr>

        </table>
    </div>

    <!-- PAS FOTO -->
    <div class="form-section">
        <div class="section-title">C. PAS FOTO</div>
        <div style="padding: 5px 0;">
            <div class="photo-box">
                Foto 4x6
            </div>
        </div>
    </div>

    {{-- Signature --}}
    <div class="form-section" style="margin-top: 30px;">
        <table>
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div style="margin-bottom: 60px;">Driver,</div>
                    <div>(_________________________)</div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div style="margin-bottom: 60px;">HRD,</div>
                    <div>(_________________________)</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
