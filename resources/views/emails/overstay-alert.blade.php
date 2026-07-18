<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Overstay Alert</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Segoe UI,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:28px 12px;">
<tr>
<td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 4px 18px rgba(17,24,39,.08);">

    <!-- Header -->
    <tr>
        <td style="background:linear-gradient(135deg,#667eea,#764ba2);padding:26px 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#ffffff;font-size:18px;font-weight:700;">LiberPX</td>
                    <td align="right" style="color:rgba(255,255,255,.85);font-size:12px;">{{ $library->name }}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Alert banner -->
    <tr>
        <td style="padding:24px 28px 4px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff5f5;border:1px solid #fecaca;border-radius:12px;">
                <tr>
                    <td style="padding:16px 18px;">
                        <span style="font-size:22px;vertical-align:middle;">&#9888;&#65039;</span>
                        <span style="font-size:15px;font-weight:700;color:#991b1b;vertical-align:middle;">
                            {{ $overstayed->count() }} student{{ $overstayed->count() > 1 ? 's are' : ' is' }} still checked in past shift time
                        </span>
                        <div style="font-size:12.5px;color:#7f1d1d;margin-top:6px;">
                            These members haven't checked out even though their shift has ended. Please verify and check them out.
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- List -->
    <tr>
        <td style="padding:16px 28px 8px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                @foreach($overstayed as $a)
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #f1f3f9;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="38" style="vertical-align:top;">
                                    <table role="presentation" cellpadding="0" cellspacing="0">
                                        <tr><td width="32" height="32" align="center" valign="middle" style="background:#dc2626;color:#fff;border-radius:50%;font-size:13px;font-weight:700;">
                                            {{ substr($a->member->user->name ?? '?', 0, 1) }}
                                        </td></tr>
                                    </table>
                                </td>
                                <td style="padding-left:10px;vertical-align:top;">
                                    <div style="font-size:13.5px;font-weight:600;color:#111827;">{{ $a->member->user->name ?? 'Member' }}</div>
                                    <div style="font-size:12px;color:#6b7280;margin-top:2px;">
                                        Seat {{ $a->seat?->seat_number ?? '—' }} &middot; {{ $a->member->shift->name }} ended {{ \Carbon\Carbon::parse($a->member->shift->end_time)->format('h:i A') }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
            </table>
        </td>
    </tr>

    <!-- CTA -->
    <tr>
        <td style="padding:8px 28px 28px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="border-radius:10px;background:#dc2626;">
                        <a href="{{ url('/owner/attendance') }}" style="display:inline-block;padding:11px 22px;font-size:13.5px;font-weight:600;color:#ffffff;text-decoration:none;">
                            View Attendance &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="padding:18px 28px;background:#f8f9fc;border-top:1px solid #eef0f6;">
            <div style="font-size:11.5px;color:#9ca3af;">
                This is an automated alert from <strong>LiberPX</strong> for {{ $library->name }}. You're receiving this because you're an owner/administrator of this library account.
            </div>
        </td>
    </tr>

</table>
</td>
</tr>
</table>
</body>
</html>
