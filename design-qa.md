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

## Map-first Evidence Atlas redesign (2026-10-13)

**Selected direction:** The user approved the generated map-first Evidence Atlas concept. The public landing experience is now rebuilt around an interactive Nigeria state choropleth, with issue/indicator/period/evidence-layer controls, map/list modes, state detail tabs, source attribution, explicit missing-record styling, and a published-data CSV download. Filament CP and deployment configuration were left unchanged.

**Data and provenance:** The map reads published Laravel records; it does not invent map values. The boundary geometry is the 37-feature Nigeria ADM1 GeoJSON from geoBoundaries gbOpen (2022; CC BY 4.0), served as `/data/nigeria-states.geojson`. The JPEG hero artwork is generated illustration, not documentary evidence. Synthetic/illustrative published records remain labelled in the interface. Resource release/expenditure rows are excluded from allocation totals.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), `php artisan view:cache`, CSV endpoint (HTTP 200), GeoJSON structure (37 features), and `git diff --check` passed. Browser review exercised map/list switching, state selection and detail tabs, issue and resource layers, NGN/USD amounts, and download. Desktop and narrow mobile layouts were inspected; full keyboard traversal and a persisted normalized screenshot comparison remain outstanding. Vite reports the environment's Node 22.11.0 is below its 22.12+ recommendation, and optional `fontaine` is not installed; the build succeeds.

**Status:** The previously recorded “Final result: blocked” applies to the earlier reference-port iteration and is superseded for this selected map-first direction. The approved map-first redesign is implemented locally; no commit, push, or production deployment was performed. The screenshot was reviewed in the browser but not saved into the project.

## Inclusive Future hero iteration (2026-10-13)

**Direction:** Adapted the selected “A clearer picture” concept to the user’s follow-up: a larger branded hero, a brief message, local Inclusive Future imagery, and an animated sequence communicating the links among four evidence layers. This updates only the public EIM hero; it does not alter the CP or data model.

**Implementation:** Replaced the previous compact quote/portrait treatment with a full-width IFI navy hero, gold CTA, concise headline and lede, an Inclusive Future local community photograph, and four direct links to the existing issue/intervention/resource/law map layers. Added a brief movement-only reveal with `prefers-reduced-motion` support. Selecting a layer in the hero updates the map layer; the underlying map values remain sourced from published records. The photo is explicitly described as illustrative.

**Visual review:** The selected source concept image (1487 × 1058 px) and the rendered local page were opened and reviewed. The local page was checked at the browser’s default desktop view and at 390 × 844 mobile. The desktop composition retains the requested editorial hierarchy and connection narrative; the mobile hero stacks the message, photo, and two-column layer links without clipping. The first capture showed reduced opacity during the link animation, so the animation was changed to movement-only; the final desktop capture confirms the labels remain legible. The source and implementation screenshots were not persisted as a normalized side-by-side comparison artifact, so the strict same-input image comparison gate remains blocked.

**Required fidelity surfaces:** Typography retains the project’s Playfair Display/DM Sans pairing, with a short two-line display headline and compact sans-serif copy. Spacing uses a roomy three-part desktop composition and a stacked mobile composition; no overlap was visible. Colors use the linked Inclusive Future palette (navy `#12305b`, gold `#f4d47c`, warm ivory `#fff6ec`) with high-contrast text. The locally sourced group photo is sharp and shows people rather than synthetic evidence or chart content; its crop remains intentionally photographic. Hero copy stays brief and avoids claims about the dataset. Tabler icons provide consistent layer affordances. Browser checks verified the four links appear and selecting Resources changes the map to published resource records. Motion is brief, one-pass, and disabled for reduced-motion preferences; links remain native keyboard-accessible anchors with visible hover/focus treatment.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), `php artisan view:cache`, and `git diff --check` passed. Build output still warns that installed Node 22.11.0 is below Vite’s 22.12+ recommendation and that optional `fontaine` is not installed. Local browser verification passed for desktop and mobile presentation plus the resource-layer action. No commit, push, or production deployment was performed.

**Status:** final result: blocked — implementation and responsive interaction checks passed, but a normalized, persisted source/implementation side-by-side comparison has not been produced.

## Hero composition correction (2026-10-13)

**User clarification:** The selected hero concept is separate from the live atlas map. Keep the interactive data map in its original filter/map/state-detail section. In the hero, use the silhouette of Nigeria as the visual container for the portrait and animate four evidence-layer connections into a shared point in the country.

