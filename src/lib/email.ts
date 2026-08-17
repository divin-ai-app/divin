import { createTransport } from "nodemailer";
import type { NodemailerConfig } from "next-auth/providers/nodemailer";

// Shared transactional email sending. Auth.js's magic-link flow calls
// sendMagicLinkEmail directly (see src/lib/auth.ts); other flows (claim OTP,
// freshness alerts) will reuse sendMail from Phase 3/6 onward.

type SendVerificationRequestParams = Parameters<
  NodemailerConfig["sendVerificationRequest"]
>[0];

const BRAND = {
  name: "divin.ai",
  ink900: "#0B1120",
  accent: "#FF6B35",
  ink50: "#F5F7FB",
};

export async function sendMagicLinkEmail(
  params: SendVerificationRequestParams,
) {
  const { identifier: to, url, provider } = params;
  const transport = createTransport(provider.server);
  const host = new URL(url).host;

  await transport.sendMail({
    to,
    from: provider.from,
    subject: `Sign in to ${BRAND.name}`,
    text: `Sign in to ${BRAND.name}\n\n${url}\n\nThis link expires shortly and can only be used once. If you didn't request this, you can ignore this email.`,
    html: magicLinkHtml({ url, host }),
  });
}

function magicLinkHtml({ url, host }: { url: string; host: string }) {
  return `
<body style="background:${BRAND.ink50};margin:0;padding:32px 16px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
  <table role="presentation" width="100%" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
    <tr>
      <td style="background:${BRAND.ink900};padding:24px 32px;">
        <span style="color:#ffffff;font-size:20px;font-weight:700;">${BRAND.name}</span>
      </td>
    </tr>
    <tr>
      <td style="padding:32px;">
        <p style="margin:0 0 16px;color:#131B2E;font-size:16px;line-height:1.5;">
          Click below to sign in to <strong>${host}</strong>.
        </p>
        <a href="${url}"
           style="display:inline-block;background:${BRAND.accent};color:#ffffff;text-decoration:none;
                  padding:12px 24px;border-radius:8px;font-weight:600;font-size:15px;">
          Sign in
        </a>
        <p style="margin:24px 0 0;color:#6B7FA3;font-size:13px;line-height:1.5;">
          This link expires shortly and can only be used once. If you didn't request
          this, you can safely ignore this email.
        </p>
      </td>
    </tr>
  </table>
</body>`;
}
