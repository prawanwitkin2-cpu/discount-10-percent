# Antigravity / Codex Project Operating Rules

## Purpose

Support systematic software development in Antigravity and Codex with:

- controlled implementation
- secure development
- responsive UI behavior
- consistent UI/UX
- evidence-based debugging
- scope control
- structured project planning
- minimal unrelated code changes

---

# Project documentation roles

## AGENTS.md

Use for:

- high-level development rules
- workflow constraints
- security principles
- responsive principles
- UI/UX principles
- debugging discipline
- scope control
- agent operating behavior

This file should remain concise and relatively stable.

---

## implementation_plan.md

Use for:

- roadmap
- architecture planning
- long-term technical direction
- planned features
- technical debt
- security improvement plans
- responsive strategy
- UI/UX improvement plans
- infrastructure strategy
- future development planning

Do not use this file for daily implementation progress.

---

## project_status.md

Use for:

- current focus
- latest progress
- files modified
- blockers
- testing status
- debugging breadcrumbs
- post-fix records
- immediate next steps

Update this file frequently during development.

---

# Core operating rules

- Always review `implementation_plan.md` before making development changes.
- Always review `project_status.md` before continuing existing work.
- Keep all changes aligned with the active task and scope.
- Prefer small, traceable, reversible changes.
- Do not modify unrelated files or unrelated code paths.
- Do not refactor unrelated code.
- Do not invent missing project details.
- If information is unavailable, state `ไม่มีข้อมูล`.
- Before editing files, identify:
  - why the file is related
  - what behavior may be affected
  - what must remain unchanged
- Update `project_status.md` after meaningful progress.
- Update `implementation_plan.md` only when long-term planning or architecture changes.

---

# Development workflow

## Standard workflow

1. Read the request carefully.
2. Review:
   - `AGENTS.md`
   - `implementation_plan.md`
   - `project_status.md`
3. Identify:
   - actual goal
   - affected files
   - protected files
   - risks
4. Determine whether:
   - debugging workflow is required
   - scrutinize workflow is required
5. Implement only the required change.
6. Review diffs for unrelated edits.
7. Update documentation.

---

# Scope control rules

Before implementation:

- identify directly related files only
- identify behaviors that must remain unchanged
- identify security impact
- identify responsive impact
- identify UI/UX impact
- identify Cloudflare compatibility impact where relevant

Do not:

- change unrelated logic
- rewrite unrelated UI
- rename unrelated files
- modify backend logic for frontend-only tasks
- modify frontend logic for backend-only tasks

---

# Debugging discipline

Use this workflow for all bug fixing.

## Required debugging process

1. Reproduce the issue.
2. Identify:
   - expected behavior
   - actual behavior
3. Trace the actual failing path.
4. Create multiple hypotheses.
5. Attempt to disprove the leading hypothesis.
6. Collect evidence from:
   - source files
   - logs
   - UI behavior
   - API responses
   - test results
7. Record debugging breadcrumbs in `project_status.md`.
8. Apply the smallest safe fix.
9. Validate the result.
10. Record the fix summary.

## Do not

- guess blindly
- patch randomly
- refactor unrelated code during debugging
- claim root cause without evidence
- modify unrelated files while debugging

---

# Scrutinize workflow

Use this workflow before:

- broad UI changes
- architecture changes
- responsive redesign
- theme changes
- security changes
- project structure changes
- large refactors

## Scrutinize steps

1. State the real goal in one sentence.
2. Determine whether a smaller change can solve the issue.
3. Trace the actual code path.
4. Verify the proposed change solves the real issue.
5. Identify:
   - side effects
   - edge cases
   - security impact
   - responsive impact
   - accessibility impact
6. Identify files that must not change.
7. Only proceed with evidence.

If evidence is missing, state `ไม่มีข้อมูล`.

---

# Coding principles

## General principles

- prioritize readability
- keep changes minimal
- follow existing project patterns
- avoid unnecessary dependencies
- preserve existing behavior unless explicitly changing it
- avoid hardcoded secrets
- validate user input
- avoid duplicate logic

