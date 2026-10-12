# Design QA — WoCRO / EIM

**Source visual truth:** `/Users/dataphytefoundation/Herd/if-dashboard/IFI Ecosystem Intelligence Map (EIM) (1).html` and the two user-annotated screenshots in the task.

**Implementation:** Laravel public dashboard, local Herd preview in the Codex in-app browser.

**Viewport and state:** Earlier See/Compare reviews used a 919 × 817 CSS-pixel viewport. The Resource Flows implementation was captured at 1280 × 817 CSS px; the Investigation section was captured in the active in-app browser at 919 × 817 CSS px, at `#investigate`, with Girls' Education and Laws & Policies selected. The annotated source image is 1280 × 1140 px and its CSS viewport/device density is unavailable, so the two cannot be normalized conclusively.

**Implementation screenshot:** Resource Flows and Investigation were captured and visually inspected in the active browser, but neither screenshot was persisted to a local project file. The source HTML file could not be reopened through the selected browser's file-URL policy; the user's supplied annotated screenshot remains the source evidence. This remains an evidence-handling blocker for a QA pass.

**Latest annotation iteration:** The See chart and Resource Flows chart are keyboard-focusable, fixed-height scrolling regions (`560px` desktop; `480px` narrow screens). Compare follows the source scatterplot composition with its two modes and state-law legend. Resource Flows follows the source's ranked stacked-bar design and separates releases from the allocation total.

## Findings

- [P1] Investigation's published sample is much sparser than the illustrative source fixture.
  Location: `#investigate`, state chart and regional index in `resources/views/dashboard.blade.php`.
  Evidence: the source screenshot shows multiple recorded law/policy statuses across states; the published Laravel starter dataset currently has one Girls' Education law/policy record, so the other states correctly render as evidence gaps.
  Impact: the structure follows the source, but the visible chart density differs substantially from the selected reference state.
  Fix: ingest the full reviewed sample dataset through the CP pipeline before expecting its state-by-state distribution to match; keep absent records explicitly labelled as gaps rather than inventing visual values.
- [P2] Footer composition remains different from the source.
  Location: footer in `resources/views/dashboard.blade.php`.
  Evidence: the source footer presents three informational columns and a separate synthetic-data line; the Laravel footer retains its current brand-led layout.
  Impact: the page still diverges after Investigation.
  Fix: port the source footer composition in a later reference pass.

- [P2] Only desktop-sized behavior was compared.
  Location: public dashboard responsive breakpoints.
  Evidence: implementation was captured at one 919 × 817 viewport; mobile and tablet captures were not made.
  Impact: wrapping and chart readability at narrow widths remain unverified.
  Fix: capture and check the same top/See states at mobile and tablet widths after the full-page port.

- [P2] Summary metrics are reference-fixture values rather than aggregates of the current published Laravel records.
  Location: “What to notice” cards beneath `#see`.
  Evidence: values (6, 50%, 4 and 3) and their state lists match the supplied illustrative reference; the current starter database contains only a few published rows. The cards reveal progressively and highlight the corresponding reference-chart rows, while the additional bar badges use database records.
  Impact: synthetic story statistics and live published-record counts can appear to describe one shared dataset when they do not.
  Fix: when the full sample dataset is ingested, calculate these cards from that same dataset; until then keep the synthetic nature prominent or replace the stats with summaries derived from currently published records.

## Required fidelity surfaces

- **Fonts and typography:** Figtree is now explicitly loaded and applied. The hero and section title/lead hierarchy were visibly reviewed at the reference viewport; narrow-width wrapping remains unverified.
- **Spacing and layout rhythm:** The hero's two-column grid, content order, wrapping pattern, CTAs, right-side synthetic-data note, section heading, segmented steps and ranked bars now follow the supplied source. Remaining sections are a material mismatch (P1 above).
- **Colors and tokens:** Navy, cream, jasmine, Elena and the source muted text palette are applied to the reference-aligned region. Remaining sections still retain older green-theme components in places.
- **Image quality and asset fidelity:** The original IFI brandmark was extracted from the supplied source and used as `public/images/ifi-brandmark.png`. The provided hatch SVG geometry was reused, not replaced with invented artwork.
- **Copy and content:** Hero, findings cards, gap-strip copy and the Girls' Education step order follow the source. The 24 burden values and insight-card figures are illustrative reference values; intervention, resource and law row badges read published Laravel records.
- **Interactions/accessibility:** All four tabs were operated. Selected/`aria-selected` state updates and additional layers appear cumulatively. Unlocking and clicking a summary card highlighted six matching chart rows. The imported-record explorer remained present. No browser console errors were reported. Responsive states and full keyboard traversal remain unverified.

## Comparison history