**Implementation:** Restored the live choropleth to the atlas workspace. The hero now has a concise “Evidence to action” headline, four compact layer links, a decorative 37-state Nigeria outline built from the same geoBoundaries source, a transparent close-up cutout of the headwrapped woman in the supplied photograph, and responsive animated paths that converge on a pulsing central node. The mobile layout keeps the portrait and Nigeria silhouette together and retains the animated connections. The live data map is unchanged and continues to use published records.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), `php artisan view:cache`, and `git diff --check` passed. Browser checks confirmed 37 outline paths, the live map is inside `#atlas-controls`, the connector CSS animation is active, and selecting Resources updates the live map to published resource records. Desktop and 390 × 844 mobile layouts were inspected. Vite still notes Node 22.11.0 is below its 22.12+ recommendation and optional `fontaine` is absent; the build succeeds. No commit, push, or production deployment was performed.

**Status:** The hero direction now follows the user's clarification. A normalized, saved source/implementation screenshot comparison remains outstanding.

## Selected reference hero replacement (2026-10-13)

**Reference and scope:** Replaced only the existing public hero with the user-selected Evidence-to-action artwork. The site navigation and live atlas remain separate and unchanged in function; this does not turn the hero illustration into the data map.

**Implementation:** Added a concise editorial message and map CTA, four linked evidence-layer cards, four animated connector paths, and a warm landscape footer. The supplied map-and-people composition was adapted with a transparent two-woman cutout. The portrait is clipped to the union of the actual 37-state Nigeria geometry, so the country outline contains the image rather than sitting behind a free-standing photo. Each hero card still selects its corresponding live atlas layer. Connector motion and marker pulses respect `prefers-reduced-motion`.

**Visual comparison:** Opened the supplied 1774 × 887 reference and the rendered 1774 × 880 hero together for direct comparison. The implementation preserves the reference's left editorial column, central Nigeria silhouette and people, four surrounding layer cards, dotted links, and pale savanna horizon. The existing navigation stays above the hero, so the rendered hero crop is 7 px shorter than the source. Comparison captures are saved as `docs/design-reviews/hero-reference-source-1774x887.png` and `docs/design-reviews/hero-implementation-1774x880.png`; a separate 1135 × 731 browser crop is `docs/design-reviews/hero-implementation-1135x731.png`.

**Responsive and interaction review:** At 1135 × 807 and 390 × 844, the headline, map-and-portrait composition, and four layer actions remain usable without horizontal overflow. The mobile markers sit off the portrait's face. The Resources hero link changed the atlas filter/title to Resources by state; Issue restored the original burden layer. Browser inspection confirmed 37 state paths, the portrait clip references Nigeria geometry, the connector animation is active, all hero images loaded, and no browser console errors were recorded. The 390 × 844 capture is saved as `docs/design-reviews/hero-mobile-390x844.png`.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), `php artisan view:cache`, and `git diff --check` passed. Vite still warns that the installed Node 22.11.0 is below its 22.12+ recommendation and optional `fontaine` is not installed; the build completes successfully. No commit, push, or production deployment was performed.

**Final result:** passed — the selected hero concept is implemented and directly compared against its saved reference at the same width; mobile overflow and the hero-to-atlas layer interaction were checked.

## Evidence section placement (2026-10-13)

**Change:** Moved “One issue. Four connected layers” to directly follow the hero, before the interactive atlas. The section content and anchors are unchanged; hero and section navigation continue to point to their existing IDs.

**Verification:** Reloaded the local browser after clearing compiled Blade views; the rendered order is hero → evidence layers → interactive atlas. The Evidence navigation still targets `#evidence`, all four layer cards remain present, and map controls still render below them. Build, six feature tests (33 assertions), Blade view caching, and `git diff --check` passed. Vite continues to warn that Node 22.11.0 is below its 22.12+ recommendation and optional `fontaine` is not installed. No commit, push, or production deployment was performed.

## Evidence section spacing and missing-evidence note (2026-10-13)

**Change:** Increased the evidence section's vertical padding to 40 px. Moved the missing-evidence note out of the four cards and placed it beneath the full atlas workspace, spanning the atlas width.

**Verification:** Browser DOM confirms the order `hero → evidence → atlas → missing-evidence note`; evidence section padding computes to 40 px. `npm run build`, six feature tests (33 assertions), Blade view caching, and `git diff --check` passed. Vite still warns about Node 22.11.0 and the optional `fontaine` package. No production deployment was performed.

**Visual refinement:** Added a distinct pale-gold section background (`#f6eddd`) to the four-layer evidence section; cards remain white for contrast.

## Selected “See the ecosystem” hero reference (2026-10-13)

**Reference and scope:** Replaced the existing hero composition with the user-selected artwork in `/Users/dataphytefoundation/Downloads/Nigeria Evidence Atlas_ See the Ecosystem.png` (1774 × 887). The global navigation and atlas below remain separate from the hero.

