# Riverbend Audit Lab: Rescuing an Inherited WordPress Site

This is a hands-on lab you do yourself. A flawed "legacy" WordPress site is already built for you in [../legacy-site](../legacy-site/). You audit it and fix it one step at a time, the same way you would at ARC. Every step ends with something you **record** and a question you **explain in your own words**. By the end you'll have real evidence for the proposal and practice talking about it.

> **Rule for the whole lab:** Riverbend Regional Commission is fictional. Label the site "Demo" and never use ARC's name, logo or content.

## How to use this guide

- Work through the modules in order. Tick each `[ ]` box as you finish it.
- Record every issue in [evidence/findings-log.csv](evidence/findings-log.csv) and every number in [evidence/scorecard.csv](evidence/scorecard.csv). Both open in Excel.
- Save screenshots to `evidence/before/` and `evidence/after/`. Name them by issue ID, for example `B05-contact-form.png`.
- Keep a short **journal** in `evidence/journal.md`: 3–5 sentences after each module about what you did, what surprised you, and what you'd tell a client. The presentation module builds on this journal.

| Module | Topic | Time |
|---|---|---|
| 0 | Tools and starting the site | 1 h |
| 1 | Tour the legacy site; find where each barrier lives | 2 h |
| 2 | Measure the baseline | 1 h |
| 3 | Automated audit | 1–2 h |
| 4 | Manual audit: keyboard, zoom, contrast, screen reader | 3–4 h |
| 5 | WCAG 2.2 checks | 1 h |
| 6 | Prioritize and plan the fixes | 1 h |
| 7 | Fix step by step | 4–6 h |
| 8 | Retest and measure the "after" | 1–2 h |
| 9 | Write the audit report | 2–3 h |
| 10 | Practice presenting | ongoing |

**Target:** finish Modules 0–2 by Sep 30, Modules 3–6 by Oct 5, Modules 7–9 by Oct 12.

---

## Module 0: Tools and local setup

Install these. All are free.

- [ ] **Docker Desktop** (already installed). It runs the legacy site. See [../legacy-site/README.md](../legacy-site/README.md).
- [ ] **Google Chrome**, which includes **Lighthouse** in DevTools
- [ ] **axe DevTools** Chrome extension (Deque)
- [ ] **WAVE** Chrome extension (WebAIM)
- [ ] **Accessibility Insights for Web** Chrome extension (Microsoft). Its "Tab stops" view is great for learning.
- [ ] **NVDA** screen reader (nvaccess.org). Create the desktop shortcut during install so **Ctrl+Alt+N** starts it.
- [ ] **Colour Contrast Analyser** (TPGi)
- [ ] Keep one page open for reference: the **WCAG 2.2 Quick Reference** at w3.org/WAI/WCAG22/quickref

