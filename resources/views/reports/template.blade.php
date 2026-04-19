<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mediation Report — {{ $room->case_id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.65;
            color: #1a1a2e;
        }

        /* ── Cover Page ── */
        .cover {
            page-break-after: always;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px 50px;
            background: #0D1B2A;
            color: white;
        }
        .cover-logo {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #C9A84C;
        }
        .cover-logo span { color: white; }
        .cover-title {
            font-size: 28pt;
            font-weight: bold;
            color: white;
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .cover-subtitle {
            font-size: 11pt;
            color: #C9A84C;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }
        .cover-divider {
            width: 60px;
            height: 3px;
            background: #C9A84C;
            margin-bottom: 40px;
        }
        .cover-meta-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .cover-meta-row { display: table-row; }
        .cover-meta-label {
            display: table-cell;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8a9bb0;
            padding: 6px 20px 6px 0;
            width: 140px;
        }
        .cover-meta-value {
            display: table-cell;
            font-size: 10pt;
            color: white;
            font-weight: bold;
            padding: 6px 0;
        }
        .cover-footer {
            font-size: 8pt;
            color: #4a6070;
            border-top: 1px solid #1e2f42;
            padding-top: 20px;
        }
        .cover-confidential {
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid #C9A84C;
            color: #C9A84C;
            font-size: 8pt;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        /* ── Body Pages ── */
        .page { padding: 40px 50px; }

        .section { margin-bottom: 28px; }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0D1B2A;
            padding-bottom: 6px;
            border-bottom: 2px solid #C9A84C;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-number {
            color: #C9A84C;
            margin-right: 6px;
        }

        p { margin-bottom: 8px; color: #2d3748; }

        /* ── Meta box ── */
        .meta-box {
            background: #f8f5ee;
            border-left: 4px solid #C9A84C;
            padding: 14px 18px;
            margin-bottom: 28px;
            border-radius: 0 4px 4px 0;
        }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { padding: 4px 12px 4px 0; font-size: 9.5pt; vertical-align: top; }
        .meta-label { font-weight: bold; color: #0D1B2A; width: 130px; }
        .meta-value { color: #4a5568; }

        /* ── Parties ── */
        .parties-grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .party-cell { width: 50%; padding: 12px; vertical-align: top; }
        .party-box {
            padding: 14px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .party-a-box { background: #eff6ff; border-color: #bfdbfe; }
        .party-b-box { background: #f5f3ff; border-color: #ddd6fe; }
        .party-label {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .party-a-label { color: #1d4ed8; }
        .party-b-label { color: #7e22ce; }
        .party-name { font-size: 11pt; font-weight: bold; color: #1a202c; }
        .party-email { font-size: 8.5pt; color: #718096; }

        /* ── Transcript ── */
        .phase-block { margin-bottom: 18px; }
        .phase-header {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #C9A84C;
            background: #fdf8ee;
            padding: 5px 10px;
            border-left: 3px solid #C9A84C;
            margin-bottom: 8px;
        }
        .message-row { margin-bottom: 6px; padding: 6px 10px; border-radius: 4px; }
        .msg-lex { background: #fdf8ee; border-left: 3px solid #C9A84C; }
        .msg-a   { background: #eff6ff; border-left: 3px solid #3b82f6; }
        .msg-b   { background: #f5f3ff; border-left: 3px solid #8b5cf6; }
        .msg-sender {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .msg-lex .msg-sender  { color: #92400e; }
        .msg-a .msg-sender    { color: #1d4ed8; }
        .msg-b .msg-sender    { color: #6d28d9; }
        .msg-content { font-size: 9pt; color: #374151; }
        .msg-truncated { font-size: 8pt; color: #9ca3af; font-style: italic; }

        /* ── Confidence score ── */
        .confidence-wrap { text-align: center; margin: 16px 0; }
        .confidence-score-big {
            font-size: 36pt;
            font-weight: bold;
            color: #C9A84C;
            line-height: 1;
        }
        .confidence-label { font-size: 9pt; color: #718096; margin-top: 4px; }
        .confidence-bar-bg {
            background: #e2e8f0;
            border-radius: 9999px;
            height: 8px;
            margin: 10px auto;
            max-width: 300px;
        }
        .confidence-bar-fill {
            height: 8px;
            border-radius: 9999px;
            background: linear-gradient(90deg, #C9A84C, #e8c96a);
        }

        /* ── Evidence table ── */
        .evidence-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        .evidence-table th {
            background: #f8f5ee;
            padding: 7px 10px;
            text-align: left;
            font-weight: bold;
            color: #0D1B2A;
            border-bottom: 2px solid #C9A84C;
        }
        .evidence-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }
        .evidence-table tr:last-child td { border-bottom: none; }

        /* ── Disclaimer ── */
        .disclaimer {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-left: 4px solid #f59e0b;
            padding: 14px 16px;
            margin-top: 30px;
            border-radius: 0 4px 4px 0;
            font-size: 8.5pt;
            color: #78350f;
        }
        .disclaimer strong { color: #92400e; }

        /* ── Footer ── */
        .report-footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 8pt;
            color: #a0aec0;
        }

        /* ── Page break ── */
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════ --}}
{{-- COVER PAGE                                  --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="cover">
    <div>
        <div class="cover-logo">First <span>Mediator</span></div>
    </div>

    <div>
        <div class="cover-confidential">Confidential</div>
        <div class="cover-title">Mediation<br>Report</div>
        <div class="cover-subtitle">AI-Assisted Dispute Resolution</div>
        <div class="cover-divider"></div>

        <table class="cover-meta-grid">
            <tr class="cover-meta-row">
                <td class="cover-meta-label">Case ID</td>
                <td class="cover-meta-value">{{ $room->case_id }}</td>
            </tr>
            <tr class="cover-meta-row">
                <td class="cover-meta-label">Category</td>
                <td class="cover-meta-value">{{ ucfirst($room->category) }}</td>
            </tr>
            <tr class="cover-meta-row">
                <td class="cover-meta-label">Jurisdiction</td>
                <td class="cover-meta-value">{{ $room->jurisdiction }}</td>
            </tr>
            <tr class="cover-meta-row">
                <td class="cover-meta-label">Session Duration</td>
                <td class="cover-meta-value">{{ $room->duration }}
                    @if($room->extended_minutes > 0) + {{ $room->extended_minutes }} min (extended) @endif
                    minutes
                </td>
            </tr>
            <tr class="cover-meta-row">
                <td class="cover-meta-label">Session Date</td>
                <td class="cover-meta-value">{{ $room->started_at?->format('d F Y') ?? $room->created_at->format('d F Y') }}</td>
            </tr>
            <tr class="cover-meta-row">
                <td class="cover-meta-label">Report Generated</td>
                <td class="cover-meta-value">{{ $report->generated_at?->format('d F Y, H:i') ?? now()->format('d F Y, H:i') }}</td>
            </tr>
            <tr class="cover-meta-row">
                <td class="cover-meta-label">Confidence Score</td>
                <td class="cover-meta-value">{{ $report->confidence_score }}%</td>
            </tr>
        </table>
    </div>

    <div class="cover-footer">
        Generated by First Mediator AI Mediation Platform &bull;
        firstmediator.com &bull;
        This report is confidential and intended solely for the named parties.
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- BODY                                        --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="page">

    {{-- Session Metadata --}}
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Case ID</td>
                <td class="meta-value">{{ $room->case_id }}</td>
                <td class="meta-label">Category</td>
                <td class="meta-value">{{ ucfirst($room->category) }}</td>
            </tr>
            <tr>
                <td class="meta-label">Jurisdiction</td>
                <td class="meta-value">{{ $room->jurisdiction }}</td>
                <td class="meta-label">Language</td>
                <td class="meta-value">{{ ucfirst($room->language) }}</td>
            </tr>
            <tr>
                <td class="meta-label">Session Started</td>
                <td class="meta-value">{{ $room->started_at?->format('d M Y, H:i') ?? '—' }}</td>
                <td class="meta-label">Session Ended</td>
                <td class="meta-value">{{ $room->ended_at?->format('d M Y, H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Duration</td>
                <td class="meta-value">{{ $room->duration }}min @if($room->extended_minutes > 0)(+{{ $room->extended_minutes }}min extended)@endif</td>
                <td class="meta-label">Payment Type</td>
                <td class="meta-value">{{ ucfirst($room->payment_type) }}</td>
            </tr>
        </table>
    </div>

    {{-- Parties --}}
    <div class="section">
        <div class="section-title"><span class="section-number">§</span> Parties Involved</div>
        <table class="parties-grid">
            <tr>
                <td class="party-cell">
                    <div class="party-box party-a-box">
                        <div class="party-label party-a-label">Party A — Initiator</div>
                        <div class="party-name">{{ $room->partyA?->name ?? 'Unknown' }}</div>
                        <div class="party-email">{{ $room->partyA?->email ?? '—' }}</div>
                    </div>
                </td>
                <td class="party-cell">
                    <div class="party-box party-b-box">
                        <div class="party-label party-b-label">Party B — Respondent</div>
                        <div class="party-name">{{ $room->partyB?->name ?? 'Invited Party' }}</div>
                        <div class="party-email">{{ $room->party_b_email ?? '—' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- 1. Case Summary --}}
    <div class="section">
        <div class="section-title"><span class="section-number">1.</span> Case Summary</div>
        <p>{{ $report->case_summary }}</p>
    </div>

    {{-- 2. Party A Position --}}
    <div class="section">
        <div class="section-title"><span class="section-number">2.</span> Party A's Position</div>
        <p>{{ $report->party_a_position }}</p>
    </div>

    {{-- 3. Party B Position --}}
    <div class="section">
        <div class="section-title"><span class="section-number">3.</span> Party B's Position</div>
        <p>{{ $report->party_b_position }}</p>
    </div>

    {{-- 4. Evidence --}}
    @if($evidence->isNotEmpty())
    <div class="section">
        <div class="section-title"><span class="section-number">4.</span> Evidence Submitted</div>
        <table class="evidence-table">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Submitted By</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evidence as $file)
                <tr>
                    <td>{{ $file->original_filename }}</td>
                    <td>{{ $file->party_label }}</td>
                    <td>{{ $file->mime_type }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($report->evidence_reviewed && $report->evidence_reviewed !== 'N/A')
            <p style="margin-top:10px;">{{ $report->evidence_reviewed }}</p>
        @endif
    </div>
    @endif

    {{-- 5. Factual Findings --}}
    <div class="section">
        <div class="section-title"><span class="section-number">5.</span> Factual Findings</div>
        <p>{{ $report->factual_findings }}</p>
    </div>

    {{-- 6. Contradictions --}}
    @if($report->contradictions && $report->contradictions !== 'None identified')
    <div class="section">
        <div class="section-title"><span class="section-number">6.</span> Contradictions Identified</div>
        <p>{{ $report->contradictions }}</p>
    </div>
    @endif

    {{-- 7. Legal Framework --}}
    @if($report->legal_framework && $report->legal_framework !== 'N/A')
    <div class="section">
        <div class="section-title"><span class="section-number">7.</span> Legal Framework</div>
        <p>{{ $report->legal_framework }}</p>
    </div>
    @endif

    {{-- 8. Resolution Recommendation --}}
    <div class="section">
        <div class="section-title"><span class="section-number">8.</span> Resolution Recommendation</div>
        <p>{{ $report->resolution_recommendation }}</p>
    </div>

    {{-- 9. Confidence Score --}}
    <div class="section">
        <div class="section-title"><span class="section-number">9.</span> AI Confidence Score</div>
        <div class="confidence-wrap">
            <div class="confidence-score-big">{{ $report->confidence_score }}%</div>
            <div class="confidence-label">Mediator confidence in the recommendation</div>
            <div class="confidence-bar-bg">
                <div class="confidence-bar-fill" style="width: {{ $report->confidence_score }}%;"></div>
            </div>
        </div>
    </div>

    {{-- 10. Next Steps --}}
    @if($report->next_steps && $report->next_steps !== 'N/A')
    <div class="section">
        <div class="section-title"><span class="section-number">10.</span> Recommended Next Steps</div>
        <p>{{ $report->next_steps }}</p>
    </div>
    @endif

    {{-- ── TRANSCRIPT SUMMARY (new page) ── --}}
    <div class="page-break"></div>

    <div class="section">
        <div class="section-title"><span class="section-number">11.</span> Session Transcript Summary</div>
        <p style="font-size:8.5pt; color:#718096; margin-bottom:14px;">
            Messages are grouped by session phase. Long messages are truncated for readability.
            The full session was conducted on the First Mediator platform.
        </p>

        @forelse($transcript as $phase => $messages)
            <div class="phase-block">
                <div class="phase-header">
                    Phase: {{ ucwords(str_replace('_', ' ', $phase)) }}
                    ({{ $messages->count() }} message{{ $messages->count() !== 1 ? 's' : '' }})
                </div>

                @foreach($messages as $msg)
                    @php
                        $senderClass = match($msg->sender_type) {
                            'lex'     => 'msg-lex',
                            'party_a' => 'msg-a',
                            default   => 'msg-b',
                        };
                        $senderLabel = match($msg->sender_type) {
                            'lex'     => 'FM Mediator (AI)',
                            'party_a' => 'Party A — ' . ($room->partyA?->name ?? 'Initiator'),
                            default   => 'Party B — ' . ($room->partyB?->name ?? $room->party_b_email ?? 'Respondent'),
                        };
                        $content = $msg->content;
                        $truncated = strlen($content) > 400;
                        $display = $truncated ? substr($content, 0, 400) : $content;
                    @endphp
                    <div class="message-row {{ $senderClass }}">
                        <div class="msg-sender">{{ $senderLabel }}</div>
                        <div class="msg-content">{{ $display }}</div>
                        @if($truncated)
                            <div class="msg-truncated">… [message truncated for report]</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @empty
            <p style="color:#718096; font-style:italic;">No session messages recorded.</p>
        @endforelse
    </div>

    {{-- Disclaimer --}}
    <div class="disclaimer">
        <strong>IMPORTANT DISCLAIMER:</strong><br>
        This mediation report has been generated by First Mediator, an AI-powered mediation platform.
        It is provided for informational purposes only and does <strong>not</strong> constitute legal advice.
        The analysis, findings, and recommendations are based solely on information provided during the session
        and should not be relied upon as a substitute for professional legal counsel.<br><br>
        Both parties are strongly encouraged to consult qualified legal professionals before acting on this report.
        First Mediator does not provide legal representation, and this report does not create an attorney-client relationship.<br><br>
        For professional legal referrals, visit the <strong>FM Refer</strong> section of your dashboard at firstmediator.com.
    </div>

    <div class="report-footer">
        Generated by First Mediator AI Mediation Platform &bull;
        {{ $report->generated_at?->format('d F Y \a\t H:i') ?? now()->format('d F Y \a\t H:i') }} &bull;
        Case {{ $room->case_id }} &bull;
        © {{ now()->year }} First Mediator. All rights reserved.
    </div>

</div>
</body>
</html>
