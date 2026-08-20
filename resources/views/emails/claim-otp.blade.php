<x-mail.layout>
    <p style="margin:0 0 16px;color:#131B2E;font-size:16px;line-height:1.5;">
        Someone is verifying ownership of <strong>{{ $profileName }}</strong> on divin.ai. If this
        is you, enter this code to confirm:
    </p>
    <p style="margin:0 0 16px;font-size:32px;font-weight:700;letter-spacing:6px;color:#0B1120;">
        {{ $code }}
    </p>
    <p style="margin:24px 0 0;color:#6B7FA3;font-size:13px;line-height:1.5;">
        This code expires in {{ $minutesValid }} minutes. If you didn't request this, you can
        safely ignore this email — no changes will be made without it.
    </p>
</x-mail.layout>
