# Production deployment

The preferred deployment path uses the cPanel UAPI over HTTPS and does not
require an interactive cPanel browser session.

```bash
scripts/deploy-production.sh --dry-run
scripts/deploy-production.sh
```

The command requires a clean `main` worktree whose HEAD matches `origin/main`.
It then updates the cPanel-managed repository, writes the exact approved commit,
starts the existing guarded deployment, waits for completion, and verifies the
deployed SHA.

The production API token is stored outside the repository in the macOS
Keychain service `thesisapps-cpanel-api`. It can also be supplied temporarily
through `THESISAPPS_CPANEL_API_TOKEN` for a non-macOS runner. Never write the
token to `.env` or commit it to Git.

Database migrations remain opt-in. To run a specific migration with the same
guarded deployment, approve its filename explicitly:

```bash
scripts/deploy-production.sh \
  --migration 2026_08_21_100000_create_master_pembayaran_honorarium_jenis_tugas_akhir_table.php
```

Only migration files approved for the exact deployment commit are staged and
run while the application is in maintenance mode. A normal deployment never
runs pending migrations automatically.

Use `scripts/deploy-production-ssh.sh` only as a fallback when SSH port 1988 is
reachable. Rollback remains intentionally available through the guarded SSH
script or the cPanel interface.
