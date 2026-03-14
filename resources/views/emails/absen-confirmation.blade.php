<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Konfirmasi Penyelesaian Tugas Driver</title>
</head>
<body style="margin: 0; padding: 0; background-color: #edf2f7; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f7fb; padding: 24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #dbe5f1;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #0b5cab 100%); padding: 22px 24px;">
                            <p style="margin: 0; font-size: 11px; line-height: 16px; text-transform: uppercase; letter-spacing: 0.08em; color: #bfdbfe;">Layanan Driver</p>
                            <h1 style="margin: 6px 0 0; font-size: 22px; line-height: 30px; color: #ffffff; font-weight: 700;">{{ $appName }}</h1>
                            <p style="margin: 6px 0 0; font-size: 13px; line-height: 20px; color: #dbeafe;">Konfirmasi Penyelesaian Tugas Driver</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 24px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 24px;">Halo,</p>
                            <p style="margin: 0 0 12px; font-size: 15px; line-height: 24px;">Terima kasih telah menggunakan layanan kami. Driver telah menyelesaikan tugasnya.</p>
                            <p style="margin: 0 0 20px; font-size: 15px; line-height: 24px;">Silakan klik tombol berikut untuk mengonfirmasi penyelesaian tugas:</p>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 0 20px;">
                                <tr>
                                    <td align="center" style="border-radius: 10px; background-color: #0b5cab;">
                                        <a href="{{ $confirmationUrl }}" style="display: inline-block; padding: 12px 22px; font-size: 14px; font-weight: 700; color: #ffffff; text-decoration: none;">Konfirmasi Sekarang</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 10px; font-size: 13px; line-height: 20px; color: #6b7280;">Jika tombol tidak berfungsi, gunakan tautan berikut:</p>
                            <p style="margin: 0 0 20px; font-size: 13px; line-height: 20px;"><a href="{{ $confirmationUrl }}" style="color: #0b5cab; word-break: break-all;">{{ $confirmationUrl }}</a></p>

                            <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">

                            <p style="margin: 0 0 8px; font-size: 14px; line-height: 22px;">Hello,</p>
                            <p style="margin: 0 0 8px; font-size: 14px; line-height: 22px;">Thank you for using our service. The driver has completed the task.</p>
                            <p style="margin: 0 0 8px; font-size: 14px; line-height: 22px;">Please use the button above to confirm task completion.</p>
                            <p style="margin: 0; font-size: 14px; line-height: 22px; color: #6b7280;">If you did not request this service, please ignore this email.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 16px 24px; background-color: #f8fafc; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; line-height: 18px; color: #6b7280;">Email ini dikirim otomatis oleh sistem {{ $appName }}.</p>
                            @if (!empty($supportEmail))
                                <p style="margin: 6px 0 0; font-size: 12px; line-height: 18px; color: #6b7280;">Butuh bantuan? Hubungi {{ $supportEmail }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