- Initial app view differed materially: green/orbit hero and an unrelated four-card first section. Replaced that hero and section with the source composition, original logo asset, matching text and CTAs, cumulative four-step controls, and ranked-state chart.
- Post-fix evidence: source annotation and rendered implementation were both opened; implementation visibly shows the navy patterned hero and the matching Girls' Education chart hierarchy. The four tabs were exercised; their accessible state changed and extra layer data appeared. The screen capture was not saved as a file, so a reproducible screenshot path is unavailable.
- Summary iteration: added the source's 2 × 2 “What to notice” card group and striped evidence-gap panel beneath the chart. At step 3, the first summary card unlocked and clicking it highlighted six rows; the other gated cards stayed subdued until their required step. The browser screenshot was captured in the current browser session but not persisted to disk.
- Scrolling iteration: constrained the chart card to 560px (480px at narrow widths), enabled vertical scrolling and keyboard focus, and confirmed the content scrolls inside the card (`scrollTop` reached 260.5px while the fixed height remained 560px).
- Compare iteration: replaced the prior region-tile block with the reference scatterplot; added the two-state comparison mode and pickers, synthetic reference status colors and size-by-intervention bubbles, accessible bubble selection/details, and responsive two-column state summaries. Browser review confirmed 24 plotted states, bubble selection details, state A/B updates, return to scatter mode, and no browser console errors. The chart uses the source's synthetic Girls' Education fixture; it is not yet computed from the ingested Laravel records. Screenshot reviewed in-browser but not persisted as a project artifact.
- Resource Flows iteration: replaced the unrelated category chart/callout with the reference heading and copy, category legend, funding-ranked horizontal stacks, compact state totals, and separate government release notes with release-to-allocation percentages. The source's 41 synthetic Girls' Education resource rows aggregate to the reference total of ₦1,480m across 22 states; the screenshot and browser accessibility tree confirmed the same total, top-ranked Lagos/Bauchi/Katsina/Rivers rows, all 22 rows, and no console errors. This visual is intentionally the synthetic reference fixture, not a live roll-up of the small published CP sample. The in-browser capture is not saved to a project-local image path, and its 1280 × 817 CSS viewport is wider than the source annotation's stated 919 × 817 CSS viewport.
- Resource scroll iteration: constrained the resource card to 560px on desktop and 480px below 640px, enabled vertical overflow, overscroll containment, a visible keyboard-focus outline, and tab focus. Browser inspection measured a fixed 560px rendered card (`557px` client height) with `864px` of content; keyboard PageDown moved the internal scroll position to `307.5px` while the card height stayed fixed.
- Investigation iteration: replaced the old filter-plus-record-list section as its primary view with the source heading/copy, issue selector, four layer tabs, reset control, scrollable state-by-state layer summary, six-region state index, and selected-state record panel. The complete published record explorer, search, starter CSV links, and expandable field details remain below it. Browser interaction checks switched from Laws & Policies to Interventions and selected Kano; the chart updated to the intervention layer and the detail panel listed Kano's published record. No browser console errors were reported. At the reference default, the current DB sample exposes one matching law/policy record; the rest are labelled evidence gaps. The local screenshot is 919 × 817 CSS px; source screenshot density cannot be normalized, and neither capture was persisted as a local image.
- Selected-state detail iteration: replaced the compact one-layer detail list with the reference's full-width navy panel composition: region/issue kicker, selected state, four layer-count tiles, then burden, intervention types, resource aggregates and law/policy instruments. Counts and values derive from the selected state's current published records; missing layers remain explicit evidence gaps. Intervention and resource drilldowns link back to the corresponding expandable source record, and law rows expose a direct field link. Browser accessibility/content inspection selected Kano and confirmed all four live layer counts plus one record in each layer; the current starter rows are sparse and do not reproduce the reference's FCT synthetic values. No console errors were reported. The implementation screenshot was not persisted, so same-size image comparison remains blocked.
- Coverage iteration: inserted the dark reference band between Resource Flows and Investigate, with its synthetic-data badge and four source metrics/captions. The rebuilt local page’s accessibility tree confirmed all four values (`5`, `24 of 37`, `4`, `63`) and section order. These remain source-fixture metrics, not aggregates of the starter Laravel records. Build and Blade view caching succeeded; Vite warned that Node 22.11.0 is below its 22.12+ recommendation and that optional `fontaine` is absent. The browser screenshot was inspected but not saved to a project-local image path.
- Historical note (2026-10-12): the preceding SQLite test-failure statement describes the pre-MySQL setup and is superseded. The app now uses MySQL for local and test databases; `Tests/Feature/ExampleTest.php` refreshes the dedicated test schema. All five tests pass against MySQL.
- Public record browser removal: removed the separate “Published dataset / Underlying records” card, search field, and public starter-CSV links per review. The dashboard continues to show data in its visualizations and selected-state detail; individual intervention, resource and law records now expose their fields inline rather than linking to the removed browser. This does not change Filament ingestion or data management. `npm run build` and `php artisan view:cache` succeeded. Live-browser visual/interaction verification was not completed after the earlier browser reload timeout.

## Implementation checklist

- [x] Match annotated hero layout and source palette.
- [x] Match annotated Girls' Education section and layer-step interaction.
- [x] Add “What to notice” cards and evidence-gap strip in the annotated position.
- [x] Gate findings by layer and highlight associated ranked rows on selection.
- [x] Keep dashboard visualization and state-detail data backed by published records; ingestion remains managed through Filament.
- [x] Port the annotated Compare section and both comparison modes.
- [x] Port the annotated Resource Flows section and separate government releases from allocation totals.
- [x] Port the annotated Coverage band and its four synthetic summary metrics.
- [x] Port the annotated Investigation filters, layer-by-state visualization, regional index, and four-layer selected-state detail panel.
- [x] Add the annotated six-zone State Child Rights / VAPP law-status map, backed by matching published law records and explicit missing-record treatment.
- [ ] Port the rest of the source page composition and visualizations.
- [ ] Save normalized source/implementation comparison screenshots in the project.
- [ ] Verify tablet and mobile layouts.

**Final result:** blocked — the rest of the page remains visibly different from the reference; the findings still combine reference-fixture statistics with the starter database; and the implementation screenshot was not persisted to the project.
