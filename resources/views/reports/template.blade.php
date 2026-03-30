<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mediation Report - {{ $room->uuid }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #C9A84C;
        }
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #0D1B2A;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 10pt;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0D1B2A;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #C9A84C;
        }
        .meta-info {
            background: #F5EDD6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .meta-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            color: #0D1B2A;
        }
        .disclaimer {
            background: #FFF3CD;
            border-left: 4px solid #FFC107;
            padding: 15px;
            margin-top: 30px;
            font-size: 9pt;
        }
        .confidence-score {
            font-size: 18pt;
            font-weight: bold;
            color: #C9A84C;
            text-align: center;
            padding: 15px;
            background: #F5EDD6;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #F5EDD6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">First Mediator</div>
        <div class="subtitle">AI-Assisted Mediation Report</div>
    </div>

    <!-- Meta Information -->
    <div class="meta-info">
        <div class="meta-row"><span class="label">Session ID:</span> {{ $room->uuid }}</div>
        <div class="meta-row"><span class="label">Date:</span> {{ $report->generated_at->format('F d, Y') }}</div>
        <div class="meta-row"><span class="label">Dispute Category:</span> {{ ucfirst($room->category) }}</div>
        <div class="meta-row"><span class="label">Jurisdiction:</span> {{ $room->jurisdiction }}</div>
        <div class="meta-row"><span class="label">Duration:</span> {{ $room->duration }} minutes</div>
        <div class="meta-row"><span class="label">Party A:</span> {{ $room->partyA->name ?? 'N/A' }}</div>
        <div class="meta-row"><span class="label">Party B:</span> {{ $room->party_b_email ?? 'N/A' }}</div>
    </div>

    <!-- Case Summary -->
    <div class="section">
        <div class="section-title">1. Case Summary</div>
        <p>{{ $report->case_summary }}</p>
    </div>

    <!-- Party A's Position -->
    <div class="section">
        <div class="section-title">2. Party A's Position</div>
        <p>{{ $report->party_a_position }}</p>
    </div>

    <!-- Party B's Position -->
    <div class="section">
        <div class="section-title">3. Party B's Position</div>
        <p>{{ $report->party_b_position }}</p>
    </div>

    <!-- Evidence Reviewed -->
    @if($evidence && count($evidence) > 0)
    <div class="section">
        <div class="section-title">4. Evidence Reviewed</div>
        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Submitted By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evidence as $file)
                <tr>
                    <td>{{ $file['filename'] }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $file['party'])) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>{{ $report->evidence_reviewed }}</p>
    </div>
    @endif

    <!-- Factual Findings -->
    <div class="section">
        <div class="section-title">5. Factual Findings</div>
        <p>{{ $report->factual_findings }}</p>
    </div>

    <!-- Contradictions -->
    @if($report->contradictions && $report->contradictions !== 'None identified')
    <div class="section">
        <div class="section-title">6. Contradictions Identified</div>
        <p>{{ $report->contradictions }}</p>
    </div>
    @endif

    <!-- Legal Framework -->
    @if($report->legal_framework)
    <div class="section">
        <div class="section-title">7. Legal Framework</div>
        <p>{{ $report->legal_framework }}</p>
    </div>
    @endif

    <!-- Resolution Recommendation -->
    <div class="section">
        <div class="section-title">8. Resolution Recommendation</div>
        <p>{{ $report->resolution_recommendation }}</p>
    </div>

    <!-- Confidence Score -->
    <div class="confidence-score">
        Confidence Score: {{ $report->confidence_score }}%
    </div>

    <!-- Next Steps -->
    @if($report->next_steps)
    <div class="section">
        <div class="section-title">9. Recommended Next Steps</div>
        <p>{{ $report->next_steps }}</p>
    </div>
    @endif

    <!-- Disclaimer -->
    <div class="disclaimer">
        <strong>IMPORTANT DISCLAIMER:</strong><br>
        This mediation report has been generated by First Mediator, an AI-powered mediation assistant. This report is provided for informational purposes only and does not constitute legal advice. The analysis, findings, and recommendations contained herein are based on the information provided during the mediation session and should not be relied upon as a substitute for professional legal counsel.
        <br><br>
        Both parties are strongly encouraged to consult with qualified legal professionals before making any decisions based on this report. First Mediator and its AI systems do not provide legal representation, and this report does not create an attorney-client relationship.
        <br><br>
        For legal escalation and professional legal opinions, please visit the FM Refer section of your dashboard.
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated by First Mediator AI Mediation Platform</p>
        <p>{{ now()->format('F d, Y \a\t h:i A') }}</p>
        <p>© {{ now()->year }} First Mediator. All rights reserved.</p>
    </div>
</body>
</html>
