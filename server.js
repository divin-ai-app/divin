// Passenger entry point for cPanel's "Setup Node.js App" feature.
//
// A limited-rights cPanel account has no PM2/systemd/custom-Nginx access —
// Apache's mod_passenger starts this file directly and proxies the domain to
// it, injecting PORT (and NODE_ENV) as environment variables. See plan §1/§2
// ("Deployment mechanics note") for why this exists instead of a
// PM2 ecosystem file. Locally, use `npm run dev` / `npm run start` instead —
// this file is only exercised by Passenger in production.
const { createServer } = require("http");
const next = require("next");

const port = Number(process.env.PORT) || 3000;
const dev = process.env.NODE_ENV !== "production";

const app = next({ dev });
const handle = app.getRequestHandler();

app
  .prepare()
  .then(() => {
    createServer((req, res) => {
      handle(req, res);
    }).listen(port, () => {
      console.log(`> divin.ai ready on port ${port} (dev=${dev})`);
    });
  })
  .catch((err) => {
    console.error("Failed to start Next.js under Passenger:", err);
    process.exit(1);
  });
