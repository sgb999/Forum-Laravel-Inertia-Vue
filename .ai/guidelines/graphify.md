# Graphify

This project has a graphify knowledge graph at `graphify-out/`. Before using Read, Grep, Glob, or the shell to explore the codebase, run graphify first:

- `graphify query "<question>"` for a scoped subgraph on any codebase or architecture question
- `graphify path "<A>" "<B>"` for the dependency path between two symbols
- `graphify explain "<concept>"` for every node related to a concept

Apply this to every subagent that explores code. Include the rule in the subagent prompt. Do not skip graphify because files are already known or because a plan is already being executed. The graph surfaces cross-file dependencies and inferred edges that grep and Read cannot find.

Use Read, Grep, or Glob directly only after graphify has oriented you and you need specific lines, or when `graphify-out/graph.json` does not exist yet.

- If `graphify-out/wiki/index.md` exists, navigate it instead of reading raw files.
- Read `graphify-out/GRAPH_REPORT.md` only for a broad architecture review when query, path, and explain are not enough.
- After modifying code files, run `graphify update .` so the graph stays current. That update is AST-only.
