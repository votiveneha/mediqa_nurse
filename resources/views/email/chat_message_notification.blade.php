<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Message Received</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f4; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4; padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden;">
                    
                    <tr>
                        <td style="background:#000; color:#fff; padding:25px 30px; text-align:center;">
                            <h2 style="margin:0; font-size:24px;">New Message Received</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px;">
                            <p style="font-size:16px; color:#333; margin-bottom:20px;">
                                Hello {{ $receiver->name ?? 'User' }},
                            </p>

                            <p style="font-size:15px; color:#555; line-height:1.8; margin-bottom:20px;">
                                You have received a new message from:
                                <strong>{{ $sender->name ?? 'User' }}</strong>
                            </p>

                            <div style="background:#f9f9f9; border:1px solid #e5e7eb; border-radius:10px; padding:20px; margin-bottom:25px;">
                                <p style="margin:0; font-size:15px; color:#111;">
                                    {{ $messageText ?? 'You have a new message.' }}
                                </p>
                            </div>

                            

                            <p style="font-size:14px; color:#777; margin:0;">
                                Regards,<br>
                                <strong>Mediqa Team</strong>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f9f9f9; text-align:center; padding:20px; font-size:13px; color:#888;">
                            © {{ date('Y') }} Mediqa. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>