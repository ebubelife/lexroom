<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Reply — First Mediator</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0D1B2A 0%, #1a2f45 100%); padding: 32px 30px; text-align: center; }
        .logo { font-family: 'Georgia', serif; font-size: 28px; font-weight: bold; color: #C9A84C; margin: 0; }
        .content { padding: 36px 30px; }
        h1 { color: #0D1B2A; font-size: 20px; margin: 0 0 16px 0; }
        p { margin: 0 0 14px 0; color: #555; font-size: 15px; }
        .ticket-meta { background: #f8f8f8; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 13px; color: #666; }
        .ticket-meta strong { color: #333; }
        .message-box { background: #F5EDD6; border-left: 4px solid #C9A84C; padding: 18px 20px; margin: 24px 0; border-radius: 4px; }
        .message-box p { color: #444; margin: 0; white-space: pre-wrap; }
        .button { display: inline-block; padding: 13px 28px; background: #C9A84C; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 8px 0; }
        .footer { background: #f8f8f8; padding: 24px 30px; text-align: center; font-size: 13px; color: #777; }
        .footer a { color: #C9A84C; text-decoration: none; }
        .divider { height: 1px; background: #eee; margin: 24px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p class="logo">First Mediator</p>
        </div>

        <div class="content">
            <h1>We've replied to your support request</h1>

            <p>Hi {{ $ticket->name }},</p>
            <p>Our support team has responded to your ticket. Here's their reply:</p>

            <div class="ticket-meta">
                <strong>Ticket:</strong> #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }} &mdash; {{ $ticket->subject }}<br>
                <strong>Category:</strong> {{ $ticket->typeLabel() }} &nbsp;&bull;&nbsp;
                <strong>Status:</strong> {{ $ticket->statusLabel() }}
            </div>

            <div class="message-box">
                <p>{{ $message->body }}</p>
            </div>

            <p>To reply or view the full conversation, click the button below:</p>

            <center>
                <a href="{{ url('/support/' . $ticket->uuid) }}" class="button">View Full Conversation</a>
            </center>

            <div class="divider"></div>

            <p style="font-size: 13px; color: #888;">
                If the button doesn't work, copy and paste this link into your browser:<br>
                <a href="{{ url('/support/' . $ticket->uuid) }}" style="color: #C9A84C;">{{ url('/support/' . $ticket->uuid) }}</a>
            </p>

            <p style="font-size: 13px; color: #999; margin-top: 20px;">
                This is a reply to your support ticket. If you did not submit this request, please ignore this email.
            </p>
        </div>

        <div class="footer">
            <p><strong>First Mediator</strong> &mdash; Resolving disputes the smart way</p>
            <p>
                <a href="{{ route('privacy') }}">Privacy Policy</a> &bull;
                <a href="{{ route('terms') }}">Terms of Service</a> &bull;
                <a href="{{ url('/support') }}">My Support Tickets</a>
            </p>
            <p style="margin-top: 12px; font-size: 12px;">&copy; {{ date('Y') }} First Mediator. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
