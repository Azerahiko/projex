<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Bergabung ke Projex</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #374151; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9fafb; border-radius: 8px; padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: #2563eb; border-radius: 12px; margin-bottom: 16px;">
                <span style="font-size: 18px; font-weight: bold; color: white;">P</span>
            </div>
            <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #111827;">Undangan Bergabung ke Projex</h1>
        </div>

        <p style="font-size: 16px; color: #374151;">Halo,</p>

        <p style="font-size: 16px; color: #374151;">
            Anda diundang untuk bergabung ke <strong>Projex</strong> — aplikasi manajemen project —
            dengan peran <strong>{{ $roleLabel }}</strong>.
        </p>

        <p style="font-size: 16px; color: #374151;">
            Undangan ini dikirim oleh <strong>{{ $invitation->inviter->name }}</strong>.
        </p>

        <div style="text-align: center; margin: 32px 0;">
            <a href="{{ $acceptUrl }}"
               style="display: inline-block; background: #2563eb; color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px;">
                Terima Undangan
            </a>
        </div>

        <p style="font-size: 14px; color: #6b7280; margin-top: 24px;">
            <strong>Catatan penting:</strong>
        </p>
        <ul style="font-size: 14px; color: #6b7280; padding-left: 20px; margin: 8px 0 24px;">
            <li>Link ini hanya dapat digunakan <strong>satu kali</strong>.</li>
            <li>Link akan kedaluwarsa pada <strong>{{ $expiresAt }}</strong> (WIB).</li>
            <li>Jika Anda tidak mengharapkan undangan ini, abaikan email ini.</li>
        </ul>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

        <p style="font-size: 12px; color: #9ca3af; text-align: center; margin: 0;">
            Jika tombol di atas tidak berfungsi, salin tautan berikut ke browser Anda:<br>
            <a href="{{ $acceptUrl }}" style="color: #2563eb; word-break: break-all;">{{ $acceptUrl }}</a>
        </p>

        <p style="font-size: 12px; color: #9ca3af; text-align: center; margin-top: 16px;">
            &copy; {{ date('Y') }} Projex. Semua hak dilindungi.
        </p>
    </div>
</body>
</html>