# EFGTrack — Training Academy Module
**User Guide**

**Version:** 1.0  
**Last updated:** June 2026  
**Audience:** Associates, new recruits, CFMs, trainers, team leaders, and agency owners using EFGTrack  
**Hub URL:** `/training` (sidebar: **Training** / **EFGTrack Academy**)

---

## Table of contents

1. [What this module does](#1-what-this-module-does)
2. [Who can access what](#2-who-can-access-what)
3. [Module map — pages and URLs](#3-module-map--pages-and-urls)
4. [Recommended workflows](#4-recommended-workflows)
5. [Training Center hub (`/training`)](#5-training-center-hub-training)
6. [Courses and the course player](#6-courses-and-the-course-player)
7. [Lessons](#7-lessons)
8. [Assessments](#8-assessments)
9. [Assignments](#9-assignments)
10. [Learning paths](#10-learning-paths)
11. [My Learning Plan](#11-my-learning-plan)
12. [Certifications](#12-certifications)
13. [FAP & Coaching Center](#13-fap--coaching-center)
14. [Live training sessions and calendar](#14-live-training-sessions-and-calendar)
15. [Achievements and leaderboard](#15-achievements-and-leaderboard)
16. [Training reports and analytics](#16-training-reports-and-analytics)
17. [Training Content Studio (administrators)](#17-training-content-studio-administrators)
18. [Connections to other EFGTrack programs](#18-connections-to-other-efgtrack-programs)
19. [Key concepts](#19-key-concepts)
20. [Academy points and badges](#20-academy-points-and-badges)
21. [Tips and best practices](#21-tips-and-best-practices)
22. [Troubleshooting](#22-troubleshooting)
23. [Appendix](#23-appendix)

---

## 1. What this module does

The **EFGTrack Academy** (Training module) is your central place to learn, practice, certify, and grow in your financial services career. It combines self-paced online courses with structured learning paths, live coaching, certifications, and progress tracking tied to your development programs (FAP, licensing, CFM training, and rank advancement).

At a high level, the module provides:

| Capability | What it means for you |
|---|---|
| **Course catalog** | Browse and complete video, document, and interactive lessons organized by topic |
| **Learning paths** | Follow role-based programs (e.g., New Associate, Licensing, CFM Certification) |
| **Assignments** | Complete courses assigned to you by leaders or trainers, often with due dates |
| **Assessments** | Prove knowledge with scored quizzes linked to courses |
| **Certifications** | Earn official academy certificates, sometimes with mentor approval |
| **FAP & Coaching** | Track Field Apprenticeship progress, receive CFM feedback, schedule sessions |
| **Live sessions** | Register for webinars, coaching labs, and field observations synced to your calendar |
| **Personalized learning plan** | Get smart recommendations based on your role and progress |
| **Achievements** | Earn points, badges, streaks, and leaderboard rank for consistent learning |
| **Reports** | View and export training analytics for yourself or your team |

The Training Center works alongside — but is separate from — checklist trackers such as **FAP**, **Licensing**, and **CFM Training**. Academy courses build skills; checklists track formal program milestones. The dashboard links both so you can see the full picture.

---

## 2. Who can access what

Access is controlled by your role and permissions. Most members can use the Training Center for their own learning. Leaders and trainers get additional tools for assigning courses, reviewing certifications, and viewing team reports.

### Member capabilities (typical associate / member)

| Feature | Access |
|---|---|
| Training Center dashboard | Yes |
| Course catalog and lessons | Yes |
| Assessments | Yes |
| My assignments | Yes |
| Learning paths (enroll and progress) | Yes |
| My Learning Plan | Yes |
| My certifications | Yes |
| Achievements and leaderboard | Yes |
| Personal training reports | Yes |
| FAP & Coaching (as trainee) | Yes, when enrolled in FAP |
| Live session registration | Yes |

### Leader and mentor capabilities

| Permission | Typical roles | What you can do |
|---|---|---|
| `view own team` | Team leader, CFM | Training reports for **direct reports** |
| `view training summary` | Team leader, CFM, agency owner | Training reports for your **full downline** |
| Active mentor assignment | CFM | Review certification requests; coaching tools in FAP & Coaching Center |
| `manage training` | Trainer, admin | Assign courses, build content, organization-wide reports |

### Administrator / trainer capabilities

| Permission | Typical roles | What you can do |
|---|---|---|
| `manage training` | Trainer, admin, agency owner | **Training Content Studio**, assign courses, organization reports |
| `manage assessments` | Trainer | Build assessments via Admin Management (linked to courses) |
| `manage training calendar` | Trainer, CFM | Schedule sessions; mark attendance |

If a page returns **403 Forbidden** or a button does not appear, your role does not include the required permission. Contact your agency administrator.

---

## 3. Module map — pages and URLs

### Member-facing pages

| Page | URL | Purpose |
|---|---|---|
| **Training Center** | `/training` | Main dashboard — progress, recommendations, catalog |
| **Course outline** | `/training/courses/{course-slug}` | Lesson list, progress, linked assessment |
| **Lesson player** | `/training/courses/{course-slug}/lessons/{lesson-id}` | Watch/read content and mark complete |
| **Assessments list** | `/assessments` | All available knowledge checks |
| **Assessment detail** | `/assessments/{id}` | Rules, attempts, start or retake |
| **Take assessment** | `/assessments/{id}/take` | Submit answers |
| **My assignments** | `/training/assignments` | Courses assigned to you |
| **Learning paths** | `/training/paths` | Browse structured programs |
| **Path detail** | `/training/paths/{path-code}` | Enroll and track path progress |
| **My Learning Plan** | `/training/plan` | Personalized recommendations |
| **My certifications** | `/training/certifications` | Earned and pending certificates |
| **Certificate detail** | `/training/certifications/{id}` | Certificate number, status, dates |
| **FAP & Coaching** | `/training/coaching` | FAP progress, CFM feedback, sessions |
| **Live sessions** | `/training/sessions` | Upcoming academy sessions |
| **Session detail** | `/training/sessions/{id}` | Register, check in, view roster (instructors) |
| **Achievements** | `/training/achievements` | Points, badges, leaderboard |
| **Training reports** | `/training/reports` | Analytics, PDF download, email |

### Administrator pages (requires `manage training`)

| Page | URL | Purpose |
|---|---|---|
| **Training Content Studio** | `/admin/training` | Admin hub — stats and quick links |
| **Course Builder** | `/admin/training/courses` | Create and list courses |
| **Course editor** | `/admin/training/courses/{slug}` | Edit course settings and lessons |
| **Path Builder** | `/admin/training/paths` | Create and list learning paths |
| **Path editor** | `/admin/training/paths/{code}` | Edit path and attach courses |
| **Assign courses** | `/training/assignments/manage` | Assign courses to members |

### Related program trackers (linked from Training Center)

| Program | URL | Purpose |
|---|---|---|
| FAP checklist | `/apprenticeship` | Field Apprenticeship Program steps |
| CFM training checklist | `/cfm-training` | CFM development checklist |
| Licensing tracker | `/licensing` | Provincial/state licensing progress |
| Rank advancement | `/rank-advancement` | Rank requirements including training % |

---

## 4. Recommended workflows

### New associate — first 30 days

1. Open **Training Center** (`/training`) and review your dashboard cards.
2. Go to **My Learning Plan** and enroll in the **New Associate Path** if suggested.
3. Start with **Compliance Foundations** or the first course in your path.
4. Complete lessons in order; mark each lesson **complete** when finished.
5. Take the course **assessment** after all lessons are done.
6. Open **FAP Training Center** from Program Trackers and begin your FAP checklist.
7. Register for an upcoming **live coaching session** if one is scheduled.
8. Check **Achievements** weekly to build your learning streak.

### Associate working toward licensing

1. Enroll in the **Licensing Path** from Learning Paths.
2. Monitor **Licensing Progress** on the Training Center dashboard.
3. Follow recommendations in **My Learning Plan** when licensing falls behind schedule.
4. Complete assigned courses before their **due dates**.

### Certified Field Mentor (CFM)

1. Use **FAP & Coaching Center** to monitor trainee FAP percentages.
2. Submit **coaching reviews** after sessions or field observations.
3. Record **FAP sign-off** when a trainee reaches the required completion threshold.
4. **Schedule coaching sessions** — they appear in Live Sessions and sync to the calendar.
5. Review **certification requests** from trainees at `/training/certifications/reviews`.
6. Mark **attendance** on session detail pages for registered trainees.

### Trainer or training administrator

1. Open **Manage Training** from the Training Center (or `/admin/training`).
2. Create or update **courses** and **lessons** in the Course Builder.
3. Publish courses when ready; only published courses appear in paths and the catalog.
4. Assemble **learning paths** from published courses in the Path Builder.
5. **Assign courses** to members with due dates and notes.
6. Build **assessments** in Admin Management and link them to courses.
7. Run **organization reports** to monitor academy adoption.
8. Use **Preview Training Center** to verify the member experience.

### Team leader — coaching without building content

1. View **Training Reports** with scope **Direct reports** or **Downline**.
2. Identify overdue assignments and low completion rates.
3. Assign remedial courses via **Assign courses** (if you have `manage training`) or ask your trainer.
4. Encourage associates to use **My Learning Plan** for prioritized next steps.

---

## 5. Training Center hub (`/training`)

The Training Center is your home base. The header shows quick links to every major area. Below that, the dashboard summarizes your academy activity.

### Summary cards

Eight cards appear at the top of the dashboard:

| Card | Meaning |
|---|---|
| **Courses Assigned** | Active course assignments not yet completed |
| **Courses Completed** | Published courses where you finished every lesson |
| **Certifications Earned** | Issued certificates |
| **In Progress** | Lessons in progress plus active path enrollments |
| **Overdue Training** | Assignments past their due date |
| **Training Hours** | Estimated hours from lesson time tracked |
| **FAP Completion** | FAP checklist percentage (or "Not started") |
| **Licensing Progress** | Licensing checklist percentage (or "Not started") |

### Gamification strip

Below the cards, you see **Academy Points**, **Learning Streak** (consecutive days), **Badges** earned, and your **Leaderboard Rank** (if ranked). Click **View achievements** for the full achievements page.

### Learning activity chart

A six-month bar chart shows lessons **started** vs **completed**. The badge shows your overall **lesson completion percentage** across all published courses.

### Recommended for you

Up to five personalized recommendations appear with action links. Click **My learning plan** to see the full list and manage suggestions.

### Learning paths preview

Shows your active paths with progress bars. Click **View all** for the full path catalog.

### Featured courses

Highlighted courses marked as featured by your administrator. Click any course to open its outline.

### Program trackers

Quick links to **FAP Training Center**, **CFM Training Checklist**, **Licensing Tracker**, and **FAP & Coaching Center**. Use these alongside academy courses.

### My assignments and certifications

Short lists of your most recent assignments and certifications, with links to the full pages.

### Course catalog

A grid of all **published** academy courses with your progress percentage on each. This is the best place to browse everything available.

---

## 6. Courses and the course player

### Opening a course

Click any course from the catalog, a learning path, an assignment, or a recommendation. The **course outline** page shows:

- Course title, description, category
- **Course type** (e.g., Video Course, Document-Based)
- **Difficulty** (Beginner through Expert)
- **Duration** (if set)
- Tags such as **Sequential** or **Drip schedule**
- Your overall **progress percentage**

### Lesson list

Each lesson shows:

- Lesson number and title
- Lesson type (video, document, article, etc.)
- Status: **Not started**, **In progress**, or **Completed**
- **Locked** status if you cannot access it yet

### Continue learning

When you have an incomplete lesson, a **Continue learning** panel highlights the next lesson with a **Start** or **Resume** button.

### Course complete

When every required lesson is finished, a green **Course complete** panel appears.

### Linked assessment

If the course has an assessment, a sidebar panel shows the assessment title, passing score, your best score, and a **Take assessment** button (or a message that you must finish lessons first).

### Course pacing rules

Administrators can configure two pacing modes:

| Mode | Behavior |
|---|---|
| **Sequential** | You must complete each lesson before the next unlocks |
| **Drip schedule** | Lessons unlock one per day from the date you started the course |
| **Open** | No locks — work at your own pace |

Locked lessons show why they are locked: *Complete the previous lesson first* or *Unlocks [date]*.

---

## 7. Lessons

### Lesson player layout

The lesson page has three areas:

1. **Main content** — video embed (YouTube), external link, or written content
2. **Navigation** — Previous / Next lesson buttons
3. **Sidebar** — full lesson list with status indicators

### Watching and reading

- **Video lessons** play in an embedded player when a YouTube URL is configured.
- **External resources** open via an **Open resource** button in a new tab.
- **Document and article lessons** display text content on the page.

### Marking a lesson complete

Click **Mark complete** when you have finished the lesson. This updates your course progress, may unlock the next lesson, and awards **academy points**.

If you already completed a lesson, the button shows **Completed**. You can click **Reopen lesson** to mark it for review without losing path credit (progress remains tracked).

### Lesson locks

In the sidebar, locked lessons appear grayed out with a **Locked** label. Complete prior lessons or wait for drip unlock dates to access them.

---

## 8. Assessments

Assessments are knowledge checks linked to academy courses. Access them from the course outline, the **Assessments** menu, or recommendations in My Learning Plan.

### Assessment list (`/assessments`)

Browse all assessments available to you. Each shows its linked course and your pass status.

### Assessment detail

Before starting, review:

| Rule | Description |
|---|---|
| **Passing score** | Minimum percentage to pass (commonly 70–80%) |
| **Question count** | Number of questions on the assessment |
| **Attempts allowed** | Default maximum is **3 attempts** per assessment |
| **Course completion** | You must finish all lessons in the linked course before taking the assessment |

Your status panel shows **Ready**, **Keep trying**, or **Passed**, plus your best score and attempts used.

### Taking an assessment

1. Click **Start assessment** (or **Retake**).
2. Answer each question — multiple choice or short answer.
3. Click **Submit assessment**.
4. View your score and pass/fail result immediately.

### After passing

- You **cannot retake** a passed assessment by default.
- Passing may trigger **certification eligibility** and award **assessment points** and badges (e.g., perfect score).
- Return to the course outline to confirm completion status.

### If you cannot start

Common lock reasons:

| Message | What to do |
|---|---|
| Complete all lessons in the linked course | Finish remaining lessons first |
| You have already passed | No further action needed |
| You have used all available attempts | Contact your trainer or CFM for guidance |

---

## 9. Assignments

Leaders and trainers can assign specific courses to you with optional due dates and notes.

### My assignments (`/training/assignments`)

Each assignment row shows:

- Course title
- Status (assigned, in progress, completed, overdue, cancelled)
- **Due date** (if set)
- Who assigned it
- Optional instructions in the notes field
- Progress bar and percentage
- **Open course** button

**Overdue** assignments are highlighted in red. Overdue items also appear as high-priority recommendations in My Learning Plan.

### Completing an assignment

Work through the course normally. When you complete all lessons (and pass the assessment if required), the assignment status updates to **completed** automatically.

### Assigning courses (leaders/trainers)

If you have `manage training`, open **Assign courses** from My Assignments or `/training/assignments/manage`:

1. Select a **member**.
2. Select a **course**.
3. Set a **due date** (default expectation is 30 days if your agency uses standard settings).
4. Add optional **notes** (e.g., "Complete before field training next week").
5. Click **Assign course**.

Recent assignments appear in a list where you can **cancel** active assignments if they were created in error.

---

## 10. Learning paths

Learning paths group multiple courses into a structured program for a specific audience (associate, mentor, or leader).

### Default paths

Your agency may publish paths such as:

| Path | Audience | Typical focus |
|---|---|---|
| **New Associate Path** | Associate | Welcome, compliance, FNA, prospecting, presentation, follow-up, FAP readiness |
| **Licensing Path** | Associate | Provincial/state licensing, CE, exam prep |
| **CFM Certification Path** | Mentor | Coaching, leadership, FAP management, mentorship |
| **Agency Owner Path** | Leader | Recruiting, retention, team building, compliance, culture |

### Browsing paths (`/training/paths`)

Each path card shows name, audience, description, course count, enrollment status, and progress percentage.

### Path detail

On a path page you can:

1. Read the full description and course list.
2. **Enroll in path** to start tracking (if not already enrolled).
3. See per-course progress bars.
4. Click any course to open it.
5. Use **Continue next course** when enrolled to jump to your next incomplete course.

### Path completion

When every required course in the path reaches 100%, the path status changes to **completed**. You earn **path completion points** and may receive the **Path Graduate** badge.

### Role-based suggestions

My Learning Plan may suggest the path that matches your role (e.g., new associates are nudged toward the New Associate Path). You can enroll directly from the Learning Plan page.

---

## 11. My Learning Plan

**My Learning Plan** (`/training/plan`) is your personalized priority list. The system analyzes your role, enrollments, assignments, course progress, checklist status, and activity patterns to recommend next steps.

### Summary stats

| Stat | Meaning |
|---|---|
| Courses completed | Your completions vs total published courses |
| Active assignments | Open assignments |
| Enrolled paths | Learning paths you have joined |
| Priority actions | High-priority recommendations count |

### Priority recommendations

Each recommendation includes:

- A **category label** (e.g., Overdue assignment, Continue learning, Assessment ready)
- A descriptive **message**
- An **action button** (Resume course, View path, Take assessment, etc.)
- A **Dismiss** button to hide a suggestion you cannot act on now

Dismissed recommendations stay hidden until your situation changes and the system generates new ones.

### Types of recommendations

| Type | When it appears |
|---|---|
| **Overdue assignment** | An assigned course is past its due date |
| **Continue learning** | You have a course in progress |
| **Learning path** | Next course in an enrolled path |
| **Recommended path** | A role-based path you have not enrolled in |
| **Assessment ready** | Course complete; assessment available |
| **FAP** | FAP checklist not started or in progress |
| **Licensing** | Licensing progress below expected pace |
| **CFM training** | CFM checklist items pending |
| **Get back on track** | No learning activity for 14+ days |
| **Featured course** | Highlighted content to explore |
| **On track** | Positive confirmation when you are progressing well |

### Enrolled paths panel

Shows each path you are enrolled in with overall progress and the **next course** to work on.

---

## 12. Certifications

Certifications are formal credentials earned by completing courses and assessments. Some are issued automatically; others require **mentor approval**.

### My certifications (`/training/certifications`)

Lists all your certification records with status:

| Status | Meaning |
|---|---|
| **Pending** | Awaiting mentor or trainer approval |
| **Issued** | Certificate granted — includes certificate number |
| **Rejected** | Request not approved — contact your mentor |

### Certificate detail

Open any record to see:

- Certificate number (when issued)
- Issue date and expiration (if applicable)
- Approver name
- Link back to the source course

### Earning a certification

Typical flow:

1. Complete the linked course and all lessons.
2. Pass the linked assessment.
3. The system creates a certification record — either **issued** immediately or **pending** review.
4. If pending, your CFM or trainer approves at **Review requests** (`/training/certifications/reviews`).

### Reviewing requests (mentors/trainers)

If you are an active mentor or have `manage training`:

1. Open **Review requests** from My Certifications.
2. See the trainee name, certification name, and linked course.
3. Click **Approve** to issue the certificate, or **Reject** with the trainee notified to follow up.

---

## 13. FAP & Coaching Center

The **FAP & Coaching Center** (`/training/coaching`) connects academy learning with Field Apprenticeship Program development and mentor coaching.

### For trainees and associates

**My FAP Progress**

- Shows your FAP checklist completion percentage.
- **Open FAP checklist** links to the full FAP tracker (`/apprenticeship`).
- If your CFM has signed off, a green confirmation shows the mentor name and date.

**Your CFM**

- Displays your assigned Certified Field Mentor's name and email.

**Coaching feedback**

- Lists reviews submitted by your CFM: coaching sessions, field observations, and scores.
- Each entry shows review type, date, feedback text, and optional score.

### For CFMs and mentors

**My Trainees**

- Lists active trainees with FAP progress bars.
- **FAP sign-off** button appears when a trainee reaches the required completion threshold (typically 90%).
- Shows **Signed off** when complete.

**Submit coaching review**

1. Select a **trainee**.
2. Choose **review type**: Coaching Session or Field Observation.
3. Optionally enter a **score** (0–100).
4. Write **feedback** (observations, next steps).
5. Click **Submit review**.

**Schedule a session**

Mentors can create live sessions:

1. Enter **title**, **type** (Live Coaching, Webinar, Field Training), **start date/time**, optional **capacity**, and **description**.
2. Click **Create session**.
3. The session appears under Upcoming coaching sessions and in Live Sessions; it syncs to the EFGTrack **calendar**.

### Upcoming coaching sessions

Shows scheduled sessions with register buttons. Registered sessions link to your calendar.

---

## 14. Live training sessions and calendar

### Sessions list (`/training/sessions`)

Browse upcoming academy sessions:

- Title, date/time, session type
- Instructor name
- Your status: Registered, Attended, or open for registration
- **Calendar** link to the synced calendar event
- **Details** link for full session page

### Session detail

On a session page you can:

| Action | Who |
|---|---|
| **Register for session** | Any member — adds the event to your calendar |
| **Check in** | Registered attendees — confirms attendance |
| **View in calendar** | Anyone with a linked calendar event |
| **Mark attended** (roster) | Session instructor or training manager |

### Session types

| Type | Typical use |
|---|---|
| **Live Coaching** | Interactive CFM or trainer-led coaching |
| **Webinar** | Group presentation or recorded review |
| **Field Training** | Field observation debriefs |

### Calendar integration

When you register, EFGTrack creates or updates a **calendar event** so the session appears on your main **Calendar** (`/calendar`). Instructors see an **attendance roster** on the session detail page.

### Points for attendance

Checking in or being marked attended awards **session attendance points** toward your academy profile.

---

## 15. Achievements and leaderboard

**Achievements** (`/training/achievements`) gamifies learning to encourage consistency.

### Your stats

| Stat | Description |
|---|---|
| **Academy Points** | Total points from lessons, courses, assessments, certifications, sessions, paths |
| **Current Streak** | Consecutive days with at least one lesson completed |
| **Best Streak** | Longest streak you have achieved |
| **Badges Earned** | Count of badges collected |
| **Leaderboard Rank** | Your position in the organization (or team) leaderboard |

### Your badges

Earned badges show name, description, level (Bronze through Diamond), points value, and date earned.

### Leaderboard

Rankings are based on total academy points. If you belong to a team, toggle between **All** (organization) and **My Team**. Each row shows rank, name, badge count, streak, and points.

### Available badges

A catalog of all badges shows which you have earned (**Earned**) vs not yet unlocked (**Locked**). Use this as a goals list for your development.

### How to earn points (defaults)

| Activity | Points |
|---|---|
| Lesson completed | 1 |
| Course completed | 10 |
| Assessment passed | 15 |
| Certification issued | 25 |
| Session attended | 5 |
| Learning path completed | 20 |

Agency configuration may adjust these values.

---

## 16. Training reports and analytics

**Training Reports** (`/training/reports`) provides analytics for personal review or team oversight.

### Report controls

| Control | Options |
|---|---|
| **Report period** | Last week, last month, last quarter, last year |
| **Audience / scope** | Depends on your permissions (see below) |

Actions:

- **Download PDF** — save a formatted report
- **Email report** — send the PDF to your own email address

### Metrics shown

| Metric | Description |
|---|---|
| Lessons completed | Lessons marked complete in the period |
| Courses completed | Courses fully finished in the period |
| Assessments passed | Assessments passed in the period |
| Certifications issued | Certificates issued in the period |
| Training hours | Estimated learning time |
| Avg course progress | Average completion across courses |
| Overdue assignments | Count of overdue items (team scopes) |
| Active learners | Members with activity (team scopes) |

Charts include **lesson completion trend** (monthly) and **top completed courses**. Personal scope also shows **My course progress** with per-course percentages.

### Report scopes

| Scope | Who can use it | What it covers |
|---|---|---|
| **Personal** | Everyone | Your own data only |
| **Direct reports** | `view own team` | Immediate team members |
| **Downline** | `view training summary` | Full downline hierarchy |
| **Organization** | `manage training` | Agency-wide academy metrics and learner table |

---

## 17. Training Content Studio (administrators)

Trainers and administrators with `manage training` can build academy content at `/admin/training`.

### Training Content Studio hub

The hub shows counts of published courses, draft courses, lessons, active paths, and certifications. Quick links:

| Tool | Purpose |
|---|---|
| **Course Builder** | Create and edit courses with lessons |
| **Learning Path Builder** | Assemble paths from published courses |
| **Assign Training** | Assign courses to members |
| **Categories** | Manage training categories (Admin Management) |
| **Assessments & Questions** | Build assessments linked to courses |
| **Preview Training Center** | View the member-facing catalog |

### Course Builder (`/admin/training/courses`)

**List view**

- See all courses (published and draft) with lesson counts.
- Click **New course** to open the creation form.
- Click any course to edit.

**Creating a course**

| Field | Guidance |
|---|---|
| Category | Organize in the catalog (Prospecting, Compliance, etc.) |
| Title | Clear, action-oriented name |
| Description | What learners will gain |
| Course type | Video, document, interactive, webinar, live, certification, coaching |
| Difficulty | Beginner through expert |
| Published | Leave unchecked for draft; check when ready for members |

After creation, you are taken to the **course editor**.

### Course editor

**Course settings**

| Field | Purpose |
|---|---|
| Category, title, slug | Organization and URL |
| Description | Shown on course outline |
| Course type, difficulty | Metadata badges |
| Duration (minutes) | Estimated time |
| Sort order | Catalog ordering |
| Instructor | Optional linked trainer |
| **Published** | Makes course visible in catalog and paths |
| **Featured** | Highlights on Training Center dashboard |
| **Sequential required** | Forces lesson order |
| **Drip enabled** | Unlocks one lesson per day from learner start |

Click **Save course** after changes.

**Lesson management**

Add lessons with:

| Field | Purpose |
|---|---|
| Title | Lesson name |
| Type | Video, document, article, interactive, quiz |
| Content | Written material |
| Video URL | YouTube link for embedded playback |
| Sort order | Position in the course |
| Required | Whether it counts toward completion |

Use **Edit** on existing lessons to update them, or **Delete** to remove. Save each lesson with **Save lesson**.

### Learning Path Builder (`/admin/training/paths`)

**Creating a path**

| Field | Purpose |
|---|---|
| Name | Display name (e.g., "New Associate Path") |
| Code | URL identifier (auto-generated from name if blank) |
| Description | Shown on path cards |
| Audience | Associate, mentor, or leader |

**Path editor**

- Update path settings and **active** status.
- **Attach modules** — add published courses from the dropdown.
- Set **sort order** and **required** flag per course.
- Remove courses from the path as needed.
- Click **Save path** to sync course attachments.

Only **published** courses can be added to paths.

### Publishing checklist for trainers

Before announcing new content:

1. Course has a clear title, description, and category.
2. All lessons have content (video, text, or links).
3. Sequential/drip settings match your training design.
4. Course is marked **Published**.
5. Assessment is linked and tested (if applicable).
6. Course is added to the appropriate **learning path**.
7. Preview in **Training Center** as a member would see it.
8. Assign to a pilot group before agency-wide rollout.

---

## 18. Connections to other EFGTrack programs

The academy does not exist in isolation. It integrates with several other modules:

### FAP (Field Apprenticeship Program)

- FAP completion % appears on the Training Center dashboard.
- FAP & Coaching Center links to the FAP checklist.
- CFMs record sign-off when FAP threshold is met.
- My Learning Plan recommends FAP actions when progress stalls.

### Licensing tracker

- Licensing % appears on the dashboard.
- Recommendations appear when licensing progress falls behind.

### CFM training checklist

- Separate checklist at `/cfm-training` for mentor development.
- Linked from Program Trackers and recommendations.

### Rank advancement

- Training completion may factor into rank requirements.
- **Rank Advancement** page is linked from the Training Center header.
- Complete learning paths and certifications to satisfy training percentages.

### Goals & Performance

- Goal metrics can reference training completion, FAP, and CFM training progress.
- Use academy completion alongside activity goals for a full development picture.

### Calendar

- Live training sessions sync to your personal and shared calendars.
- Registration and attendance flow through both Training Sessions and Calendar.

### Notifications

- Assignment due dates, certification status changes, and learning reminders may appear in your EFGTrack notification center (depending on agency settings).

---

## 19. Key concepts

| Term | Definition |
|---|---|
| **Course (module)** | A collection of lessons on one topic; may include an assessment and certification |
| **Lesson** | A single unit of learning (video, document, etc.) |
| **Published** | Visible to members in the catalog and paths |
| **Draft** | Visible only to administrators in the Content Studio |
| **Assignment** | A course allocated to a specific member, often with a due date |
| **Learning path** | Ordered set of courses forming a development program |
| **Enrollment** | Your commitment to track progress on a path |
| **Assessment** | Scored quiz; passing may be required for certification |
| **Certification** | Formal credential with certificate number |
| **Sequential course** | Lessons must be completed in order |
| **Drip course** | Lessons unlock on a daily schedule |
| **Academy points** | Gamification currency for learning activities |
| **Streak** | Consecutive days with completed lessons |
| **Coaching review** | Mentor feedback record (session, observation, sign-off) |
| **Live session** | Scheduled group or field training event with registration |

---

## 20. Academy points and badges

### Badge levels

| Level | Typical meaning |
|---|---|
| Bronze | Early milestones |
| Silver | Intermediate achievements |
| Gold | Advanced accomplishments |
| Platinum | Major program completion |
| Diamond | Elite recognition |

### Example badges

Your agency may configure badges such as:

| Badge | How to earn |
|---|---|
| First Course Completed | Finish your first academy course |
| Prospecting Certified | Complete prospecting course and requirements |
| Presentation Expert | Master presentation course |
| CFM Certified | Complete CFM certification path requirements |
| Dedicated Learner | Complete three academy courses |
| Assessment Ace | Score 100% on an assessment |
| Path Graduate | Complete an entire learning path |
| 3 / 7 / 14-Day Learning Streak | Complete lessons on consecutive days |

Check **Available Badges** on the Achievements page for the full list in your agency.

---

## 21. Tips and best practices

### For learners

- Start each week on **My Learning Plan** — work the top priority first.
- Mark lessons **complete** honestly; progress drives assignments, paths, and rank.
- Build a **daily streak** — even one short lesson counts.
- Finish **compliance and foundational courses** early in your career.
- Register for **live sessions** in advance; add them to your calendar.
- If stuck on a locked lesson, read the lock reason on the course outline.

### For CFMs

- Review trainee **My Learning Plan** priorities in your 1:1 conversations.
- Submit **coaching reviews** within 48 hours of sessions for timely feedback.
- Use **FAP sign-off** only when the trainee has genuinely completed requirements.
- Assign specific courses when you identify skill gaps.
- Approve **certifications** promptly when trainees qualify.

### For trainers and administrators

- Keep course titles and descriptions **member-friendly**, not internal jargon.
- Use **featured** sparingly — highlight truly important content.
- Test **sequential** and **drip** courses with a test account before publishing.
- Align paths with **onboarding milestones** (week 1, 30, 60, 90).
- Run **monthly organization reports** to spot drop-off and overdue patterns.
- Link every certification course to a clear **assessment** with realistic passing scores.

---

## 22. Troubleshooting

### I do not see the Training menu

- Confirm you are logged in as a member or higher role.
- Contact your administrator if your account lacks dashboard access.

### A course or path is missing

- Only **published** content appears to members.
- Draft courses are visible only in the Training Content Studio.
- Your role may not be the target **audience** for some paths — check with your trainer.

### A lesson is locked

- **Sequential course:** Complete the previous lesson first.
- **Drip course:** Wait until the unlock date shown on the course outline.
- If you believe this is an error, contact your trainer.

### I cannot take the assessment

- Complete **all lessons** in the linked course.
- Check if you have **attempts remaining** (default max: 3).
- If you already **passed**, retakes are disabled by default.

### My assignment still shows overdue after completion

- Refresh the page — status updates when all lessons are complete.
- If an assessment is required, pass it to fully complete the course.
- Contact the person who assigned the course if status does not update within a few minutes.

### Certification stuck on pending

- Your CFM or trainer must approve at **Review requests**.
- Confirm you passed the assessment and completed all lessons.
- Follow up with your mentor if pending more than a few business days.

### No recommendations in My Learning Plan

- Recommendations sync when you visit the Training Center or Learning Plan.
- If you are fully caught up, you may only see an "on track" message.
- Dismissed items stay hidden until circumstances change.

### I registered for a session but do not see it on my calendar

- Open the session detail and click **View in calendar**.
- Check that you are looking at the correct date range in Calendar.
- Contact the session instructor if the event is missing.

### Leaderboard rank is blank

- Rankings require academy points. Complete at least one lesson to appear.
- Very small agencies may not show meaningful ranks until more members participate.

### Permission denied (403) on admin or reports

| Page | Required permission |
|---|---|
| Training Content Studio | `manage training` |
| Assign courses | `manage training` |
| Organization reports | `manage training` |
| Downline reports | `view training summary` |
| Direct team reports | `view own team` |

### Coaching Center shows no trainees (CFM)

- Trainees must have an **active mentor assignment** linking them to you.
- Verify assignments in CFM management or contact your agency owner.

---

## 23. Appendix

### A. Default learning paths

| Code | Name | Audience |
|---|---|---|
| `new-associate` | New Associate Path | Associate |
| `licensing` | Licensing Path | Associate |
| `cfm-certification` | CFM Certification Path | Mentor |
| `agency-owner` | Agency Owner Path | Leader |

### B. Course types

| Type | Description |
|---|---|
| Video Course | Primarily video lessons |
| Document-Based Course | Reading and document lessons |
| Interactive Course | Interactive exercises and worksheets |
| Webinar Recording | Recorded group sessions |
| Live Training | Tied to scheduled live sessions |
| Certification Program | Leads to formal certification |
| Coaching Program | Mentor-led development content |

### C. Difficulty levels

Beginner → Intermediate → Advanced → Expert

### D. Lesson types

Video, Document, Article, Interactive, Quiz

### E. Coaching review types

| Type | Use when |
|---|---|
| Coaching Session | After a 1:1 or group coaching meeting |
| Field Observation | After accompanying a trainee in the field |
| FAP Sign-Off | Formal FAP completion approval by CFM |

### F. Session types

| Type | Use when |
|---|---|
| Live Coaching | Interactive coaching meeting |
| Webinar | Presentation or group training |
| Field Training | Field observation or debrief |

### G. Assignment statuses

| Status | Meaning |
|---|---|
| Assigned | Not yet started |
| In progress | At least one lesson started |
| Completed | All requirements met |
| Overdue | Past due date and not completed |
| Cancelled | Removed by assigner |

### H. Certification statuses

| Status | Meaning |
|---|---|
| Pending | Awaiting approval |
| Issued | Certificate granted |
| Rejected | Not approved |

### I. Training categories (examples)

Prospecting, Presentation Skills, Leadership, Compliance — your agency may add more via Admin Management.

### J. Report scope quick reference

| Scope | Permission | Best for |
|---|---|---|
| Personal | (all members) | Your own development review |
| Direct reports | `view own team` | Team leader 1:1s |
| Downline | `view training summary` | CFM and leader oversight |
| Organization | `manage training` | Agency-wide training KPIs |

### K. Sample seeded courses (reference)

Agencies seeding the academy often include:

- Prospecting Fundamentals
- Presentation Mastery
- Leadership Essentials
- Compliance Foundations

Your live catalog depends on what your administrators have published.

---

*This guide reflects the EFGTrack Training Academy module as implemented in EFGTrack. Course names, paths, badges, and permissions may vary by agency configuration and role.*