Start the site (it's already built):

- [ ] Start Docker Desktop, then in a terminal: `cd "GA@WORK Projects/riverbend/legacy-site"` → `docker compose up -d`
- [ ] Open **http://localhost:8080**. Admin login: **http://localhost:8080/wp-admin**, user `admin`, password `riverbend-demo`.
- [ ] In WP Admin, look at **Appearance → Themes** (Astra) and **Plugins** (Elementor, Beaver Builder Lite, Contact Form 7). This is the stack you "inherited."

**Explain it:** Why build and test on a local copy instead of the live site? Write 2–3 sentences in your journal. Mention risk, staging, and how you'd do it at ARC.

---

## Module 1: Tour the legacy site and find where each barrier lives

The site was built by a script to copy what years of different vendors leave behind. It has 13 known barriers (B01–B13) already planted. The testing tools will also find problems nobody planted on purpose; they come from the theme and plugins' own defaults. Log those too. They're the realistic "inherited" findings, and you won't find them listed here.

**Your job in this module:** for each barrier in the table below, find **where it lives in WP Admin**: which page, which builder (Elementor, Beaver Builder, block editor), which widget or module, or the Customizer. Open it in the editor, but **don't change anything yet**. Take a "before" screenshot. Knowing where a problem lives is half of fixing it, and it's exactly what you'll do on ARC's sites.

Tips: open a page with **Edit with Elementor** or **Page Builder** (Beaver Builder) from the admin bar. The site-wide CSS is under **Appearance → Customize → Additional CSS**. The text color is under **Customize → Global → Colors**. The form is under **Contact → Contact Forms**.

### The pages

| Page | Built with | Content |
|---|---|---|
| Home | Elementor | Hero image, carousel, "Submit a Comment" button, social icons, map |
| About | Beaver Builder | Mission text, leadership, a notice box |
| Meetings | Block editor | Meeting schedule table, a few paragraphs |
| Public Comment | Block editor | The Contact Form 7 form |
| News | Posts | 3 short posts about fictional regional projects |

### The planted barriers

The WCAG success criterion (SC) is included so you learn the numbers as you go. The "How it was planted" column tells you what a vendor did, so you can recognize the same pattern on a real site.

| ID | Where | How it was planted | WCAG SC broken |
|---|---|---|---|
| B01 | Home hero | Use Canva to make a banner with the text "Public Hearing – Oct 30, 6 PM" **baked into the image**. Upload it with **no alt text**. | 1.1.1 Non-text Content, 1.4.5 Images of Text |
| B02 | Home | Elementor **Image Carousel**: 4 large photos whose alt text is just the file name, no arrows or dots, **Autoplay on**, speed 2000, **Pause on hover off**, **Pause on interaction off** | 2.2.2 Pause, Stop, Hide; 1.1.1 |
| B03 | Home | Elementor **HTML** widget: `<div class="fake-btn" onclick="location.href='/public-comment/'" style="background:#0a6;color:#fff;padding:12px 20px;display:inline-block;cursor:pointer">Submit a Comment</div>` | 2.1.1 Keyboard, 4.1.2 Name, Role, Value |
| B04 | Home | Elementor **Social Icons**: 4 icons, size **10px**, spacing **0** | 2.5.8 Target Size (Minimum), *new in 2.2* |
| B05 | Home | Elementor **HTML** widget: `<iframe src="https://www.openstreetmap.org/export/embed.html?bbox=-84.6,33.6,-84.2,33.9" width="600" height="400"></iframe>` (no `title`) | 4.1.2 Name, Role, Value |
| B06 | Whole site | **Appearance → Customize → Global → Colors → Text** set to `#999999` | 1.4.3 Contrast (Minimum) |
| B07 | Whole site | **Customize → Additional CSS**: `a { text-decoration: none; }` (links now differ from text only by color) | 1.4.1 Use of Color |
| B08 | Whole site | **Additional CSS**: `*:focus, *:focus-visible { outline: none !important; box-shadow: none !important; }` | 2.4.7 Focus Visible |
| B09 | Whole site | **Additional CSS**: `#masthead { position: sticky; top: 0; z-index: 999; min-height: 160px; background: #fff; }` | 2.4.11 Focus Not Obscured, *new in 2.2* |
| B10 | About | In Beaver Builder: use **bold paragraphs** as fake headings, and jump from an H2 straight to an H4 | 1.3.1 Info and Relationships |
| B11 | About | Beaver Builder **HTML** module: `<div style="width:900px;border:1px solid #ccc;padding:10px">Notice: The Riverbend Regional Commission board will meet ...(write 3 sentences)</div>` | 1.4.10 Reflow |
| B12 | Meetings | **Table** block with Date / Time / Location / Topic columns. Leave **"Header section" off** so there are no header cells. | 1.3.1 Info and Relationships |
| B13 | Public Comment | Replace the Contact Form 7 form with this, where placeholders are the only labels: `[text* your-name placeholder "Name"] [email* your-email placeholder "Email"] [textarea* your-message placeholder "Your comment"] [submit "Send"]`. In News posts, use **"click here"** as the link text 3 times. | 1.3.1, 3.3.2 Labels or Instructions, 2.4.4 Link Purpose |

It's also slow and heavy, the way legacy sites often are. The photos were uploaded at full camera size (4000px wide, 2–4 MB each). A must-use plugin from the "previous vendor" (`wp-content/mu-plugins/legacy-vendor-tweaks.php`) switched off WordPress's automatic downscaling of large images.

**Want the builder practice?** Pick one barrier, such as B03 or B04. Delete it, then re-create it yourself from the "How it was planted" column. That's a quick way to learn Elementor and Beaver Builder before you fix things in them.

**Checkpoint:** before you open any tools, browse the site as a normal visitor. It probably looks "fine." That's the lesson: most barriers are invisible unless you test for them.

**Explain it:** In your journal, pick two barriers and describe who they affect and how. Examples: a keyboard-only user, a screen reader user, someone with low vision, someone on a phone.

---

## Module 2: Measure the baseline

Measure before you touch anything. Test **Home, About, Meetings and Public Comment**.

- [ ] **Lighthouse:** open the page in an **Incognito** window (so extensions don't skew results) → F12 → Lighthouse → Mode "Navigation", Device "Mobile", tick Performance and Accessibility → Analyze. Record both scores and **LCP** (Largest Contentful Paint).
- [ ] **Page weight:** F12 → Network tab → reload → note the total transferred size (bottom bar).
- [ ] **axe:** F12 → axe DevTools tab → Scan all of my page → record the total issue count.
- [ ] **WAVE:** click the WAVE icon → record the Errors and Contrast Errors counts.
- [ ] Put all numbers in [scorecard.csv](evidence/scorecard.csv) under "Before". Screenshot each report into `evidence/before/`.

**Notice:** the Lighthouse accessibility score may still look fairly high (80s–90s) even with 13 serious barriers. Write down why you think that happens. It's one of the best points you can make in an interview.

---

## Module 3: Automated audit

- [ ] Run **axe** on each page. For every issue, click it to read the explanation and "More info", then add a row to the findings log: page, element, issue, WCAG SC, level (A/AA), Found by = `axe`.
- [ ] Run **WAVE** on each page. Log anything axe missed (Found by = `WAVE`). WAVE "Alerts" aren't always failures; decide for each one and write down your reasoning.
- [ ] Run Accessibility Insights **FastPass** on Home.
- [ ] Tick off which of B01–B13 the tools found. Mark the ones they **missed**.

**Expect:** the scanners will catch **fewer than half** of the 13 planted barriers. Count them yourself. When a tool misses a barrier you know is there, work out **why**. Some clues:
- An image with **empty** alt text (`alt=""`) looks "decorative" to a scanner, even when it holds important information.
- An alt text that's just the file name technically "exists," so some tools pass it.
- Some tools accept a **placeholder** as a field's label, even though it disappears as soon as you type.
- A scanner can't press keys, watch animation or zoom the page.

Automated tools find only part of the problems, often quoted as roughly a third to half. That's why the manual audit matters.

**Explain it:** "Why can't you just run a scanner?" Answer in your journal using your own results: *the tools found X of my 13; they missed …*

---

## Module 4: Manual audit

This is where your real skill shows. Log every finding with Found by = `Keyboard`, `Zoom`, `CCA` or `NVDA`.

### 4a. Keyboard only (unplug or ignore the mouse)

On each page, press **Tab** from the top and move through everything, then go back with **Shift+Tab**. Use **Enter** for links and buttons, **Space** for buttons and checkboxes, and **Esc** for menus.

- [ ] Can you **see** where focus is at all times? (B08)
- [ ] Is the focused item ever **hidden behind the sticky header**? Try Shift+Tab going back up the page. (B09)
- [ ] Can you reach and activate **"Submit a Comment"**? (B03)
- [ ] Can you **stop the carousel**? (B02)
- [ ] Is there a **"Skip to content"** link on the first Tab press?
- [ ] Does the focus order follow the visual order?
- [ ] Do the menus and dropdowns open and close by keyboard?
- [ ] Turn on Accessibility Insights → **Ad hoc tools → Tab stops** and screenshot the path it draws.

### 4b. Zoom and reflow

- [ ] Set Chrome zoom to **200%**. Is all text still readable, with nothing cut off or overlapping?
- [ ] Set the zoom to **400%**. That equals a 320px-wide screen. Do you ever need to scroll **sideways** to read text? (B11)
- [ ] F12 → toggle the device toolbar → iPhone SE. Check the same things.

### 4c. Contrast and color

- [ ] Open the Colour Contrast Analyser, use the eyedropper on the body text (foreground) and the page (background). The ratio must be **4.5:1** for normal text and **3:1** for large text and UI parts. (B06: #999 on white is about 2.8:1, a fail.)
- [ ] Look at links inside paragraphs. Can you tell a link from plain text without seeing color? (B07)

### 4d. Screen reader (NVDA)

Start NVDA with **Ctrl+Alt+N**. The **NVDA key** is **Insert**. Press **Ctrl** to stop speech.

| Key | What it does |
|---|---|
| H / Shift+H | Next / previous heading |
| 1–6 | Next heading at that level |
| K | Next link |
| F | Next form field |
| B | Next button |
| T | Next table |
| G | Next graphic (image) |
| D | Next landmark (header, nav, main, footer) |
| NVDA+F7 | List of all headings, links and landmarks |
| NVDA+Space | Switch between browse mode and focus mode |
| NVDA+Q | Quit NVDA |

- [ ] **Headings:** NVDA+F7 → Headings. Does the list outline the page logically? (B10)
- [ ] **Images:** press G. What does NVDA say for the hero and the carousel? (B01, B02)
- [ ] **Links:** NVDA+F7 → Links. Would "click here" make sense out of context? (B13)
- [ ] **Form:** press F. Does each field announce a name? What happens after you start typing and the placeholder disappears? (B13)
- [ ] **Table:** press T, then Ctrl+Alt+arrow keys to move between cells. Does NVDA read the column header? (B12)
- [ ] **Map:** what does NVDA announce for the iframe? (B05)
- [ ] **Fake button:** does NVDA announce "Submit a Comment" as a button? (B03)

Record short screen recordings (Win+Alt+R with the Xbox Game Bar) of NVDA on the form, before and after. Hearing the difference is very convincing in a presentation.

**Explain it:** In your journal: "What was it like to use the site with only a keyboard and a screen reader?" Keep it honest and concrete. Stories like this are what reviewers remember.

---

## Module 5: WCAG 2.2 checks

ARC asks for "readiness for WCAG 2.2." These are the new A/AA criteria. Check each one and log the result, **including passes**, since a pass is evidence too.

| SC | Name | What to check on this site |
|---|---|---|
| 2.4.11 (AA) | Focus Not Obscured (Minimum) | The sticky header (B09) |
| 2.5.7 (AA) | Dragging Movements | Can the map be used without dragging? (Zoom buttons, or a link to a text list) |
| 2.5.8 (AA) | Target Size (Minimum) | Clickable targets at least 24×24 CSS px, or spaced out enough (B04). Measure in DevTools. |
| 3.2.6 (A) | Consistent Help | If help or contact info appears on several pages, is it in the same place each time? |
| 3.3.7 (A) | Redundant Entry | Does any multi-step form make people type the same information twice? |
| 3.3.8 (AA) | Accessible Authentication (Minimum) | Does the WP login allow password managers and pasting, with no puzzle CAPTCHA? |

Also note: **4.1.1 Parsing was removed** in WCAG 2.2. It's a good detail to mention when asked "what changed?"

---

## Module 6: Prioritize and plan the fixes

Give every finding a severity:

| Severity | Meaning | Example here |
|---|---|---|
| **Critical** | Blocks a task completely for some users | Can't reach "Submit a Comment" by keyboard (B03) |
| **High** | A major barrier; any workaround is hard | Form fields with no labels (B13), no visible focus (B08) |
| **Medium** | Hard, but people can get through | Low contrast (B06), carousel that won't stop (B02) |
| **Low** | An annoyance or best-practice gap | Skipped heading level (B10) |

For each finding, also fill in **Fix location**, meaning where the fix lives:
`Content` (editor) · `Page builder` (Elementor/Beaver settings) · `Theme/CSS` · `Plugin` · `Hosting/config`

- [ ] Sort the log: Critical first, then by effort (quick wins first inside each level).
- [ ] Group the fixes into 3 "releases":
  1. **Blockers**: Critical and High
  2. **Site-wide**: CSS and colors, one change that fixes every page
  3. **Content cleanup**: alt text, headings, link text

**Explain it:** "You have 10 hours this month and 40 findings. What do you fix first, and why?" Write your answer. ARC will ask some version of this.

---

## Module 7: Fix step by step

Work through one finding at a time: **fix → retest with the same tool that found it → after screenshot → update the log** (Status = Fixed, retest result, date).

Don't rebuild pages. Fix the site **in place**, the way ARC wants: "incrementally improve platforms without requiring complete rebuilds."

### Release 1: Blockers

- [ ] **B03 fake button:** replace the HTML widget with Elementor's **Button** widget (a real `<a>` link to /public-comment/). Retest: Tab to it, press Enter, and check that NVDA says "link, Submit a Comment".
- [ ] **B08 no focus outline:** delete that rule from Additional CSS. Then add a strong, consistent focus style:
  ```css
  :focus-visible { outline: 3px solid #1a4f8b; outline-offset: 2px; }
  ```
  Retest: Tab through a whole page.
- [ ] **B13 form labels:** rewrite the Contact Form 7 form with real labels:
  ```
  <label> Name (required)
      [text* your-name autocomplete:name] </label>
  <label> Email (required)
      [email* your-email autocomplete:email] </label>
  <label> Your comment (required)
      [textarea* your-message] </label>
  [submit "Send comment"]
  ```
  Retest with NVDA (F key). Submit the form empty and check that the error messages are announced.
- [ ] **B02 carousel:** in Elementor, turn **Autoplay off**, or keep it on with Pause on hover and Pause on interaction on. Also add alt text to each image in the Media Library. Talking point: autoplay carousels rarely help anyone, so it's fair to suggest removing autoplay.

### Release 2: Site-wide

- [ ] **B06 contrast:** set body text to `#333333` (about 12.6:1). Recheck with the Colour Contrast Analyser.
- [ ] **B07 links by color only:** replace the rule with:
  ```css
  .entry-content a, .elementor-widget-text-editor a { text-decoration: underline; }
  ```
- [ ] **B09 sticky header:** use the easiest fix that works: remove the forced `min-height`, and add
  ```css
  html { scroll-padding-top: 180px; }
  ```
  so focused items scroll into view below the header. Retest with Shift+Tab.
- [ ] **B04 tiny icons:** set the social icon size to at least 24px, with spacing of 8px or more. Also check that each icon has an accessible name.
- [ ] **B11 reflow:** change `width:900px` to `max-width:900px`. Retest at 400% zoom.

### Release 3: Content cleanup

- [ ] **B01 hero:** put the event text into real HTML (an Elementor Heading plus a Text widget) over a plain background image. Give that image empty alt text (`alt=""`) because it's decorative.
- [ ] **B05 map iframe:** add `title="Map of the Riverbend region"`, plus a text link below it, such as "View meeting locations as a list". That also covers 2.5.7.
- [ ] **B10 headings:** in Beaver Builder, change the bold paragraphs to real Heading modules, and fix the H2 → H4 jump.
- [ ] **B12 table:** turn on the **Header section** in the Table block. Add a caption, such as "2026 board meeting schedule".
- [ ] **B13 link text:** change "click here" to descriptive text, such as "Read the Northside Trail project summary".
- [ ] **Performance:** resize the big images to at most 1920px wide, then convert them to WebP. Tools include Squoosh (squoosh.app) or a plugin such as Converter for Media. Lazy-load everything below the fold.

Also work through the extra findings that came from the themes and plugins. Some can't be fixed without editing plugin code. Mark those **"Vendor limitation"** and write down a recommendation, such as replacing the plugin, reporting it to the vendor, or overriding it in a child theme. That's realistic, and it's good consulting.

---

## Module 8: Retest and measure the "after"

- [ ] Rerun **Lighthouse, axe and WAVE** on the same 4 pages. Record the "After" numbers in the scorecard.
- [ ] Repeat the **keyboard pass** and the **NVDA pass** from Module 4 in full.
- [ ] Recheck **400% zoom** and the **mobile** view.
- [ ] Make a **before/after slide** for 3 of the most visual fixes: the focus outline, the form with NVDA, and contrast.
- [ ] Go through every finding. Is each one Fixed, Accepted (with a reason) or a Vendor limitation?

**Explain it:** "A 100 Lighthouse accessibility score: does that mean the site is accessible?" Answer using your own before and after data.

---

## Module 9: Write the audit report

Create `evidence/audit-report.md`, or a Word document, with these sections:

1. **Summary** (half a page): what you tested, the main problems, what you fixed, the before and after numbers
2. **Scope:** pages, browsers, devices, the standard (WCAG 2.1 AA, plus the new 2.2 criteria)
3. **Method:** automated tools, keyboard, zoom and reflow, contrast, NVDA with Chrome
4. **Findings:** the table from your log, grouped by severity
5. **Fixes and results:** before and after screenshots and scores
6. **Remaining issues and recommendations:** vendor limitations, editor training, automated checks on every change
7. **Keeping it accessible:** a short checklist for content editors (alt text, headings, link text, tables)

Also write a one-paragraph **accessibility statement** for the demo site, the kind of page public agencies publish.

**Research for the "Understanding ARC" score:** in April 2024 the US Department of Justice published an ADA Title II rule requiring state and local government websites to meet **WCAG 2.1 AA**, with compliance dates in 2026 and 2027 depending on the entity's size. Look up the current status and which date would apply to a regional commission like ARC. Put 2–3 sentences in the report. This is the "evolving public-sector accessibility obligations" the RFP mentions.

---

## Module 10: Practice presenting

Don't memorize a script. Build answers out of your journal and your evidence.

### Your 60-second story

Use STAR: **Situation** (an inherited site from several vendors), **Task** (reach WCAG 2.1 AA without a rebuild), **Action** (baseline, automated and manual audits, prioritized releases), **Result** (your numbers). Say it out loud, time it, and record yourself. Redo it until it feels natural.

### Questions to answer in your own words

Write bullet-point answers, not paragraphs, then practice them out loud.

- [ ] Walk us through how you audit a website for accessibility.
- [ ] What do automated tools miss? Give an example from your own work.
- [ ] We have limited hours. How do you decide what to fix first?
- [ ] How would you fix an inaccessible page built in Elementor or Divi without rebuilding it?
- [ ] What's new in WCAG 2.2, and what would it mean for our sites?
- [ ] After you fix a site, how do you stop new content from breaking accessibility again?
- [ ] Tell us about a finding that surprised you.
- [ ] How do you work on code another vendor wrote?
- [ ] What does "accessible from the start" mean when you build a new site?

**Tip:** in the interview, show one short before-and-after clip of NVDA reading the form. It makes the case in 20 seconds.