**Implementation:** Updated the hero copy to “See the ecosystem, not a report about it,” kept the map CTA and four live evidence-layer links, and retained the Nigeria silhouette, portrait, and animated connections. Added a transparent generated Lagos/city skyline asset (`public/images/hero-nigeria-cityscape.png`) along the lower edge to better match the selected reference. The existing landscape treatment and typography remain responsive.

**Visual and interaction review:** Compared the selected reference with the rendered hero in the local browser at its 1135 × 807 viewport. The composition preserves the left copy/CTA, central Nigeria-and-people visual, four evidence cards with animated links, warm ivory palette, and faded cityscape. Browser accessibility state showed the new headline and all four links. Clicking the hero Resources link changed the atlas layer/title to Resources; clicking Issue restored Burden. The local screenshot was inspected in-browser but could not be persisted to `docs/design-reviews`, so this iteration does not have a normalized side-by-side screenshot artifact.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), and `git diff --check` passed. The Vite Node-version and optional `fontaine` warnings remain as documented above. No commit, push, or production deployment was performed.

**Final result:** blocked — the local implementation and layer interaction are verified, but the required persisted source/implementation visual comparison has not been produced.

## Full-image hero revision (2026-10-13)

**User direction:** Use the supplied “See the ecosystem” artwork as the hero background, remove the separately rendered layer cards/animated connectors, and keep the image contained within the hero.

**Implementation and comparison:** The supplied 1774 × 887 PNG is the single hero image, displayed in a fixed 2:1 responsive box capped at 640 px tall. At the 1135 px desktop viewport it renders at 1135 × 567.5 px; at 390 px mobile it renders at 390 × 195 px. The entire image remains visible without cropping, and the next evidence section starts immediately at the hero boundary. The independent HTML cards, map markers, animated paths, and extra skyline layer are not rendered; legacy connector selectors are explicitly animation-disabled. Dotted connections visible within the supplied image are static artwork. A screen-reader heading and description remain in the DOM, and an invisible, keyboard-focusable hotspot preserves the artwork’s “Explore the map” action.

