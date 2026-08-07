<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e8edf5;">
                    <tr>
                        <td style="background:#1769e0;padding:16px 24px;">
                            <span style="color:#ffffff;font-size:16px;font-weight:700;">Approval Needed</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 6px;color:#17233c;font-size:15px;font-weight:700;">{{ $approval->title }}</p>

                            @if($approval->description)
                            <p style="margin:0 0 16px;color:#555;font-size:13px;line-height:1.5;">{{ $approval->description }}</p>
                            @endif

                            <p style="margin:0 0 4px;color:#7b8794;font-size:12px;">Requested by</p>
                            <p style="margin:0 0 16px;color:#17233c;font-size:13px;font-weight:600;">{{ $approval->requestedBy->name ?? 'System' }}</p>

                            <a href="{{ $approval->url }}" style="display:inline-block;background:#1769e0;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:700;">
                                Review &amp; Take Action
                            </a>

                            <p style="margin:20px 0 0;color:#a0aab8;font-size:11px;word-break:break-all;">
                                Or copy this link: {{ $approval->url }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
