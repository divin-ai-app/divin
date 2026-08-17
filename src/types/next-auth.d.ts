import type { DefaultSession } from "next-auth";
import type { Role } from "@/generated/prisma/enums";

// Module augmentation: extend Auth.js's default Session/User shape with the
// fields our schema.prisma User model actually has (see prisma/schema.prisma).
declare module "next-auth" {
  interface Session {
    user: {
      id: string;
      role: Role;
    } & DefaultSession["user"];
  }
}

// AdapterUser (passed into the `session` callback in src/lib/auth.ts) is
// declared against @auth/core's own User type, not next-auth's re-export —
// augment it separately so `user.role` type-checks there too.
declare module "@auth/core/types" {
  interface User {
    role?: Role;
  }
}