**Interaction and verification:** The map hotspot navigated to `#atlas-controls`. Desktop and 390 px mobile browser checks confirmed the hero dimensions, full-image rendering, no horizontal overflow, and no animation. `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), and `git diff --check` passed. The rendered artwork is the supplied source image, with no crop or spill beyond the hero. Vite reports the existing Node 22.11.0 version warning (22.12+ recommended) and optional `fontaine` warning. No commit, push, or production deployment was performed.

**Final result:** passed — the selected artwork is used as a fixed-height, responsive hero; all connector movement is disabled, and the map CTA remains usable.

## High-resolution layered hero restored (2026-10-13)

**User direction:** Restore the selected composition with its separate Nigeria/map-and-portrait visual, four surrounding evidence cards, connector paths, and skyline, while retaining the newer fixed, responsive hero height. The connector paths should remain static.

**Implementation:** Replaced the single flattened reference image with the layered composition: vector Nigeria state silhouette, high-resolution transparent portrait, four live evidence-layer cards, static dotted connectors, and a faded cityscape. Kept the hero as a bounded responsive section; at the 1135 px desktop viewport it is 567.5 px tall. At mobile widths, the layout stacks the copy and visual and places the four cards in two rows. Connector animation is disabled.

**Visual review:** Compared the selected screenshot with the rendered page in the local browser at 1135 × 807. The composition again follows the reference: copy and CTA at left, map/portrait centered, four cards arranged around the map, and cityscape contained along the lower edge. The restored design is no longer a full-bleed image; the next evidence section starts immediately after the bounded hero. The browser screenshot was inspected live but was not persisted as a file in `docs/design-reviews`.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), and `git diff --check` passed. The resource layer card remains wired to the atlas layer selector in the page. Vite continues to report Node 22.11.0 (22.12+ recommended) and the optional `fontaine` dependency warning. No commit, push, or production deployment was performed.

**Final result:** blocked — the selected composition is restored and visually reviewed, but the required persisted implementation/reference screenshot comparison is not available.

## Hero linkage cards removed (2026-10-13)

**User direction:** Remove all four hero evidence-layer cards.

**Implementation:** Removed the four hero cards and their now-orphaned connector paths and map marker dots. The high-resolution Nigeria/portrait composition, skyline, fixed responsive hero, headline, and map CTA remain. Evidence-layer cards below the hero remain untouched.

**Browser review:** Reloaded the local prototype at 1135 × 807. The hero remains 567.5 px tall; the layered Nigeria/portrait visual and cityscape render; the four hero cards, connector paths, and map markers are absent. Clicking the Resource evidence card below the hero still changes the atlas layer to Resources.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), and `git diff --check` passed. The existing Vite Node-version and optional `fontaine` warnings remain. The live browser screenshot was reviewed, but not saved into `docs/design-reviews`.

**Final result:** blocked — the requested removal is implemented and browser-checked, but no persisted source/implementation screenshot comparison is available.

## Hero alignment and two-line heading (2026-10-13)

**User direction:** Move the Nigeria/map-and-portrait artwork farther right and down so it balances the copy and meets the hero background’s lower edge. Keep the headline on exactly two lines at responsive widths.

**Implementation and browser review:** Split the headline into two non-wrapping responsive lines, adjusted the desktop copy/map grid, and enlarged/repositioned the map artwork. At the local 1135 × 807 browser viewport, the heading renders as two lines without horizontal overflow; the visual is right-aligned and reaches the contained landscape/hero lower boundary. The map and portrait remain inside the hero, and the evidence section starts directly after it. Mobile-specific sizing is retained. The live screenshot was inspected, but could not be persisted to `docs/design-reviews` for a normalized comparison.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), and `git diff --check` passed. Vite emitted the existing Node 22.11.0 recommendation warning (22.12+ required) and optional `fontaine` notice. No commit, push, or production deployment was performed.

**Final result:** blocked — the requested responsive alignment is implemented and visually checked at desktop size, but no persisted implementation/reference comparison has been produced.

## Hero height and atlas workspace spacing (2026-10-13)

**User direction:** Reduce the hero height to remove excess open sky and add `py-18`-equivalent vertical padding to the atlas workspace.

**Implementation and browser review:** Reduced desktop hero height to a responsive 44vw frame, capped at 570 px, with a 480–520 px tablet range; the portrait/map remains anchored to the bottom edge. Added 72 px vertical padding to the atlas workspace (40 px on narrow screens). At 1135 × 807, the hero is 499 px tall and the atlas workspace has 72 px vertical padding. The screenshot was inspected live but could not be persisted to `docs/design-reviews` for a normalized comparison.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), and `git diff --check` passed. Vite emitted the existing Node-version recommendation and optional `fontaine` notice. No commit, push, or production deployment was performed.

**Final result:** blocked — the spacing changes are implemented and browser-checked, but no persisted source/implementation screenshot comparison has been produced.

## Inclusive Futures Initiative header branding (2026-10-13)

**User direction:** Replace the WoCRO header branding with the attached Inclusive Futures Initiative logo.

**Implementation and browser review:** Added the supplied transparent PNG as a dedicated header asset, replaced the header text/brand lockup with the image, and tinted its white artwork navy for contrast on the existing light header. Adjusted image sizing for the compact mobile header. The logo loaded and was visually checked in the local browser at 1135 × 807; the rest of the header and atlas remain unchanged.

**Verification:** `npm run build`, `php artisan test --compact` (6 tests, 33 assertions), and `git diff --check` passed. Vite emitted the existing Node-version recommendation and optional `fontaine` notice. No commit, push, or production deployment was performed.

**Final result:** passed for the requested header replacement; mobile sizing follows the existing breakpoint but was not separately browser-rendered.

## Atlas header and fictional-label cleanup (2026-10-13)

**User direction:** Remove the main navigation and Download data/Nigeria controls, show “Ecosystem Intelligence Map” in their place, and remove the fictional qualifier across public atlas surfaces.

**Implementation and browser review:** Kept the Inclusive Futures Initiative logo, removed the navigation and action controls from the atlas header, and added the requested product name. Public record text is cleaned at the presentation/export boundary, including cached records, without changing stored source data. The local browser accessibility tree showed the product name and no navigation/action controls; the page’s initial public text did not contain “fictional”.

**Verification:** Production build passed. Feature tests cover the replacement header and absence of the qualifier from rendered page and CSV while preserving source records. No commit, push, or production deployment was performed.

## Platform footer and compact header (2026-10-13)

**User direction:** Replace remaining WoCRO footer branding with IFI, add a brief about the platform, remove the boundary-data block, replace the footer disclaimer with copyright/year, and reduce the header height.

**Implementation:** Updated the footer brand to “Inclusive Future Initiatives IFI”; replaced the data note with a concise platform description; removed the boundary-data column; changed the bottom line to “IFI - Ecosystem Intelligence Map” and “© 2026 Inclusive Future Initiatives. All rights reserved.” Reduced the header to 64 px on desktop and 56 px minimum on mobile, with the logo scaled to fit.

**Verification:** The local browser showed the updated footer with the boundary-data block absent and a 64 px desktop header. Build passed, all 6 feature tests passed (38 assertions), and `git diff --check` passed. Mobile sizing is covered by the responsive CSS but was not separately browser-tested. No deployment was performed.