## Documentation

Update documentation when changing:

- workflow
- architecture
- security behavior
- UI behavior
- responsive behavior
- infrastructure behavior

---

# UI/UX principles

Focus on:

- usability
- consistency
- readability
- interaction clarity
- accessibility
- predictable navigation
- visual hierarchy

## Requirements

- maintain consistent spacing and typography
- maintain clear hover/active/focus states
- ensure readable contrast
- preserve layout consistency
- avoid decorative redesigns unrelated to the task

---

# Responsive principles

Focus on:

- layout behavior
- breakpoint behavior
- overflow handling
- touch usability
- mobile readability

## Requirements

- mobile-first where practical
- avoid horizontal scrolling
- preserve desktop behavior
- ensure forms/buttons remain usable on touch devices
- ensure cards/tables/layouts remain readable

---

# Theme consistency rules

## Requirements

- dark mode and light mode must both render correctly
- text must remain readable
- cards/tables/forms/buttons must have proper contrast
- sidebar may remain permanently dark if required by project design
- avoid mixed inconsistent theme states

## Do not

- apply random inline colors
- override unrelated theme variables
- redesign unrelated pages during theme fixes

---

# Secure by Design principles

Focus on:

- architecture-level security
- least privilege
- secure defaults
- protected secrets
- safe error handling
- protected uploads
- secure session handling

## Requirements

- validate input server-side
- enforce authorization server-side
- avoid exposing internal errors
- avoid exposing secrets
- avoid unsafe uploads
- log safely

---

# OWASP implementation guidance

Apply OWASP protections for:

- SQL injection
- XSS
- CSRF
- SSRF
- authentication
- authorization
- file upload security
- session security
- security misconfiguration
- dependency risks

---

# Secure PHP project structure guidance

## Recommended structure

```text
secure-php-project/
├── app/
├── config/
├── public/
├── routes/
├── storage/
├── resources/
├── database/
├── tests/
├── vendor/
├── .env
└── composer.json
```

## Rules

- only `public/` should be web accessible
- never expose:
  - `.env`
  - `storage/`
  - `config/`
  - `vendor/`
  - `database/`
- do not modify `vendor/` directly
- use prepared statements
- validate uploads
- secure sessions/cookies
- keep secrets outside public paths

---

# Cloudflare compatibility principles

Ensure compatibility with:

- reverse proxy behavior
- CDN behavior
- WAF behavior
- rate limiting
- session handling behind proxies
- upload handling
- CORS behavior

## Do not

- create infinite retry loops
- expose origin details
- hardcode Cloudflare credentials
- trigger unnecessary bot-like behavior

---

# Unused file review workflow

When reviewing unused files:

1. Do not delete immediately.
2. Identify:
   - unused
   - obsolete
   - duplicate
   - temporary
   - generated
   - backup files
3. Provide:
   - path
   - evidence
   - deletion risk
   - recommendation
4. Only recommend deletion with evidence.
5. Never remove files automatically without approval.

---

# Post-fix records

For important fixes, record:

- symptom
- root cause
- files changed
- fix summary
- validation performed
- remaining risks
- follow-up actions

Use for:

- security issues
- auth issues
- responsive issues
- theme issues
- layout issues
- Cloudflare issues
- multi-page bugs

---

# Prohibited behavior

Do not:

- modify unrelated code
- perform broad refactors without approval
- rename unrelated files
- expose secrets
- expose internal errors
- trust client-side validation alone
- disable security controls for convenience
- create unnecessary dependencies
- modify `vendor/`
- expose protected directories publicly
- delete files without approval
- redesign unrelated UI
- break responsive behavior
- break theme consistency

---

# Final checklist

Before responding:

- confirm scope was respected
- confirm unrelated code was not changed
- confirm security impact was considered
- confirm responsive impact was considered
- confirm UI/UX consistency was considered
- confirm theme consistency was considered
- confirm Cloudflare compatibility was considered where relevant
- confirm documentation was updated
- confirm blockers or risks were identified
- confirm missing information is stated as `ไม่มีข้อมูล`
