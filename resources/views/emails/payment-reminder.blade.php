<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Batas Waktu Pembayaran</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px 30px;
            color: #374151;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #111827;
        }
        .details-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .details-row:last-child {
            margin-bottom: 0;
        }
        .details-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 14px;
        }
        .details-value {
            font-weight: 700;
            color: #1f2937;
            font-size: 14px;
            text-align: right;
        }
        .cta-container {
            text-align: center;
            margin: 35px 0 20px;
        }
        .cta-button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 30px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
            transition: all 0.2s ease-in-out;
        }
        .cta-button:hover {
            background-color: #2563eb;
            box-shadow: 0 4px 12px -1px rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .warning-text {
            color: #dc2626;
            font-weight: 600;
            font-size: 14px;
            margin-top: 20px;
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pusat Informasi & Admisi UKRIDA</h1>
        </div>
        <div class="content">
            <div class="greeting">Halo, {{ $lead->lead_name }} 👋</div>
            <p>Kami ingin menginformasikan bahwa pendaftaran Anda di UKRIDA telah mencapai batas waktu (deadline) pembayaran biaya pendaftaran & kuliah pada hari ini.</p>
            
            <div class="warning-text">
                PENTING: Mohon lakukan pembayaran hari ini agar status pendaftaran Anda tetap aktif dan tidak hangus secara otomatis oleh sistem.
            </div>

            <div class="details-box">
                <div class="details-row">
                    <span class="details-label">Nama Calon Mahasiswa</span>
                    <span class="details-value">{{ $lead->lead_name }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">No. Registrasi</span>
                    <span class="details-value">{{ $lead->no_registrasi }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Program Studi / Jurusan</span>
                    <span class="details-value">{{ $lead->jurusan?->nama_jurusan ?? '-' }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Batas Waktu (Deadline)</span>
                    <span class="details-value" style="color: #dc2626;">Hari Ini</span>
                </div>
            </div>

            <p>Silakan klik tombol di bawah ini untuk mengakses sistem pendaftaran dan melakukan pembayaran serta konfirmasi pembayaran Anda:</p>
            
            <div class="cta-container">
                <a href="https://register.ukrida.ac.id/admisi/public/register/register/registerEmail" class="cta-button" target="_blank">Akses Sistem Pendaftaran</a>
            </div>

            <p style="margin-top: 30px; font-size: 13px; color: #6b7280;">Jika Anda sudah melakukan pembayaran atau telah menyelesaikan tahapan ini, mohon abaikan email ini.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} UKRIDA. All rights reserved.</p>
            <p>Ada pertanyaan? Silakan hubungi affiliate pendamping Anda atau kunjungi <a href="https://ukrida.ac.id" target="_blank">ukrida.ac.id</a></p>
        </div>
    </div>
</body>
</html>
