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

Use `scripts/deploy-production-ssh.sh` only as a fallback when SSH port 1988 is
reachable. Rollback remains intentionally available through the guarded SSH
script or the cPanel interface.
