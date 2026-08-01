# ThesisApps deployment

Production deployment is intentionally gated by an exact Git commit SHA.
Pushing a branch does not grant permission to deploy it.

## Persistent server state

The following paths must never be replaced by Git:

- `/home/thesisapp/public_html/.env`
- `/home/thesisapp/public_html/storage`
- `/home/thesisapp/public_html/public/gambar`
- `/home/thesisapp/public_html/public/dokumen`
- `/home/thesisapp/public_html/public/public/dokumen`
- `/home/thesisapp/public_html/public/mobile/controller/simta/uploadedFiles`
- `/home/thesisapp/public_html/dokumen`
- `/home/thesisapp/shared/thesisapps/official-assets`

Production `.env` must include:

```dotenv
OFFICIAL_ASSET_PATH=/home/thesisapp/shared/thesisapps/official-assets
```

## Approval and deployment

1. Verify the commit locally and on GitHub.
2. Back up production and test the release against staging.
3. Write only the approved SHA to:
   `/home/thesisapp/shared/thesisapps/deploy-approved-commit`.
4. Trigger cPanel Git deployment for that exact commit.
5. Verify login, dashboard, documents, image uploads, and Laravel logs.

The approval file is removed after a successful deployment. A different commit
cannot reuse an earlier approval.

Source files are copied atomically and verified by SHA-256. The deployment
manifest at `/home/thesisapp/shared/thesisapps/managed-files.json` controls
which source files may be removed on a later release. Files outside that
manifest, including runtime exclusions, are never deleted by deployment.

## SSH deployment from the local repository

The local SSH client should define a `thesisapps-production` host alias with a
dedicated key. The server must authorize only the public key; the private key
must remain on the local computer.

After the hosting provider enables external SSH for the account, deploy the
exact `origin/main` commit with one command:

```bash
scripts/deploy-production-ssh.sh
```

Inspect the selected host and SHA without contacting the server:

```bash
scripts/deploy-production-ssh.sh --dry-run
```

The helper refuses a dirty worktree, refetches the server checkout, writes the
exact SHA approval, and then calls the same guarded cPanel deployment script.

## Rollback

Before source synchronization, the deployment script copies the current source
to `/home/thesisapp/shared/thesisapps/deploy-backups/<timestamp>-<commit>`.
Runtime directories are preserved throughout deployment and rollback.

To restore an earlier source release after SSH has been enabled, use the full
SHA of an ancestor of `origin/main`:

```bash
scripts/deploy-production-ssh.sh --rollback FULL_COMMIT_SHA
```

Rollback through this command is a new guarded deployment: it first creates a
backup of the current production source and never replaces runtime uploads.
It does not roll back the database.

Database migrations are never run automatically. Any required migration must
be reviewed, backed up, and approved as a separate operation.
