import NextAuth from "next-auth";
import Nodemailer from "next-auth/providers/nodemailer";
import { PrismaAdapter } from "@auth/prisma-adapter";

import { db } from "@/lib/db";
import { sendMagicLinkEmail } from "@/lib/email";

// Account login only. This is deliberately separate from claim-flow ownership
// verification (proving control of a *business*) — see src/lib/claim-otp.ts,
// built in Phase 3. See plan §4 "Core flows" for why the two are sequenced.
export const { handlers, auth, signIn, signOut } = NextAuth({
  adapter: PrismaAdapter(db),
  session: { strategy: "database" },
  pages: {
    signIn: "/login",
    verifyRequest: "/verify-request",
  },
  providers: [
    Nodemailer({
      server: {
        host: process.env.EMAIL_SERVER_HOST,
        port: Number(process.env.EMAIL_SERVER_PORT ?? 587),
        auth: process.env.EMAIL_SERVER_USER
          ? {
              user: process.env.EMAIL_SERVER_USER,
              pass: process.env.EMAIL_SERVER_PASSWORD,
            }
          : undefined,
        // Laragon's Mailpit (local dev) has no TLS listener on 1025.
        secure: false,
      },
      from: process.env.EMAIL_FROM,
      sendVerificationRequest: sendMagicLinkEmail,
    }),
  ],
  callbacks: {
    async session({ session, user }) {
      if (session.user) {
        session.user.id = user.id;
        session.user.role = user.role ?? "OWNER";
      }
      return session;
    },
  },
});
