<x-mail.layout>
    <p style="margin:0 0 16px;color:#131B2E;font-size:16px;line-height:1.5;">
        Click below to sign in to your divin.ai account.
    </p>
    <a href="{{ $url }}"
       style="display:inline-block;background:#FF6B35;color:#ffffff;text-decoration:none;
              padding:12px 24px;border-radius:8px;font-weight:600;font-size:15px;">
        Sign in
    </a>
    <p style="margin:24px 0 0;color:#6B7FA3;font-size:13px;line-height:1.5;">
        This link expires in {{ $minutesValid }} minutes and can only be used once. If you
        didn't request this, you can safely ignore this email.
    </p>
</x-mail.layout>
