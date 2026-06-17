# EFGTrack — Goals & Performance Module
**User Guide**

**Version:** 1.0  
**Last updated:** June 2026  
**Audience:** Associates, CFMs, team leaders, and agency owners using EFGTrack  
**Hub URL:** `/goals` (sidebar: **Goals & Performance**)

---

## Table of contents

1. [What this module does](#1-what-this-module-does)
2. [Who can access what](#2-who-can-access-what)
3. [Module map — pages and URLs](#3-module-map--pages-and-urls)
4. [Recommended workflows](#4-recommended-workflows)
5. [Goals hub (`/goals`)](#5-goals-hub-goals)
6. [Performance Planner (`/goals/plan`)](#6-performance-planner-goalsplan)
7. [Success Blueprint (`/goals/blueprint/{id}`)](#7-success-blueprint-goalsblueprintid)
8. [Quick Goal wizard (`/goals/create`)](#8-quick-goal-wizard-goalscreate)
9. [My Goals — views and filters](#9-my-goals--views-and-filters)
10. [Activity Scorecard (`/goals/scorecard`)](#10-activity-scorecard-goalsscorecard)
11. [What-If Calculator (`/goals/what-if`)](#11-what-if-calculator-goalswhat-if)
12. [Goal Reports (`/goals/reports`)](#12-goal-reports-goalsreports)
13. [Team Goals (`/goals/team`)](#13-team-goals-goalsteam)
14. [CFM Coaching (`/goals/coaching`)](#14-cfm-coaching-goalscoaching)
15. [Key concepts](#15-key-concepts)
16. [Automated progress and KPI sync](#16-automated-progress-and-kpi-sync)
17. [Alerts, forecasts, and coaching insights](#17-alerts-forecasts-and-coaching-insights)
18. [Achievements and badges](#18-achievements-and-badges)
19. [Notifications and reminders](#19-notifications-and-reminders)
20. [Tips and best practices](#20-tips-and-best-practices)
21. [Troubleshooting](#21-troubleshooting)
22. [Appendix](#22-appendix)

---

## 1. What this module does

The **Goals & Performance** module helps you plan, track, and coach business performance in EFGTrack. It connects your daily activities (prospecting, FNAs, presentations, recruiting) to larger outcomes (production, income, rank advancement).

At a high level, the module provides:

| Capability | What it means for you |
|---|---|
| **Goal tracking** | Set targets by category (recruiting, production, FAP, licensing, etc.) and monitor progress |
| **Activity-based planning** | Start with an income or production target and work backward to daily contacts and appointments |
| **Success Blueprints** | A linked set of outcome + activity goals created from the Performance Planner |
| **KPI automation** | Progress updates from Prospects, FNA, production, training, and downline data |
| **Scorecard** | Weekly/daily view of whether you are hitting activity targets |
| **What-If simulations** | Test targets without creating goals |
| **Team visibility** | Leaders see downline goal progress and off-track items |
| **CFM coaching** | Mentors review trainee goals, leave notes, and see deficiency alerts |
| **Reports** | Download or email PDF performance summaries |

---

## 2. Who can access what

Access is controlled by permissions assigned to your role.

| Permission | Typical roles | What you can do |
|---|---|---|
| `manage goals` | Member, CFM, leaders | Full personal goals hub, planner, scorecard, what-if, reports, quick goal wizard |
| `view team goals` | CFM, team leaders, agency owner | View **Team Goals** for your downline |
| `coach goals` | CFM, mentors with coaching access | **CFM Coaching** — trainee goals, coach notes, voice notes |

If a menu item or page returns **403 Forbidden**, your role does not include the required permission. Contact your agency administrator.

---

## 3. Module map — pages and URLs

| Page | URL | Purpose |
|---|---|---|
| **Goals hub** | `/goals` | Dashboard, insights, and all your goals |
| **Performance Planner** | `/goals/plan` | Build a Success Blueprint from a top-level target |
| **Quick Goal** | `/goals/create` | 9-step wizard for a single goal |
| **Success Blueprint** | `/goals/blueprint/{id}` | View funnel progress for a created plan |
| **Activity Scorecard** | `/goals/scorecard` | Daily/weekly/monthly activity vs targets |
| **What-If Calculator** | `/goals/what-if` | Simulate funnels without saving goals |
| **Goal Reports** | `/goals/reports` | PDF download and email summaries |
| **Team Goals** | `/goals/team` | Downline goal visibility (leaders) |
| **CFM Coaching** | `/goals/coaching` | Trainee coaching workspace (CFMs) |

Quick links on the hub header: **Performance Planner**, **Quick Goal**, **Scorecard**, **What-If**, **Reports**, **Team Goals**, **CFM Coaching** (last two depend on permissions).

---

## 4. Recommended workflows

### New associate — first 30 days

1. Open **Performance Planner** and choose **Recruiting Goal** or **Production Goal** based on your focus.
2. Enter a realistic annual target and review the calculated funnel (contacts → invitations → appointments → presentations → …).
3. Click **Create Success Blueprint** — this creates linked goals automatically.
4. Each week, open **Activity Scorecard** (weekly view) and **Sync KPIs** on the hub.
5. Use **What-If** to adjust targets before changing your plan.

### Established producer — income planning

1. Use **Performance Planner** → **Annual Income Goal**.
2. Enter your desired annual income (e.g. `$100,000`).
3. Review reverse-engineered production, applications, FNAs, and daily contacts.
4. Create the blueprint and track progress on the **Success Blueprint** page.
5. Run **Goal Reports** monthly for a PDF summary.

### CFM — weekly coaching rhythm

1. Open **Team Goals** → filter **Needs attention** for off-track goals.
2. Go to **CFM Coaching** → filter by trainee.
3. Review alerts (no prospecting, pace behind, deadline approaching).
4. Leave a **coach note** or **voice note** on specific goals.
5. Follow up in your next mentor session using milestone status on each goal card.

### Team leader — pipeline review

1. Open **Team Goals** with scope **Full downline** or **Direct recruits**.
2. Switch view to **By member** for per-person rollup.
3. Use search and category filters to focus (e.g. production goals only).
4. Click **Review now** when the off-track banner appears.

---

## 5. Goals hub (`/goals`)

The hub is your home base. It has four main sections, top to bottom.

### 5.1 Header and quick actions

The navy/gold header explains the module and links to all sub-pages. Use **Performance Planner** for full funnel planning; use **Quick Goal** for one-off targets.

### 5.2 Performance insights panel

Shows three types of intelligence:

- **Active blueprint** — If you created a plan via the Performance Planner, a card links to your latest **Success Blueprint**.
- **Performance forecast** — Projected completion % for active goals (based on current pace). Requires goals with measurable progress.
- **Coaching alerts** — Warnings such as “no prospecting in 7 days,” “goal behind pace,” or “deadline approaching.”

> **Tip:** If forecasts are empty, create a Performance Plan or add goals with deadlines and metric keys.

### 5.3 Dashboard summary

Six stat cards:

| Card | Meaning |
|---|---|
| Total Goals | All goals in your account |
| Active | Currently in progress |
| Completed | Reached 100% or marked complete |
| Off Track | Behind expected pace |
| Completion % | Share of goals completed |
| Current Streak | Consecutive days with goal-related activity |

Below the cards:

- **Monthly Progress** — Bar chart of goals created vs completed per month.
- **AI Coaching** — Rule-based suggestions (e.g. focus areas when behind). Shown when the system detects underperformance.
- **Progress by Category** — Average progress per goal category.
- **Recent Achievements** — Badges earned (see [§18](#18-achievements-and-badges)).

### 5.4 My Goals

Your full goal list with filters and multiple view modes (see [§9](#9-my-goals--views-and-filters)).

---

## 6. Performance Planner (`/goals/plan`)

The Performance Planner reverse-engineers a top-level target into a full funnel of linked goals.

### Step 1 — Choose planning type and target

| Planning type | Use when you want to… | Example target |
|---|---|---|
| **Annual Income Goal** | Plan from desired take-home income | `$100,000` annual income |
| **Production Goal** | Plan from premium/production target | `$250,000` annual production |
| **Recruiting Goal** | Plan from recruit count | `12` recruits per year |
| **Rank Advancement Goal** | Roadmap to SM, ED, or SED | Select rank + completion target |

**Actions on Step 1:**

1. Click a planning type card to select it.
2. Enter **Target value**.
3. For rank goals, choose **Target rank** (SM, ED, SED).
4. Optionally edit **Plan name** and **Deadline** (defaults to end of current year).
5. Click **Calculate activity funnel**.

The system uses your personal conversion rates (when available) and default industry rates from configuration to compute required activities at each stage.

### Step 2 — Review funnel and create blueprint

You will see a vertical funnel listing each stage:

- **Outcome goals** (top) — Income, production, recruits, rank requirements
- **Activity goals** (bottom) — Daily contacts, invitations, appointments, presentations, FNAs, applications

Each stage shows **Annual**, **Monthly**, **Weekly**, and **Daily** targets.

**Actions on Step 2:**

- **Back** — Return to Step 1 to change inputs.
- **Create Success Blueprint** — Saves the plan, creates linked goals, and redirects you to the blueprint page.

After creation, a green confirmation appears on the hub: *Performance plan "…" created with N linked goals.*

---

## 7. Success Blueprint (`/goals/blueprint/{id}`)

A Success Blueprint is the living view of a Performance Plan.

### What you see

- **Header** — Plan name, planning type, root target, and overall **projected completion %**.
- **Recommended actions** — Shown when pace is behind (e.g. increase weekly contacts).
- **Goal dependency funnel** — Each stage as a card with:
  - Progress % (actual vs target)
  - Projected % (forecast at current pace)
  - Pace status (on track / behind)
  - Target, actual, monthly, and daily figures

### How to use it

1. Open from the hub **Active blueprint** card or after creating a plan.
2. Identify the **lowest funnel stage** with red/amber pace — that is usually your bottleneck activity.
3. Focus weekly coaching and scorecard reviews on that activity.
4. Return after **Sync KPIs** on the hub to see updated progress.

> Only you can view your own blueprint (`user_id` must match your account).

---

## 8. Quick Goal wizard (`/goals/create`)

Use the Quick Goal wizard when you need **one goal** without a full funnel plan — for example, a personal development target or a simple monthly recruiting number.

### The 9 steps

| Step | What you enter |
|---|---|
| 1. Category | Choose from 12 categories (recruiting, production, prospecting, FAP, licensing, etc.). Optional: apply a **quick template**. |
| 2. Goal name | Name, description, hierarchy level (vision → daily), optional parent goal |
| 3. Target value | Numeric target |
| 4. Measurement type | Number, currency, percentage, or completion |
| 5. Deadline | Start date and deadline |
| 6. Milestones | Optional sub-targets with due dates |
| 7. Accountability partner | Sponsor or mentor (if assigned in your profile) |
| 8. Notifications | Email, in-app, weekly reminders |
| 9. Review & create | Confirm and save |

### SMART score

A **SMART %** badge updates as you type. It evaluates whether your goal is Specific, Measurable, Achievable, Relevant, and Time-bound. Aim for 80%+ before saving.

### Templates

When you select a category, **quick templates** may appear (e.g. pre-filled recruiting goals). Click a template to auto-fill fields, then adjust as needed.

### Automated vs manual goals

If you select a **metric key** tied to EFGTrack data (e.g. `applications`, `fna_completed`, `recruits`), progress can sync automatically when you click **Sync KPIs** on the hub. Manual goals (e.g. income without integration) require you to update progress yourself or use production entries.

---

## 9. My Goals — views and filters

The **My Goals** panel on the hub supports rich filtering and six view modes.

### Filters

- **Search** — Matches goal name or description.
- **Status** — Draft, active, completed, off track, paused, cancelled, or all.
- **Category** — Any active goal category.

### View modes

| Mode | Best for |
|---|---|
| **List** | Spreadsheet-style table with progress, deadline, SMART score |
| **Cards** | Visual cards with progress bars (default) |
| **Timeline** | Goals on a horizontal timeline by start/deadline |
| **Progress** | Progress-focused layout with emphasis on % complete |
| **Calendar** | Goals, deadlines, and milestones on a month grid |
| **Kanban** | Columns: Active, Off Track, Completed |

### Sync KPIs

Click **Sync KPIs** to pull latest actuals from connected modules (Prospects, FNA, production, training, downline) into goals that have a `metric_key`. A confirmation flash message appears when complete.

> Run **Sync KPIs** after logging prospect activity, completing FNAs, or entering production — otherwise automated goals may look stale.

---

## 10. Activity Scorecard (`/goals/scorecard`)

The scorecard answers: *“Am I doing enough activity this period?”*

### Period selector

Switch between **Daily**, **Weekly**, **Monthly**, **Quarterly**, and **Annual**. The label updates (e.g. “Week of Jun 16”).

### Overall activity score

A single percentage — the average of all tracked activities for the period.

### Activity cards

Each card shows:

| Activity | Typical source |
|---|---|
| New Prospects | Prospect module |
| Calls / Contacts | Prospect module |
| Follow-Ups | Prospect module |
| Appointments | Prospect / calendar |
| Presentations | Prospect module |
| FNAs | FNA module |
| Applications | Prospect module |
| Invitations | Prospect module |
| Recruits | Downline / recruiting |

Each card displays **actual / target** and a progress bar.

### How targets are determined

1. **Primary:** Sum of `goal_activity_targets` from your active Performance Plan goals.
2. **Fallback:** Sensible weekly defaults (e.g. 25 contacts/week) if no plan exists.

> For the most accurate scorecard, create a **Performance Plan** first so daily/weekly targets align with your income or production goal.

---

## 11. What-If Calculator (`/goals/what-if`)

The What-If Calculator lets you **simulate** a target without creating goals or a blueprint.

### How to use

1. Select **Goal type** (income, production, recruiting, rank).
2. Enter **Target value**.
3. For rank, select **Target rank**.
4. Click **Run simulation**.

### Results

- **Summary cards** — Key annual/monthly/daily figures (e.g. required applications, daily contacts).
- **Stage table** — Full funnel with annual, monthly, and daily columns.

Simulations are saved to your history (`goal_simulations` table) for reference but do not create goals.

**When to use What-If vs Performance Planner:**

| Tool | Creates goals? | Best use |
|---|---|---|
| What-If | No | Exploring “what if I target $150K?” before committing |
| Performance Planner | Yes (blueprint + linked goals) | Official plan for the year |

---

## 12. Goal Reports (`/goals/reports`)

Generate PDF summaries of goal performance for a reporting period.

### Report period

| Option | Date range |
|---|---|
| Last week | Previous calendar week |
| Last month | Previous calendar month |
| Last quarter | Previous calendar quarter |
| Last year | Previous calendar year |

The preview updates when you change the period (average progress, goal count, completed, off track, and a goals table).

### Download PDF

Click **Download PDF**. Your browser downloads a letter-size PDF including:

- Summary stats
- Goals table (name, category, progress, status, deadline)
- Category scorecard (if available)
- Achievements earned in the period

### Email report

Click **Email report**. The PDF summary is sent to your account email. You are redirected back with a success message.

> Reports include goals whose start date, deadline, or duration overlaps the selected period.

---

## 13. Team Goals (`/goals/team`)

**Requires:** `view team goals` permission.

### Summary cards

Members with goals, total goals, active, completed, off track, and average progress across your selected scope.

### Off-track banner

When goals need attention, a yellow banner shows the count and a **Review now** button that switches to the **Needs attention** view.

### Scope filter

| Scope | Shows |
|---|---|
| My goals only | Your personal goals within the team view |
| Direct recruits | Goals owned by your direct downline |
| Full downline | Goals for your entire downline tree |

### View modes

| Mode | Description |
|---|---|
| **All goals** | Filterable list with expandable goal details |
| **By member** | Rollup per team member (goal count, avg progress, off-track count) |
| **Needs attention** | Goals that are off track or significantly behind pace |

### Additional filters

- **Member** — Focus on one person.
- **Status** — Active, completed, etc.
- **Category** — Recruiting, production, etc.
- **Search** — Goal name, description, or member name.

### Expandable goal rows

Click a goal to expand milestones, accountability partner, and progress details.

### Trainee section

If you have `coach goals`, a **Trainee goals** section highlights goals for apprentices assigned to you.

---

## 14. CFM Coaching (`/goals/coaching`)

**Requires:** `coach goals` permission.

### Trainee filter

Pills at the top: **All trainees** or individual trainee names.

### Goal cards

Each card shows:

- Trainee name
- Goal name, category, progress %
- First coaching alert for that trainee (if any)
- Milestone checklist
- **Add coach note** button

### Coach note panel

1. Select a goal from the dropdown (or click **Add coach note** on a card).
2. Enter a text note **or** upload a **voice note** (audio file).
3. Click **Save note**.

Notes are stored on the goal and visible per your coaching visibility rules.

### Coaching suggestions

The sidebar lists system-generated suggestions for your trainees (from `GoalCoachingService`).

### Conversion KPIs

For each trainee, the system can surface funnel conversion KPIs (invitation → appointment → presentation rates) to guide coaching conversations.

---

## 15. Key concepts

### Outcome vs activity goals

| Type | Definition | Example |
|---|---|---|
| **Outcome goal** | The result you want | $100,000 annual income, 12 recruits |
| **Activity goal** | The behavior that produces outcomes | 5 daily contacts, 3 FNAs per week |

Performance Plans create **both**, linked in a dependency funnel: outcome at top, activities at bottom.

### Goal hierarchy levels

Goals can be tagged by planning horizon:

`Vision` → `Annual` → `Quarterly` → `Monthly` → `Weekly` → `Daily`

Performance Planner stages are assigned appropriate levels automatically.

### Goal statuses

| Status | Meaning |
|---|---|
| Draft | Created but not started |
| Active | In progress |
| Completed | Target reached |
| Off Track | Behind expected pace |
| Paused | Temporarily suspended |
| Cancelled | No longer pursued |

### Conversion funnel

Activities flow through stages with conversion rates. Example (income funnel):

```
Daily contacts → Prospect conversations → Invitations → Appointments
  → Presentations → FNAs → Applications → Production → Income
```

Rates can be personalized over time via `goal_conversion_rates` (your historical performance). Defaults are used for new users.

### SMART validation

The Quick Goal wizard scores goals against SMART criteria. Strong goals have clear names, measurable targets, realistic deadlines, relevant categories, and defined time bounds.

---

## 16. Automated progress and KPI sync

Many goals can track progress automatically via **metric keys**.

### Metric sources

| Source module | Example metrics |
|---|---|
| **Prospects** | Contacts, invitations, presentations, appointments, applications, recruits |
| **Production** | Annual/monthly premium |
| **FNA** | FNAs completed, FNAs approved |
| **FAP / Licensing / Training** | Completion percentages |
| **Downline** | Direct recruits, team recruits, team production |
| **Calendar** | Mentoring sessions |
| **CFM** | Trainees assigned |
| **Manual** | Monthly/annual income (until payroll integration) |

### How to enable automation

1. When creating a goal, choose a category and select an available **metric key** (Quick Goal wizard).
2. Or use **Performance Planner**, which assigns metric keys to funnel stages automatically.
3. Click **Sync KPIs** on the hub to refresh actuals.

### What Sync KPIs does

- Reads current values from connected services for each goal’s metric and date range.
- Updates `actual_value` and recalculates status (including off track).
- Writes a progress history entry with source `automated`.

---

## 17. Alerts, forecasts, and coaching insights

### Alert types

| Alert | Trigger (default) |
|---|---|
| No prospecting | No contacts in 7 days |
| No presentations | No presentations in 14 days |
| No FNA activity | No FNAs in 14 days |
| No follow-ups | No follow-ups in 7 days |
| Goal behind pace | Projected completion &lt; 80% |
| Goal off track | Status is off track |
| Deadline approaching | Deadline within 7 days and not complete |

Alerts appear on the hub **Coaching alerts** panel and in CFM Coaching for trainees.

### Forecasts

For each active goal, the system projects **projected completion %** at the current pace. Shown on:

- Hub insights panel
- Success Blueprint header and stages
- CFM trainee insights

### Recommended actions

When pace is behind, the system may suggest actions (e.g. increase weekly contacts). These appear on the Success Blueprint and are stored as recommendations.

---

## 18. Achievements and badges

Badges are earned automatically when criteria are met.

| Badge | Level | Criteria (summary) |
|---|---|---|
| First Recruit | Bronze | 1+ direct recruit |
| First Policy | Bronze | 1+ production entry |
| First Licensed Associate | Silver | Licensing completion 100% |
| FAP Graduate | Gold | FAP completion 100% |
| Top Producer | Platinum | $100,000+ annual premium |
| Leadership Builder | Diamond | 5+ team recruits |

Recent achievements appear on the hub dashboard. Reports can include achievements earned in the report period.

---

## 19. Notifications and reminders

When creating a goal (Quick Goal wizard, Step 8), you can enable:

- **Email notifications** — Goal-related emails
- **In-app notifications** — Alerts in EFGTrack
- **Weekly reminders** — Recurring weekly check-in reminders

Reminders are processed by a scheduled job (`DispatchGoalReminders`). Weekly reminders repeat each week until the goal is complete.

If you assign an **accountability partner** (sponsor/mentor), they may receive coach copies of reminders when configured on the goal’s coach relationships.

---

## 20. Tips and best practices

1. **Start with a Performance Plan** for your primary annual target — it creates the full activity chain.
2. **Check the Scorecard weekly** — activity goals matter as much as outcome goals.
3. **Sync KPIs after field work** — log prospects and FNAs first, then sync.
4. **Use What-If before changing targets** — avoid rebuilding plans unnecessarily.
5. **Review the Success Blueprint monthly** — focus on the lowest stage with weak progress.
6. **Set milestones on big goals** — quarterly checkpoints improve SMART scores and visibility.
7. **CFMs: filter Needs attention weekly** — proactive coaching prevents year-end gaps.
8. **Download monthly reports** — useful for mentor meetings and personal review.
9. **Pick one primary planning type** — income, production, or recruiting; too many competing blueprints dilute focus.
10. **Use Quick Goals for personal development** — books, speaking, habits outside the business funnel.

---

## 21. Troubleshooting

### Buttons or dropdowns do nothing

- Hard-refresh the page (`Ctrl+F5` / `Cmd+Shift+R`).
- Ensure frontend assets are built: `npm run build` (or `npm run dev` during development).
- Livewire requires JavaScript; check browser console for errors.

### PDF download does not start

- Use the **Download PDF** link on `/goals/reports` (standard HTTP download, not Livewire).
- Ensure pop-up/download blockers allow downloads from your EFGTrack domain.

### Sync KPIs shows no change

- Confirm the goal has a **metric key** assigned (automated goals only).
- Confirm activity was logged in the source module (Prospects, FNA, etc.) within the goal’s date range.
- Manual metrics (e.g. income) are not auto-synced.

### Scorecard shows 0% or low targets

- Create a **Performance Plan** so activity targets are generated.
- Without a plan, the system uses generic weekly defaults.

### Forecasts are empty

- Create goals with deadlines and measurable progress.
- Performance Plans populate forecasts once goals are active and have data.

### Team Goals shows no members

- Verify you have downline relationships in EFGTrack.
- Check scope filter (direct vs full downline).
- Members must have goals matching your filters.

### CFM Coaching shows no trainees

- Trainees must be linked via mentor assignments.
- Trainees need active goals assigned to their user account.

### Permission denied (403)

- Your role needs `manage goals`, `view team goals`, or `coach goals` as appropriate.

---

## 22. Appendix

### A. Goal categories

| Slug | Name |
|---|---|
| recruiting | Recruiting Goals |
| production | Production Goals |
| prospecting | Prospecting Goals |
| financial_review | Financial Review Goals |
| fap | FAP Goals |
| licensing | Licensing Goals |
| cfm_development | CFM Development Goals |
| leadership | Leadership Goals |
| training | Training Goals |
| rank_advancement | Rank Advancement Goals |
| income | Income Goals |
| personal_development | Personal Development Goals |

### B. Measurement types

| Key | Use for |
|---|---|
| number | Counts (recruits, calls) |
| currency | Dollar targets (premium, income) |
| percentage | Completion rates |
| completion | Binary or checklist-style goals |

### C. Rank advancement requirements (defaults)

| Rank | Production | Recruits | Licensing | FAP | Training |
|---|---|---|---|---|---|
| SM | $50,000 | 2 | 100% | 100% | 80% |
| ED | $150,000 | 5 | 100% | 100% | 100% |
| SED | $300,000 | 10 | 100% | 100% | 100% |

Used by the Rank planning type in Performance Planner and What-If.

### D. Income planning constants (defaults)

| Constant | Default | Used for |
|---|---|---|
| Commission rate | 20% | Income → production conversion |
| Avg premium per application | $2,500 | Production → applications |
| Working days per month | 22 | Daily contact targets |
| Working weeks per year | 48 | Annualization |

### E. Related technical files (for administrators)

| Area | Location |
|---|---|
| Routes | `routes/web.php` |
| Goal config | `config/goals.php` |
| Planning config | `config/goals-planning.php` |
| Livewire components | `app/Livewire/Goals/` |
| Services | `app/Services/Goals/` |
| Feature tests | `tests/Feature/Goals/` |

---

*This guide reflects the Goals & Performance module as implemented in EFGTrack. Feature availability may vary by role and agency configuration.*
