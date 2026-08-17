import { defineConfig, globalIgnores } from "eslint/config";
import nextVitals from "eslint-config-next/core-web-vitals";
import nextTs from "eslint-config-next/typescript";

const eslintConfig = defineConfig([
  ...nextVitals,
  ...nextTs,
  // Override default ignores of eslint-config-next.
  globalIgnores([
    // Default ignores of eslint-config-next:
    ".next/**",
    "out/**",
    "build/**",
    "next-env.d.ts",
    // Prisma's generated client output (see prisma/schema.prisma).
    "src/generated/**",
  ]),
  {
    // server.js is the cPanel Passenger entry point (see plan §2's
    // "Deployment mechanics note") — plain CommonJS by necessity, outside
    // the Next.js app bundle, so the TS import-style rules don't apply.
    files: ["server.js"],
    rules: {
      "@typescript-eslint/no-require-imports": "off",
    },
  },
]);

export default eslintConfig;
