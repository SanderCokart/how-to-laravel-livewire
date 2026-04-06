---
agent: agent
---
Follow these steps in order. Do not skip steps or reorder them.

## Step 1: Gather changes

Run `git status` and `git diff` to see all changes.

## Step 2: Plan commits

Split changes into logical commits (one per intent). Order: refactor first, then features/fixes, then docs/chore. Use the project’s commit format for each message (see reference below).

- Group by intent; prefer smaller, focused commits when changes are unrelated.
- If everything is one logical change, use one commit.

## Step 3: Show a readable summary

Output a **nicely formatted summary** so the user can review the commits before copying or running. For each commit, show:

- The commit message (readable, not escaped).
- The files included in that commit (paths only).

Example format:

- **FEAT | Add back-to-top button** — `back-to-top-button.tsx`
- **REFACTOR | Simplify article page layout** — `page.tsx`

Keep this short and scannable. No code block in this step.

## Step 4: Output the copy-paste code block

**Directly under the summary**, output **exactly one** copy-pasteable bash code block. It must contain:

- For each commit: `git add <paths>` then `git commit -m "<message>"`, chained with ` && `.
- Line continuation with ` \` if you use multiple lines.
- End with `git push`.

Example shape (replace with real paths and messages):

```bash
git add path/to/file1 path/to/file2 && git commit -m "FEAT | Add thing" && \
git add path/to/file3 && git commit -m "REFACTOR | Simplify other" && \
git push
```

- One `git add` per commit, only the paths for that commit.
- Use the project’s commit format (e.g. `#123 | FEAT | Add thing` if there’s an issue; add `! ` prefix for breaking changes). For multi-line bodies, use `-m "TAG | Summary" -m "- Bullet one" -m "- Bullet two"` or a single `-m` with the full message.
- Quote messages so they paste safely; escape internal double quotes or use single-quoted `-m 'message'`.
- Include `git push` in the block.

## Step 5: Ask Y or N

Immediately after the code block, ask exactly:

**Run this? (Y / N)**

## Step 6: Act on answer

- **Y** — Run the same command sequence in the terminal (the exact commands from the code block).
- **N** — Do nothing. Do not run any git commands.

---

## Commit message format (reference)

First line: optional `! ` (breaking change), optional `#<issue> | ` (from branch), then `TAG | Short description`. Separate sections with ` | `.

**Tags (capitalized):** FEAT, FIX, DOCS, FORMAT, REFACTOR, PERF, TEST, DOCKER, CI, CHORE, REVERT.

**Optional body:** Empty line, then bullet points with a literal, direct description.

Example:

```
! | #123 | FEAT | Add new feature

- Add new feature
- Add new test
```
