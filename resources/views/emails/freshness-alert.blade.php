<x-mail.layout>
    <p style="margin:0 0 16px;color:#131B2E;font-size:16px;line-height:1.5;">
        We compared <strong>{{ $profileName }}</strong>'s public listing against one of its other
        online sources and found {{ count($discrepancies) === 1 ? 'a difference' : 'differences' }}:
    </p>

    <table role="presentation" width="100%" style="margin:0 0 20px;border-collapse:collapse;">
        @foreach ($discrepancies as $d)
            <tr>
                <td style="padding:8px 0;border-top:1px solid #E7ECF4;color:#6B7FA3;font-size:13px;vertical-align:top;width:120px;">
                    {{ $d['label'] }}
                </td>
                <td style="padding:8px 0;border-top:1px solid #E7ECF4;color:#131B2E;font-size:14px;">
                    <span style="color:#9DAEC9;text-decoration:line-through;">{{ $d['current_value'] ?? '(empty)' }}</span>
                    &rarr;
                    <strong>{{ $d['source_value'] }}</strong>
                </td>
            </tr>
        @endforeach
    </table>

    <p style="margin:0 0 20px;color:#131B2E;font-size:14px;line-height:1.5;">
        Review each change and accept it or keep what's currently published.
    </p>

    <a href="{{ $reportUrl }}" style="display:inline-block;background:#FF6B35;color:#ffffff;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;">
        Review in dashboard
    </a>
</x-mail.layout>
