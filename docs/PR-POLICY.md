# Pull request policy

Enforced by the `pr-policy` check (`.github/workflows/pr-policy.yml`).

## Rules

1. **Every PR links an issue.** Put `Closes #N` or `Refs #N` in the PR description.
2. **Branch names** are `feature/{issue_id}` (new features), `dependabot/{issue_id}` (Dependabot/dependency updates), or `hotfix/{issue_id}` (hotfixes). `{issue_id}` must equal the linked issue number, e.g. `feature/123` with `Closes #123`.
3. **`feature/*` and `dependabot/*` PRs target `staging`.**
4. **`hotfix/*` PRs target `staging`**, unless it is a genuine production incident: then they may target `master` and must carry the `production-incident` label.
5. **PR titles** are exactly the branch name, e.g. `feature/123`, `dependabot/181`, or `hotfix/45`.
6. **Back-merge:** a hotfix merged into `master` must be merged back into `staging` straight away so the branches do not diverge. This is a manual step (open a `staging` <- `master` PR); automation is optional.

Release and sync PRs from this repo's own `staging` or `master` are exempt from rules 1-5: the `staging` -> `master` release, the `master` -> `staging` back-merge, and `staging` -> `feature/N` updates.

Automated Dependabot PRs authored by `dependabot[bot]` from this repository on a generated `dependabot/<ecosystem>/...` branch targeting `staging` are exempt from the branch-name and exact-title rules (rules 2 and 5). They must still link an issue (rule 1) and target `staging`: open an issue for the update and add `Closes #N` to the Dependabot PR's description, which re-runs the check. Ordinary contributors or bots using the `dependabot/` prefix must use `dependabot/{issue_id}` and follow rules 1-5.

## Issues and auto-close

GitHub only auto-closes issues (`Closes #N`) when the PR is merged into the **default branch**, which is `master`. Issues for feature PRs merged into `staging` therefore stay open until the release PR lands on `master`. This is expected; close them at release or reference them in the release PR.

## Not covered by this change

Making `pr-policy` a required status check is a repository setting/ruleset and is configured separately.
