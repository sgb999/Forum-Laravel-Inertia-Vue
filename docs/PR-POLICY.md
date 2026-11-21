# Pull request policy

Enforced by the `pr-policy` check (`.github/workflows/pr-policy.yml`).

## Rules

1. **Every PR links an issue.** Put `Closes #N` or `Refs #N` in the PR description.
2. **Branch names** are `feature/{issue_id}` (new features) or `hotfix/{issue_id}` (hotfixes). `{issue_id}` must equal the linked issue number, e.g. `feature/123` with `Closes #123`.
3. **`feature/*` PRs target `staging`.**
4. **`hotfix/*` PRs target `staging`**, unless it is a genuine production incident: then they may target `master` and must carry the `production-incident` label.
5. **Back-merge:** a hotfix merged into `master` must be merged back into `staging` straight away so the branches do not diverge. This is a manual step (open a `staging` <- `master` PR); automation is optional.

The release PR from `staging` into `master` is exempt from rules 1-4.

## Issues and auto-close

GitHub only auto-closes issues (`Closes #N`) when the PR is merged into the **default branch**, which is `master`. Issues for feature PRs merged into `staging` therefore stay open until the release PR lands on `master`. This is expected; close them at release or reference them in the release PR.

## Not covered by this change

Making `pr-policy` a required status check is a repository setting/ruleset and is configured separately.
