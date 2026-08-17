import { PrismaClient } from "@/generated/prisma/client";
import { PrismaMariaDb } from "@prisma/adapter-mariadb";

// Prisma 7 requires a driver adapter for the standard SQL workflow — see
// prisma/schema.prisma and the plan's "ORM / DB" decision (MySQL/MariaDB,
// no root VPS access). Reused as a singleton across hot reloads in dev so
// `next dev` doesn't exhaust the connection pool on every file save.

const globalForPrisma = globalThis as unknown as {
  prisma: PrismaClient | undefined;
};

function createPrismaClient() {
  // PrismaMariaDb accepts a mariadb.PoolConfig object or a plain connection
  // string; pool sizing is set via ?connectionLimit=N in DATABASE_URL rather
  // than a separate option (see @prisma/adapter-mariadb's constructor).
  const adapter = new PrismaMariaDb(process.env.DATABASE_URL!);

  return new PrismaClient({ adapter });
}

export const db = globalForPrisma.prisma ?? createPrismaClient();

if (process.env.NODE_ENV !== "production") {
  globalForPrisma.prisma = db;
}
