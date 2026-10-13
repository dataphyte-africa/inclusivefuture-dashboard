<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore issues, interventions, resources, and laws and policies across Nigeria.">
    <title>Ecosystem Intelligence Map · WoCRO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header>
    <div class="wrap">
        <img class="brandmark" alt="Inclusive Futures Initiative" src="{{ asset('images/ifi-brandmark.png') }}">
        <span class="hdr-badge">Published dataset</span>
    </div>
</header>

<main x-data="eimDashboard(window.eimRecords, window.eimStates)" x-init="$watch('activeIssue', value => window.dispatchEvent(new CustomEvent('eim:issue-changed', { detail: { issue: value } })))" @eim:state-selected.window="selectedState = $event.detail.state; activeType = 'issue'; $nextTick(() => document.querySelector('#investigate .investigate-detail')?.scrollIntoView({behavior: 'smooth', block: 'center'}))">
    <section class="hero">
        <svg class="hero-pattern" preserveAspectRatio="none" viewBox="0 0 400 200" aria-hidden="true"><defs><pattern id="hero-hatch" width="26" height="26" patternUnits="userSpaceOnUse" patternTransform="rotate(45)"><path d="M0 13 L13 0 M13 26 L26 13" stroke="#FFF6EC" stroke-width="4"/></pattern></defs><rect width="400" height="200" fill="url(#hero-hatch)"/></svg>
        <div class="wrap">
            <div>
                <div class="eyebrow on-dark">WoCRO · Ecosystem Intelligence Map</div>
                <h1>See the ecosystem, not a report about it.</h1>
                <p class="lead">Explore how issues affecting women, children and young people vary across Nigeria, and see how interventions, resources, laws and policies connect around them.</p>
                <div class="hero-ctas"><a class="btn btn-primary" href="#see">Explore the ecosystem ↓</a><a class="btn btn-ghost" href="#about">About the data</a></div>
            </div>
            <div class="hero-note"><span class="synth-tag on-dark"><span class="dot"></span>Data note</span><p>Only published records appear on this map. A missing record marks a gap in the available evidence; it does not mean the activity is absent.</p></div>
        </div>
    </section>

    <section class="section" id="see">
        <div class="wrap">
            <div class="section-head"><div><div class="eyebrow">01 · See</div><h2><span x-text="activeIssue || 'All issues'"></span>, one layer at a time</h2></div><p class="sub" id="see-summary-copy"></p></div>
            <div class="see-toolbar"><div class="steps" role="tablist" aria-label="Ecosystem layers">
                <button type="button" class="active" role="tab" aria-selected="true" data-see-step="1"><span class="n">1</span>Burden</button>
                <button type="button" role="tab" aria-selected="false" data-see-step="2"><span class="n">2</span>+ Interventions</button>
                <button type="button" role="tab" aria-selected="false" data-see-step="3"><span class="n">3</span>+ Resources</button>
                <button type="button" role="tab" aria-selected="false" data-see-step="4"><span class="n">4</span>+ Laws &amp; Policies</button>
            </div><div class="see-issue-label">Issue <label class="dashboard-filter"><select id="dashboard-issue-filter" x-model="activeIssue" aria-label="Choose issue"><option value="">All issues</option><template x-for="issue in issueAreas" :key="issue"><option :value="issue" x-text="issue"></option></template></select></label></div></div>
            <div class="see-layout">
                <div class="card-block see-reference-chart" role="region" aria-label="Published issue values by state"><div class="see-chart-controls"><label class="dashboard-filter">Indicator <select id="dashboard-indicator-filter" aria-label="Choose indicator"></select></label><span class="synth-tag" id="data-provenance-badge" hidden><span class="dot"></span>Illustrative / synthetic records included</span></div><div id="see-reference-rows" class="seechart" aria-live="polite"></div><div id="see-reference-legend" class="see-legend"></div></div>
                <aside class="see-rail" aria-label="What to notice">
                    <div class="see-summary">
                        <div class="see-rail-head">What to notice</div>
                        <div class="findings-grid" id="see-findings">
                            <button type="button" class="finding-card" id="finding-burden" aria-pressed="false"><span class="fnum">—</span><span class="finding-copy">Issue values by state</span><span class="fstates">Hover to inspect coverage</span></button>
                            <button type="button" class="finding-card" id="finding-resources" aria-pressed="false"><span class="fnum">—</span><span class="finding-copy">Recorded resources</span><span class="fstates">Hover to inspect coverage</span></button>
                            <button type="button" class="finding-card" id="finding-interventions" aria-pressed="false"><span class="fnum">—</span><span class="finding-copy">Interventions recorded</span><span class="fstates">Hover to inspect coverage</span></button>
                            <button type="button" class="finding-card" id="finding-laws" aria-pressed="false"><span class="fnum">—</span><span class="finding-copy">Laws and policies recorded</span><span class="fstates">Hover to inspect coverage</span></button>
                        </div>
                    </div>
                </aside>
            </div>
            <div class="gap-strip">
                <h5>Evidence gaps — not ecosystem gaps</h5>
                <div class="gap-chips" id="issue-gap-states"></div>
                <p class="explain" id="issue-gap-copy"></p>
            </div>
        </div>
    </section>

    <section class="section compare-section" id="compare">
        <div class="wrap">
            <div class="section-head"><div><div class="eyebrow">02 · Compare</div><h2>Compare patterns across states</h2></div><p class="sub">Compare published issue values, interventions, resources and law or policy status. The scatterplot uses matched records; the two-state view also shows where evidence is missing.</p></div>
            <div class="compare-data-filters"><label class="dashboard-filter">Indicator <select id="compare-indicator-filter" aria-label="Compare indicator"></select></label><label class="dashboard-filter">Currency <select id="compare-currency-filter" aria-label="Compare resource currency"></select></label></div>
            <div class="compare-controls">
                <div class="steps compare-modes" id="compare-modes" role="tablist" aria-label="Compare views">
                    <button type="button" class="active" role="tab" aria-selected="true" data-mode="scatter">Burden × Resources</button>
                    <button type="button" role="tab" aria-selected="false" data-mode="two-state">Compare two states</button>
                </div>
                <div class="twostate-pickers" id="twostate-pickers" hidden>
                    <label>State A <select id="compare-state-a" aria-label="State A"></select></label>
                    <label>State B <select id="compare-state-b" aria-label="State B"></select></label>
                </div>
            </div>
            <div class="scatter-wrap" id="scatter-wrap">
                <svg id="compare-scatter" viewBox="0 0 860 460" role="img" aria-label="Scatterplot comparing published issue and resource records by state"></svg>
                <div class="sc-status-legend" id="compare-legend"></div>
            </div>
            <div class="sc-detail" id="compare-detail" aria-live="polite"></div>
            <div class="twostate-block" id="twostate-block" hidden></div>
        </div>
    </section>

    <section class="section resource-section" id="resources">
        <div class="wrap">
            <div class="section-head"><div><div class="eyebrow">03 · Resource flows</div><h2>Where are resources flowing?</h2></div><p class="sub" id="money-summary-copy">Resource totals use published records. Releases and expenditure are shown separately from other resources.</p></div>
            <label class="dashboard-filter">Currency <select id="dashboard-currency-filter" aria-label="Resource currency"></select></label>
            <div class="money-legend" id="money-legend" aria-label="Resource funding category legend"></div>
            <div class="card-block money-block" role="region" aria-label="Published resource flows by state" tabindex="0">
                <div class="money-rows" id="money-rows" role="list"></div>
            </div>
        </div>
    </section>

    <section class="section coverage-section" id="coverage" aria-labelledby="coverage-title">
        <div class="wrap">
            <div class="coverage-band">
                <div class="coverage-head">
                    <h2 id="coverage-title">Coverage</h2>
                    <span class="synth-tag on-dark"><span class="dot"></span>Published records</span>
                </div>
                <div class="coverage-grid">
                    <article class="coverage-stat"><strong id="coverage-issues">0</strong><span>Issues represented</span><p>issues with at least one published record</p></article>
                    <article class="coverage-stat"><strong id="coverage-states">0 of 37</strong><span>States with any record</span><p>for this issue, across all four layers</p></article>
                    <article class="coverage-stat"><strong id="coverage-gaps">0</strong><span>Indicator gaps</span><p>states without a numeric value for this indicator</p></article>
                    <article class="coverage-stat"><strong id="coverage-resources">0</strong><span>Resource records</span><p id="coverage-resource-total">Recorded resources; releases and expenditure are shown separately</p></article>
                </div>
            </div>
        </div>
    </section>

    <section class="records-band investigate-section" id="investigate" aria-labelledby="investigate-title">
        <div class="wrap">
            <div class="section-head investigate-head">
                <div><div class="eyebrow">04 · Investigate</div><h2 id="investigate-title">Investigate the connections</h2></div>
                <p class="sub">Explore issues, intervention types, resource flows and laws and policies across states. Select a state or filter the data to examine the records behind the patterns.</p>
            </div>

            <div class="controls-bar" aria-label="Investigate filters">
                <label class="issue-filter">Issue
                    <select x-model="activeIssue" aria-label="Filter by issue">
                        <option value="">All issues</option>
                        <template x-for="issue in issueAreas" :key="issue"><option :value="issue" x-text="issue"></option></template>
                    </select>
                </label>
                <div class="seg investigate-tabs" role="tablist" aria-label="Data layer">
                    <button type="button" role="tab" :aria-selected="activeType === 'issue'" :class="{active: activeType === 'issue'}" @click="activeType = 'issue'; selectedState = ''">Issues</button>
                    <button type="button" role="tab" :aria-selected="activeType === 'intervention'" :class="{active: activeType === 'intervention'}" @click="activeType = 'intervention'; selectedState = ''">Interventions</button>
                    <button type="button" role="tab" :aria-selected="activeType === 'resource'" :class="{active: activeType === 'resource'}" @click="activeType = 'resource'; selectedState = ''">Resources</button>
                    <button type="button" role="tab" :aria-selected="activeType === 'law'" :class="{active: activeType === 'law'}" @click="activeType = 'law'; selectedState = ''">Laws &amp; Policies</button>
                </div>
                <button type="button" class="clear-btn" @click="activeIssue = initialIssue; activeType = 'law'; selectedState = ''">Reset</button>
            </div>

            <div class="investigate-chart card-block" :class="'layer-' + activeType" role="region" :aria-label="stateChartTitle" tabindex="0">
                <div class="investigate-chart-head">
                    <h3 x-text="stateChartTitle"></h3>
                    <span><i></i>Published records <b x-text="stateRows.reduce((total, row) => total + row.count, 0)"></b></span>
                </div>
                <p class="investigate-chart-note">Bars show the number of published records in this layer, not a measure of need or intervention strength. A missing record is an evidence gap, not a zero.</p>
                <div class="investigate-state-list" role="list">
                    <template x-for="row in stateRows" :key="row.name">
                        <button type="button" class="investigate-state-row" role="listitem" :class="{'is-selected': selectedState === row.name}" :aria-pressed="selectedState === row.name" :title="row.name + ': ' + row.summary" @click="selectedState = selectedState === row.name ? '' : row.name">
                            <span class="investigate-state-name" x-text="row.name"></span>
                            <span class="investigate-state-track" aria-hidden="true"><span :class="['investigate-state-fill', 'fill-' + activeType, {'is-empty': !row.count}]" :style="'width:' + row.width + '%' "></span></span>
                            <span class="investigate-state-summary">
                                <span class="investigate-status" :class="'status-' + row.statusClass" x-text="row.count ? row.summary : 'No record available'"></span>
                                <span class="investigate-record-count" x-text="row.count ? (activeType === 'law' ? (row.count === 1 ? '1 instrument' : '+' + (row.count - 1) + ' more instrument' + (row.count === 2 ? '' : 's')) : row.count + ' recorded') : 'Evidence gap'"></span>
                            </span>
                        </button>
                    </template>
                    <div class="investigate-empty" x-show="stateRows.every(row => !row.count)">No published records match this issue and layer yet. This is a coverage gap in the current dataset, not evidence that activity is absent.</div>
                </div>
            </div>

            <div class="investigate-lower-grid">
                <aside class="investigate-detail" :class="{'has-selection': selectedState}" aria-live="polite">
                    <template x-if="selectedState">
                        <div>
                            <div class="eyebrow on-dark" x-text="selectedStateRow?.zoneName + ' · ' + (activeIssue || 'All issues')"></div>
                            <h3 x-text="selectedState"></h3>
                            <div class="investigate-detail-stats">
                                <template x-for="stat in selectedStateLayerCounts" :key="stat.type">
                                    <div class="investigate-detail-stat"><b x-text="stat.count"></b><span x-text="stat.label"></span></div>
                                </template>
                            </div>
                            <section class="investigate-detail-block">
                                <h4>Burden</h4>
                                <template x-if="selectedStateBurdenRows.length">
                                    <div><template x-for="(row, index) in selectedStateBurdenRows" :key="index"><div class="investigate-detail-row"><span x-text="row.title"></span><b x-text="row.value"></b></div></template></div>
                                </template>
                                <p class="investigate-detail-empty-copy" x-show="!selectedStateBurdenRows.length">No numeric value is published for this state and issue. This is an evidence gap, not a zero.</p>
                            </section>
                            <section class="investigate-detail-block">
                                <h4>Intervention types</h4>
                                <p class="investigate-detail-intro" x-show="selectedStateInterventionRows.length" x-text="selectedStateInterventionRows.length + ' intervention' + (selectedStateInterventionRows.length === 1 ? '' : 's') + ' recorded'"></p>
                                <template x-for="row in selectedStateInterventionTypes" :key="row.label"><div class="investigate-detail-row"><span x-text="row.label"></span><b x-text="row.count"></b></div></template>
                                <details class="investigate-detail-drill" x-show="selectedStateInterventionRows.length">
                                    <summary x-text="'Show underlying programmes (' + selectedStateInterventionRows.length + ')'"></summary>
                                    <template x-for="record in selectedStateInterventionRows" :key="record.record_key"><div class="investigate-detail-record"><span><b x-text="record.title"></b><small x-text="record.details.implementer || record.details.source_name || 'Implementer not recorded'"></small></span><details class="investigate-record-fields"><summary>View fields</summary><dl><template x-for="(value, field) in record.details" :key="field"><div><dt x-text="field.replaceAll('_', ' ')"></dt><dd x-text="value || 'Not provided'"></dd></div></template></dl></details></div></template>
                                </details>
                                <p class="investigate-detail-empty-copy" x-show="!selectedStateInterventionRows.length">No intervention recorded for this state under the selected issue.</p>
                            </section>
                            <section class="investigate-detail-block">
                                <h4>Resources</h4>
                                <template x-for="row in selectedStateResourceRows" :key="row.category + row.currency"><div class="investigate-detail-row"><span x-text="row.category"></span><b x-text="formatAmount(row.amount, row.currency)"></b></div></template>
                                <details class="investigate-detail-drill" x-show="selectedStateResourceRows.length">
                                    <summary x-text="'Show underlying records (' + selectedStateLayerRecords.filter(record => record.type === 'resource').length + ')'"></summary>
                                    <template x-for="record in selectedStateLayerRecords.filter(record => record.type === 'resource')" :key="record.record_key"><div class="investigate-detail-record"><span><b x-text="record.title"></b><small x-text="record.details.funder_mda || record.details.source_name || 'Funder not recorded'"></small></span><details class="investigate-record-fields"><summary>View fields</summary><dl><template x-for="(value, field) in record.details" :key="field"><div><dt x-text="field.replaceAll('_', ' ')"></dt><dd x-text="value || 'Not provided'"></dd></div></template></dl></details></div></template>
                                </details>
                                <p class="investigate-detail-empty-copy" x-show="!selectedStateResourceRows.length">No resource record is published for this state and issue.</p>
                            </section>
                            <section class="investigate-detail-block">
                                <h4>Laws &amp; policies</h4>
                                <template x-for="record in selectedStateLawRows" :key="record.record_key"><div class="investigate-detail-law"><div><b x-text="record.title"></b><span x-text="[record.details.instrument_category, record.details.jurisdiction].filter(Boolean).join(' · ') || 'Classification not recorded'"></span></div><span class="investigate-status" :class="'status-' + statusClass(record.details.status_label || '')" x-text="record.details.status_label || 'Status not recorded'"></span><details class="investigate-record-fields"><summary>View fields</summary><dl><template x-for="(value, field) in record.details" :key="field"><div><dt x-text="field.replaceAll('_', ' ')"></dt><dd x-text="value || 'Not provided'"></dd></div></template></dl></details></div></template>
                                <p class="investigate-detail-empty-copy" x-show="!selectedStateLawRows.length">No law or policy record for this state under the selected issue.</p>
                            </section>
                            <button type="button" class="investigate-clear-state" @click="selectedState = ''">Clear state selection</button>
                        </div>
                    </template>
                    <div class="investigate-detail-empty" x-show="!selectedState"><span class="investigate-detail-kicker">STATE DETAIL</span><h3>Investigate a connection</h3><p>Select a state from the chart to see the records behind its pattern.</p></div>
                </aside>
            </div>

            <div class="investigate-law-map card-block" role="region" aria-label="State Child Rights and VAPP law status map">
                <div class="investigate-law-legend" aria-label="Law status legend">
                    <span><i class="law-status-swatch law-status-missing"></i>Not recorded</span>
                    <span><i class="law-status-swatch law-status-domesticated"></i>Domesticated (in force)</span>
                    <span><i class="law-status-swatch law-status-pending"></i>Domestication pending</span>
                    <span><i class="law-status-swatch law-status-not-adopted"></i>Not adopted</span>
                </div>
                <p class="investigate-law-map-caption">(State Child Rights / VAPP Law — the headline legislative signal; open a state to see every instrument on record)</p>
                <div class="investigate-law-zone-grid">
                    <template x-for="zone in stateZones" :key="zone.key">
                        <section class="investigate-law-zone" :class="'zone-' + zone.key.toLocaleLowerCase()" :aria-label="zone.name">
                            <h4 x-text="zone.name"></h4>
                            <div class="investigate-law-tiles">
                                <template x-for="row in stateLawMapRows.filter(row => row.zone === zone.key)" :key="row.name">
                                    <button type="button" class="investigate-law-tile" :class="['law-status-' + row.statusClass, {'is-selected': selectedState === row.name}]" :title="row.name + ' · ' + row.status" :aria-label="row.name + ': ' + row.status + '. Select state to inspect all matching records.'" :aria-pressed="selectedState === row.name" @click="selectedState = selectedState === row.name ? '' : row.name; if (selectedState) $nextTick(() => document.querySelector('.investigate-detail')?.scrollIntoView({behavior: 'smooth', block: 'nearest'}))">
                                        <span x-text="row.abbreviation"></span>
                                    </button>
                                </template>
                            </div>
                        </section>
                    </template>
                </div>
                <p class="investigate-law-map-note" x-show="stateLawMapRows.every(row => row.statusClass === 'missing')">No matching state Child Rights / VAPP legislative records are currently published for this issue, so all state tiles are shown as “Not recorded.”</p>
                <p class="investigate-law-map-note">Hover any tile for its exact status. Select a state to inspect every instrument in the panel above. A muted tile means no matching legislative record is currently published for that state under this issue filter — it does not mean no activity exists there.</p>
            </div>

        </div>
    </section>

    <footer class="site-footer" id="about"><div class="shell footer-layout"><div><a class="brand brand-footer" href="/"><span class="brand-mark">E</span><span>WoCRO <small>ECOSYSTEM INTELLIGENCE MAP</small></span></a><p>Explore the ecosystem, understand the evidence, and see where the gaps remain.</p></div><div><h4>About the data</h4><p>Records appear on this map after they are published. Where available, use each record’s source and reporting period to understand its context.</p></div><div><h4>Understanding gaps</h4><p>A missing record means no matching evidence is currently published for this selection. It does not prove that the activity is absent.</p></div></div><div class="shell footer-bottom">IFI · Ecosystem Intelligence Map within WoCRO <span>Published data · check each record’s source and period</span></div></footer>
</main>
<div id="dashboard-tooltip" class="dashboard-tooltip" role="tooltip" aria-hidden="true" hidden></div>
<script>window.eimRecords = @js($records); window.eimStates = @js($states);</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const records = window.eimRecords || [];
    const states = window.eimStates || [];
    const chart = document.getElementById('see-reference-rows');
    const legend = document.getElementById('see-reference-legend');
    const issueFilter = document.getElementById('dashboard-issue-filter');
    const indicatorFilter = document.getElementById('dashboard-indicator-filter');
    const currencyFilter = document.getElementById('dashboard-currency-filter');
    const compareCurrencyFilter = document.getElementById('compare-currency-filter');
    const compareIndicatorFilter = document.getElementById('compare-indicator-filter');
    let step = 1;
    let selectedIssue = issueFilter?.value || '';
    let selectedIndicator = '';
    let selectedCurrency = '';
    let selectedEvidenceState = '';
    const issueRows = () => records.filter((record) => record.type === 'issue' && (!selectedIssue || record.issue_area === selectedIssue));
    const hasNumericValue = (record) => record.details.value !== null && record.details.value !== undefined && String(record.details.value).trim() !== '' && Number.isFinite(Number(record.details.value));
    const resourceRows = () => records.filter((record) => record.type === 'resource' && (!selectedIssue || record.issue_area === selectedIssue));
    const money = (amount, currency) => `${currency === 'NGN' ? '₦' : currency} ${Number(amount || 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char]);
    const cleanUnit = (unit) => String(unit || '').replace(/\s*[,;]?\s*(?:synthetic|illustrative|fictional)\b[^)]*\)?\s*$/i, '').replace(/\s*\($/, '').replace(/\s*%/g, '%').trim();
    const formatIssueValue = (value, unit) => {
        const normalizedUnit = cleanUnit(unit);
        const formattedValue = Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 });
        return `${formattedValue}${normalizedUnit.startsWith('%') ? '%' : normalizedUnit ? ` ${normalizedUnit}` : ''}`;
    };
    const setOptions = (select, values, selected) => {
        if (!select) return;
        select.innerHTML = values.map((value) => `<option value="${escapeHtml(value)}">${escapeHtml(value)}</option>`).join('');
        if (values.includes(selected)) select.value = selected;
    };
    const updateSelectors = () => {
        const indicators = [...new Set(issueRows().filter(hasNumericValue).map((record) => record.title).filter(Boolean))].sort();
        const currencies = [...new Set(resourceRows().map((record) => record.details.currency).filter(Boolean))].sort();
        if (!indicators.includes(selectedIndicator)) selectedIndicator = indicators[0] || '';
        if (!currencies.includes(selectedCurrency)) selectedCurrency = currencies[0] || '';
        setOptions(indicatorFilter, indicators.length ? indicators : ['No published indicators'], selectedIndicator);
        setOptions(compareIndicatorFilter, indicators.length ? indicators : ['No published indicators'], selectedIndicator);
        setOptions(currencyFilter, currencies.length ? currencies : ['No published currencies'], selectedCurrency);
        setOptions(compareCurrencyFilter, currencies.length ? currencies : ['No published currencies'], selectedCurrency);
    };
    const selectedIssueRecords = () => records.filter((record) => !selectedIssue || record.issue_area === selectedIssue);
    const renderGapsAndCoverage = () => {
        const statesWithAny = new Set(selectedIssueRecords().map((record) => record.state).filter(Boolean).map((state) => state.toLowerCase()));
        const statesWithIssue = new Set(issueRows().filter((record) => record.title === selectedIndicator && hasNumericValue(record)).map((record) => record.state).filter(Boolean).map((state) => state.toLowerCase()));
        const gaps = states.filter(([name]) => !statesWithIssue.has(name.toLowerCase()));
        const uniqueIssues = [...new Set(records.map((record) => record.issue_area).filter(Boolean))];
        const selectedResources = resourceRows();
        const hasIllustrativeRecords = selectedIssueRecords().some((record) => /synthetic|illustrative/i.test([record.details.data_type, record.details.source_name, record.details.notes].filter(Boolean).join(' ')));
        const resourceTotals = selectedResources.filter((record) => !/expenditure|release|disbursement/i.test(record.details.resource_category || '')).reduce((totals, record) => {
            const currency = record.details.currency || 'Unspecified';
            totals[currency] = (totals[currency] || 0) + Number(record.details.amount || 0);
            return totals;
        }, {});
        document.getElementById('see-summary-copy').textContent = `${statesWithIssue.size} of ${states.length} states have a numeric published value for this indicator. Choose another layer above to add its published records to the view.`;
        document.getElementById('issue-gap-states').innerHTML = gaps.map(([name]) => `<button type="button" class="gap-chip" data-state="${escapeHtml(name)}" aria-label="Inspect published evidence for ${escapeHtml(name)}">${escapeHtml(name)}</button>`).join('') || '<span class="gap-chip">No gaps</span>';
        document.getElementById('issue-gap-copy').innerHTML = `No numeric value for <b>${escapeHtml(selectedIndicator || 'the selected indicator')}</b> is currently published for ${gaps.length} of ${states.length} states${selectedIssue ? ` under ${escapeHtml(selectedIssue)}` : ''}. This is a coverage gap in the dataset, not evidence that activity is absent.`;
        document.getElementById('coverage-issues').textContent = String(uniqueIssues.length);
        document.getElementById('coverage-states').textContent = `${statesWithAny.size} of ${states.length}`;
        document.getElementById('coverage-gaps').textContent = String(gaps.length);
        document.getElementById('coverage-resources').textContent = String(selectedResources.length);
        const resourceTotalLabel = Object.entries(resourceTotals).map(([currency, amount]) => money(amount, currency)).join(' + ');
        document.getElementById('coverage-resource-total').textContent = resourceTotalLabel
            ? `${resourceTotalLabel} in recorded resources; releases and expenditure are shown separately`
            : 'No published resource amounts; releases and expenditure are shown separately';
        const provenanceBadge = document.getElementById('data-provenance-badge');
        provenanceBadge.hidden = !hasIllustrativeRecords;
        const publishedInterventions = selectedIssueRecords().filter((record) => record.type === 'intervention');
        const publishedLaws = selectedIssueRecords().filter((record) => record.type === 'law');
        const stateNamesFor = (rows) => [...new Set(rows.map((record) => record.state).filter(Boolean).map((state) => states.find(([name]) => name.toLowerCase() === state.toLowerCase())?.[0] || state))].sort();
        const setFinding = (id, number, copy, rows, detail) => {
            const button = document.getElementById(id);
            const names = stateNamesFor(rows);
            button.querySelector('.fnum').textContent = String(number);
            button.querySelector('.finding-copy').textContent = copy;
            button.querySelector('.fstates').textContent = names.length ? `${names.length} ${names.length === 1 ? 'state' : 'states'} · hover for details` : 'No published records for this selection';
            button.dataset.states = names.join('|');
            button.dataset.tip = detail;
            button.classList.toggle('locked', names.length === 0);
            button.setAttribute('aria-label', `${number} ${copy}. ${names.length} states. Hover or focus to see details.`);
        };
        const issueSelection = issueRows().filter((record) => record.title === selectedIndicator && hasNumericValue(record));
        const resourceTotalText = Object.entries(resourceTotals).map(([currency, amount]) => money(amount, currency)).join(' + ');
        setFinding('finding-burden', statesWithIssue.size, `${selectedIndicator || 'Issue'} values recorded`, issueSelection, `${issueSelection.length} published issue records for ${selectedIndicator || 'this indicator'} across ${statesWithIssue.size} states.`);
        setFinding('finding-resources', selectedResources.length, 'Resource records · ' + (resourceTotalText || 'no published total'), selectedResources, `${selectedResources.length} resource records. Recorded resources total ${resourceTotalText || 'no recorded amount'}; releases and expenditure are shown separately.`);
        setFinding('finding-interventions', publishedInterventions.length, 'Intervention records published', publishedInterventions, `${publishedInterventions.length} intervention records across ${stateNamesFor(publishedInterventions).length} states.`);
        setFinding('finding-laws', publishedLaws.length, 'Law and policy records published', publishedLaws, `${publishedLaws.length} law and policy records across ${stateNamesFor(publishedLaws).length} states.`);
    };
    const display = () => {
        updateSelectors();
        document.querySelectorAll('[data-see-step]').forEach((button) => {
            const active = Number(button.dataset.seeStep) === step;
            button.classList.toggle('active', active);
            button.classList.toggle('done', Number(button.dataset.seeStep) < step);
            button.setAttribute('aria-selected', String(active));
        });
        const valuesByState = new Map();
        issueRows().filter((record) => record.title === selectedIndicator && hasNumericValue(record)).forEach((record) => {
            const key = record.state?.toLowerCase();
            const previous = valuesByState.get(key);
            if (!previous || String(record.details.period || '').localeCompare(String(previous.details.period || '')) > 0) valuesByState.set(key, record);
        });
        const values = states.map(([name]) => ({ name, record: valuesByState.get(name.toLowerCase()) })).sort((first, second) => Number(second.record?.details.value || 0) - Number(first.record?.details.value || 0));
        const maxIssueValue = Math.max(1, ...values.map(({ record }) => Number(record?.details.value || 0)));
        const maxInterventions = Math.max(1, ...states.map(([name]) => records.filter((record) => record.type === 'intervention' && (!selectedIssue || record.issue_area === selectedIssue) && record.state?.toLowerCase() === name.toLowerCase()).length));
        const resourcesByState = resourceRows().filter((record) => record.details.currency === selectedCurrency).reduce((totals, record) => {
            const state = record.state?.toLowerCase();
            totals[state] = (totals[state] || 0) + Number(record.details.amount || 0);
            return totals;
        }, {});
        const maxStateResources = Math.max(1, ...Object.values(resourcesByState));
        chart.innerHTML = values.map(({ name, record }) => {
            const value = Number(record?.details.value || 0);
            const matches = records.filter((item) => item.state?.toLowerCase() === name.toLowerCase() && (!selectedIssue || item.issue_area === selectedIssue));
            const interventions = matches.filter((record) => record.type === 'intervention').length;
            const laws = matches.filter((record) => record.type === 'law').length;
            const largestCurrencyTotal = resourcesByState[name.toLowerCase()] || 0;
            const currencyLabel = selectedCurrency && largestCurrencyTotal ? `${selectedCurrency} ${largestCurrencyTotal.toLocaleString()}` : '';
            const unit = cleanUnit(record?.details.unit || '');
            const valueLabel = record ? formatIssueValue(value, unit) : 'No record';
            const accessibleSummary = `${name}: ${valueLabel}; ${interventions} interventions; ${currencyLabel || 'no recorded resources'}; ${laws} laws or policies. Select to inspect all published records for this state and issue.`;
            return `<button type="button" class="see-row${record ? '' : ' is-empty'}" data-state="${escapeHtml(name)}" aria-label="${escapeHtml(accessibleSummary)}" aria-pressed="${String(selectedEvidenceState.toLocaleLowerCase() === name.toLocaleLowerCase())}"><span class="srlabel">${escapeHtml(name)}</span><div class="srbars"><div class="srtrack"><div class="srfill" style="width:${record ? value / maxIssueValue * 100 : 0}%"></div></div>${step >= 2 ? `<div class="srtrack layer-track"><div class="srfill" style="width:${interventions / maxInterventions * 100}%"></div></div>` : ''}${step >= 3 ? `<div class="srtrack fund-track"><div class="srfill fund-fill" style="width:${largestCurrencyTotal / maxStateResources * 100}%"></div></div>` : ''}</div><div class="srmeta"><span class="srval">${escapeHtml(valueLabel)}</span>${step >= 2 ? `<span class="see-interv-badge${interventions ? '' : ' zero'}">${interventions} recorded</span>` : ''}${step >= 3 ? `<span class="srval resource-val">${escapeHtml(currencyLabel || '—')}</span>` : ''}${step >= 4 ? `<span class="see-interv-badge${laws ? '' : ' zero'}">${laws} instrument${laws === 1 ? '' : 's'}</span>` : ''}</div></button>`;
        }).join('');
        legend.innerHTML = `<span><i class="sw"></i>${escapeHtml(selectedIndicator || 'No published indicator')} · published values</span>`
            + (step >= 2 ? '<span><i class="sw sw-interventions"></i>Interventions recorded in published data</span>' : '')
            + (step >= 3 ? '<span><i class="sw sw-resources"></i>Recorded resources</span>' : '')
            + (step >= 4 ? '<span>Laws and policies recorded in published data</span>' : '');
        renderGapsAndCoverage();
    };
    document.querySelectorAll('[data-see-step]').forEach((button) => button.addEventListener('click', () => { step = Number(button.dataset.seeStep); display(); }));
    const selectEvidenceState = (state) => {
        selectedEvidenceState = state;
        document.querySelectorAll('#see-reference-rows .see-row[data-state]').forEach((row) => {
            row.setAttribute('aria-pressed', String(row.dataset.state.toLocaleLowerCase() === state.toLocaleLowerCase()));
        });
        window.dispatchEvent(new CustomEvent('eim:state-selected', { detail: { state } }));
    };
    document.getElementById('see-reference-rows')?.addEventListener('click', (event) => {
        const row = event.target.closest('.see-row[data-state]');
        if (row) selectEvidenceState(row.dataset.state);
    });
    document.getElementById('issue-gap-states')?.addEventListener('click', (event) => {
        const chip = event.target.closest('.gap-chip[data-state]');
        if (!chip) return;
        selectEvidenceState(chip.dataset.state);
    });
    const tooltip = document.getElementById('dashboard-tooltip');
    const showTooltip = (html, x, y) => {
        tooltip.innerHTML = html;
        tooltip.hidden = false;
        tooltip.setAttribute('aria-hidden', 'false');
        const left = Math.min(Math.max(8, x + 14), window.innerWidth - 270);
        const top = Math.min(Math.max(8, y + 14), window.innerHeight - tooltip.offsetHeight - 12);
        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;
    };
    const hideTooltip = () => { tooltip.hidden = true; tooltip.setAttribute('aria-hidden', 'true'); };
    const stateEvidenceTip = (state) => {
        const sameState = records.filter((record) => record.state?.toLowerCase() === state.toLowerCase() && (!selectedIssue || record.issue_area === selectedIssue));
        const issue = sameState.filter((record) => record.type === 'issue' && record.title === selectedIndicator && hasNumericValue(record)).sort((a, b) => String(b.details.period || '').localeCompare(String(a.details.period || '')))[0];
        const issueUnit = cleanUnit(issue?.details.unit || '');
        const readableIssueValue = issue ? escapeHtml(formatIssueValue(issue.details.value, issueUnit)) : 'No numeric record';
        const parts = [`<strong>${escapeHtml(state)}</strong>`, `<div class="tt-row"><span>${escapeHtml(selectedIndicator || 'Burden')}</span><b>${readableIssueValue}</b></div>`];
        if (issueUnit.includes('%') && issueUnit.trim() !== '%') parts.push(`<div class="tt-muted">${escapeHtml(issueUnit.replace('%', '').trim())}</div>`);
        if (issue?.details.period) parts.push(`<div class="tt-muted">${escapeHtml(issue.details.period)}${issue.details.source_name ? ` · ${escapeHtml(issue.details.source_name)}` : ''}</div>`);
        if (step >= 2) {
            const rows = sameState.filter((record) => record.type === 'intervention');
            const types = [...new Set(rows.map((record) => record.details.intervention_type || record.title).filter(Boolean))];
            parts.push(`<div class="tt-row"><span>Interventions</span><b>${rows.length}</b></div>${types.length ? `<div class="tt-muted">${types.slice(0, 3).map(escapeHtml).join(' · ')}</div>` : ''}`);
        }
        if (step >= 3) {
            const rows = sameState.filter((record) => record.type === 'resource');
            const byCurrency = rows.reduce((totals, record) => { const currency = record.details.currency || 'Unspecified'; totals[currency] = (totals[currency] || 0) + Number(record.details.amount || 0); return totals; }, {});
            parts.push(`<div class="tt-row"><span>Resources</span><b>${rows.length} records</b></div>${Object.entries(byCurrency).slice(0, 3).map(([currency, amount]) => `<div class="tt-muted">${escapeHtml(money(amount, currency))}</div>`).join('') || '<div class="tt-muted">No published resources</div>'}`);
        }
        if (step >= 4) {
            const rows = sameState.filter((record) => record.type === 'law');
            parts.push(`<div class="tt-row"><span>Law instruments</span><b>${rows.length}</b></div>${rows.slice(0, 3).map((record) => `<div class="tt-muted">${escapeHtml(record.title || 'Instrument')}${record.details.status ? ` · ${escapeHtml(record.details.status)}` : ''}</div>`).join('') || '<div class="tt-muted">No published instruments</div>'}`);
        }
        return parts.join('');
    };
    const findingContainer = document.getElementById('see-findings');
    let pinnedFinding = '';
    const applyFindingHighlight = (finding) => {
        const active = finding || pinnedFinding;
        const names = active ? (active.dataset.states || '').split('|').map((name) => name.toLowerCase()) : [];
        document.querySelectorAll('#see-reference-rows .see-row[data-state]').forEach((row) => {
            const matches = names.includes(row.dataset.state.toLowerCase());
            row.classList.toggle('finding-highlight', Boolean(active && matches));
            row.classList.toggle('finding-dim', Boolean(active && !matches));
        });
        document.querySelectorAll('#see-findings .finding-card').forEach((card) => {
            const selected = card === pinnedFinding;
            card.classList.toggle('selected', selected);
            card.setAttribute('aria-pressed', String(selected));
        });
    };
    findingContainer?.addEventListener('pointerover', (event) => {
        const card = event.target.closest('.finding-card:not(.locked)');
        if (!card) return;
        applyFindingHighlight(card);
        showTooltip(`<strong>${escapeHtml(card.querySelector('.finding-copy').textContent)}</strong><div class="tt-muted">${escapeHtml(card.dataset.tip)}</div><div class="tt-muted">${escapeHtml((card.dataset.states || '').split('|').join(', '))}</div>`, event.clientX, event.clientY);
    });
    findingContainer?.addEventListener('pointermove', (event) => { if (event.target.closest('.finding-card')) { const card = event.target.closest('.finding-card'); const rect = tooltip.getBoundingClientRect(); tooltip.style.left = `${Math.min(event.clientX + 14, window.innerWidth - rect.width - 8)}px`; tooltip.style.top = `${Math.min(event.clientY + 14, window.innerHeight - rect.height - 8)}px`; } });
    findingContainer?.addEventListener('pointerout', (event) => { if (event.target.closest('.finding-card') && !event.target.closest('.finding-card').contains(event.relatedTarget)) { applyFindingHighlight(); hideTooltip(); } });
    findingContainer?.addEventListener('focusin', (event) => { const card = event.target.closest('.finding-card:not(.locked)'); if (!card) return; applyFindingHighlight(card); const rect = card.getBoundingClientRect(); showTooltip(`<strong>${escapeHtml(card.querySelector('.finding-copy').textContent)}</strong><div class="tt-muted">${escapeHtml(card.dataset.tip)}</div><div class="tt-muted">${escapeHtml((card.dataset.states || '').split('|').join(', '))}</div>`, rect.left, rect.bottom); });
    findingContainer?.addEventListener('focusout', (event) => { if (event.target.closest('.finding-card')) { applyFindingHighlight(); hideTooltip(); } });
    findingContainer?.addEventListener('click', (event) => { const card = event.target.closest('.finding-card:not(.locked)'); if (!card) return; pinnedFinding = pinnedFinding === card ? '' : card; applyFindingHighlight(); });
    chart?.addEventListener('pointerover', (event) => { const row = event.target.closest('.see-row[data-state]'); if (row) showTooltip(stateEvidenceTip(row.dataset.state), event.clientX, event.clientY); });
    chart?.addEventListener('pointermove', (event) => { if (event.target.closest('.see-row')) { const rect = tooltip.getBoundingClientRect(); tooltip.style.left = `${Math.min(event.clientX + 14, window.innerWidth - rect.width - 8)}px`; tooltip.style.top = `${Math.min(event.clientY + 14, window.innerHeight - rect.height - 8)}px`; } });
    chart?.addEventListener('pointerout', (event) => { if (event.target.closest('.see-row') && !event.target.closest('.see-row').contains(event.relatedTarget)) hideTooltip(); });
    chart?.addEventListener('focusin', (event) => { const row = event.target.closest('.see-row[data-state]'); if (!row) return; const rect = row.getBoundingClientRect(); showTooltip(stateEvidenceTip(row.dataset.state), rect.left, rect.bottom); });
    chart?.addEventListener('focusout', (event) => { if (event.target.closest('.see-row')) hideTooltip(); });
    issueFilter?.addEventListener('change', () => { selectedIssue = issueFilter.value; updateSelectors(); display(); });
    indicatorFilter?.addEventListener('change', () => {
        selectedIndicator = indicatorFilter.value;
        display();
        rebuildComparisonSample();
        renderScatter();
        renderPointDetail();
        renderTwoStates();
    });
    currencyFilter?.addEventListener('change', () => { selectedCurrency = currencyFilter.value; display(); rebuildComparisonSample(); renderScatter(); renderPointDetail(); renderTwoStates(); window.dispatchEvent(new Event('eim:resources-changed')); });
    compareCurrencyFilter?.addEventListener('change', () => { selectedCurrency = compareCurrencyFilter.value; if (currencyFilter) currencyFilter.value = selectedCurrency; display(); rebuildComparisonSample(); renderScatter(); renderPointDetail(); renderTwoStates(); window.dispatchEvent(new Event('eim:resources-changed')); });
    compareIndicatorFilter?.addEventListener('change', () => { selectedIndicator = compareIndicatorFilter.value; if (indicatorFilter) indicatorFilter.value = selectedIndicator; display(); rebuildComparisonSample(); renderScatter(); renderPointDetail(); renderTwoStates(); });
    display();

    let comparisonSample = [];
    let comparisonOptionsSample = [];
    const scatter = document.getElementById('compare-scatter');
    const detail = document.getElementById('compare-detail');
    const pickerWrap = document.getElementById('twostate-pickers');
    const scatterWrap = document.getElementById('scatter-wrap');
    const comparisonBlock = document.getElementById('twostate-block');
    const stateA = document.getElementById('compare-state-a');
    const stateB = document.getElementById('compare-state-b');
    let selectedPoint = '';
    const rebuildComparisonSample = () => {
        const issuesByState = new Map();
        issueRows().filter((record) => record.title === selectedIndicator && hasNumericValue(record)).forEach((record) => {
            const key = record.state.toLocaleLowerCase();
            const previous = issuesByState.get(key);
            if (!previous || String(record.details.period || '').localeCompare(String(previous.details.period || '')) > 0) issuesByState.set(key, record);
        });
        const matchingResources = resourceRows().filter((record) => record.details.currency === selectedCurrency);
        const resourcesByState = new Map();
        matchingResources.forEach((record) => {
            const key = record.state.toLocaleLowerCase();
            resourcesByState.set(key, [...(resourcesByState.get(key) || []), record]);
        });
        const availableStateNames = states.map(([name]) => name).filter((name) => issuesByState.has(name.toLocaleLowerCase()) || resourcesByState.has(name.toLocaleLowerCase()));
        comparisonOptionsSample = availableStateNames.map((name) => {
            const key = name.toLocaleLowerCase();
            const resources = resourcesByState.get(key) || [];
            const laws = records.filter((record) => record.type === 'law' && record.state?.toLocaleLowerCase() === key && (!selectedIssue || record.issue_area === selectedIssue));
            const interventions = records.filter((record) => record.type === 'intervention' && record.state?.toLocaleLowerCase() === key && (!selectedIssue || record.issue_area === selectedIssue));
            const issue = issuesByState.get(key);
            const status = laws.sort((first, second) => String(first.details.enactment_date || '').localeCompare(String(second.details.enactment_date || ''))).at(-1)?.details.status_label || 'No law record';
            return { id: key.replace(/[^a-z0-9]+/g, '-'), name, burden: issue ? Number(issue.details.value) : null, unit: cleanUnit(issue?.details.unit || ''), resources: resources.length ? resources.reduce((sum, record) => sum + Number(record.details.amount || 0), 0) : null, interventions: interventions.length, status, hasBurden: Boolean(issue), hasResources: resources.length > 0 };
        });
        comparisonSample = comparisonOptionsSample.filter((row) => row.hasBurden && row.hasResources);
        const optionsMarkup = comparisonOptionsSample.length
            ? comparisonOptionsSample.map((row) => {
                const burden = row.hasBurden ? formatIssueValue(row.burden, row.unit) : 'No indicator value';
                const resources = row.hasResources ? `${selectedCurrency} resources recorded` : `No ${selectedCurrency} resource data`;
                return `<option value="${escapeHtml(row.id)}">${escapeHtml(row.name)} — ${escapeHtml(burden)} · ${escapeHtml(resources)}</option>`;
            }).join('')
            : '<option value="" disabled selected>No published records for this selection</option>';
        stateA.innerHTML = optionsMarkup;
        stateB.innerHTML = stateA.innerHTML;
        if (comparisonOptionsSample.length) {
            stateA.value = comparisonOptionsSample[0].id;
            stateB.value = (comparisonOptionsSample[1] || comparisonOptionsSample[0]).id;
        }
    };
    const median = (values) => { const sorted = [...values].sort((a,b)=>a-b); const mid = Math.floor(sorted.length / 2); return sorted.length % 2 ? sorted[mid] : (sorted[mid-1] + sorted[mid]) / 2; };
    const renderScatter = () => {
        const width=860,height=460,left=64,right=30,top=26,bottom=54,plotW=width-left-right,plotH=height-top-bottom;
        if (!comparisonSample.length) {
            scatter.innerHTML = `<text class="sc-axis-label" x="${width / 2}" y="${height / 2}" text-anchor="middle">No published issue and resource records are available to compare for this selection yet.</text>`;
            return;
        }
        const maxBurden = Math.max(...comparisonSample.map((row) => row.burden), 1);
        const maxResources = Math.max(...comparisonSample.map((row) => row.resources), 1);
        const x=value=>left+value/maxBurden*plotW, y=value=>top+plotH-value/maxResources*plotH;
        const medX=x(median(comparisonSample.map(d=>d.burden))),medY=y(median(comparisonSample.map(d=>d.resources)));
        const maxInterventions=Math.max(...comparisonSample.map(d=>d.interventions),1);
        let markup=`<line class="sc-grid-line" x1="${left}" y1="${top+plotH}" x2="${left+plotW}" y2="${top+plotH}"/><line class="sc-grid-line" x1="${left}" y1="${top}" x2="${left}" y2="${top+plotH}"/><line class="sc-median" x1="${medX}" y1="${top}" x2="${medX}" y2="${top+plotH}"/><line class="sc-median" x1="${left}" y1="${medY}" x2="${left+plotW}" y2="${medY}"/><text class="sc-axis-label" x="${left}" y="${height-14}">${escapeHtml(selectedIndicator)} (${escapeHtml(comparisonSample[0].unit)})</text><text class="sc-axis-label" x="${left+plotW}" y="${height-14}" text-anchor="end">Higher ${escapeHtml(selectedIndicator)} →</text><text class="sc-axis-label" transform="translate(18,${top+plotH}) rotate(-90)">← Less ${escapeHtml(selectedCurrency)} resources</text><text class="sc-axis-label" transform="translate(18,${top}) rotate(-90)" text-anchor="end">More resources →</text>`;
        comparisonSample.forEach(d=>{
            const radius=7+Math.sqrt(d.interventions/maxInterventions)*17;
            const normalizedStatus = d.status.toLocaleLowerCase();
            const color=normalizedStatus.includes('domesticated') && !normalizedStatus.includes('pending') ? '#0ca30c' : normalizedStatus.includes('pending') || normalizedStatus.includes('draft') ? '#d9a418' : normalizedStatus.includes('not adopted') ? '#e34948' : '#c8c2b4';
            const label=`${d.name}: ${formatIssueValue(d.burden, d.unit)} issue value, ${money(d.resources, selectedCurrency)} recorded resources, ${d.status}, ${d.interventions} interventions`;
            markup+=`<circle class="sc-dot${selectedPoint&&selectedPoint!==d.id?' dim':''}" data-state="${d.id}" cx="${x(d.burden)}" cy="${y(d.resources)}" r="${radius}" fill="${color}" stroke="#fff" stroke-width="1.5" tabindex="0" role="button" aria-label="${label}"><title>${label}</title></circle>`;
        });
        scatter.innerHTML=markup;
        scatter.querySelectorAll('.sc-dot').forEach(dot=>{
            const select=()=>{selectedPoint=selectedPoint===dot.dataset.state?'':dot.dataset.state;renderScatter();renderPointDetail();};
            dot.addEventListener('click',select);dot.addEventListener('keydown',event=>{if(event.key==='Enter'||event.key===' '){event.preventDefault();select();}});
        });
        document.getElementById('compare-legend').innerHTML=`<span>Horizontal: ${escapeHtml(selectedIndicator || 'Issue value')}</span><span>Vertical: ${escapeHtml(selectedCurrency)} resource amount</span><span class="dot-note">Dot size = intervention records; color reflects latest published law status</span>`;
    };
    const renderPointDetail=()=>{
        const d=comparisonSample.find(item=>item.id===selectedPoint);
        if(!d){detail.className='sc-detail';detail.innerHTML='';return;}
        detail.className='sc-detail show';
        detail.innerHTML=`<div class="sd-item"><b>${escapeHtml(d.name)}</b><span>selected state · published records</span></div><div class="sd-item"><b>${escapeHtml(formatIssueValue(d.burden, d.unit))}</b><span>${escapeHtml(selectedIndicator)}</span></div><div class="sd-item"><b>${money(d.resources, selectedCurrency)}</b><span>recorded resources</span></div><div class="sd-item"><b>${escapeHtml(d.status)}</b><span>latest published law status</span></div><div class="sd-item"><b>${d.interventions}</b><span>interventions recorded</span></div>`;
    };
    const stateColumn=d=>`<article class="ts-col"><h3>${escapeHtml(d.name)}</h3><p class="ts-burden">${escapeHtml(selectedIndicator)}: <b>${d.hasBurden ? escapeHtml(formatIssueValue(d.burden, d.unit)) : 'No published numeric value'}</b></p><div class="ts-block"><h4>Interventions</h4><div class="ts-row">${d.interventions} recorded intervention${d.interventions===1?'':'s'}</div></div><div class="ts-block"><h4>Resources</h4><div class="ts-row">${d.hasResources ? `${money(d.resources, selectedCurrency)} recorded` : `No ${escapeHtml(selectedCurrency)} resource data on record`}</div></div><div class="ts-block"><h4>Latest published law status</h4><div class="ts-row"><span class="sw" style="background:${d.status.toLocaleLowerCase().includes('domesticated') && !d.status.toLocaleLowerCase().includes('pending') ? '#0ca30c' : d.status.toLocaleLowerCase().includes('pending') || d.status.toLocaleLowerCase().includes('draft') ? '#d9a418' : d.status.toLocaleLowerCase().includes('not adopted') ? '#e34948' : '#c8c2b4'}"></span>${escapeHtml(d.status)}</div></div></article>`;
    const renderTwoStates=()=>{
        if (!comparisonOptionsSample.length) {
            comparisonBlock.innerHTML='<p class="ts-intro">No published issue or resource records are available for this selection yet.</p>';
            return;
        }
        const a=comparisonOptionsSample.find(d=>d.id===stateA.value),b=comparisonOptionsSample.find(d=>d.id===stateB.value);
        if (!a || !b) return;
        const context = a.hasBurden && b.hasBurden
            ? `${escapeHtml(a.name)} and ${escapeHtml(b.name)} differ by ${Math.abs(a.burden-b.burden).toLocaleString()}${a.unit.includes('%') ? ' percentage points' : ` ${escapeHtml(a.unit || 'units')}`} for ${escapeHtml(selectedIndicator)}.`
            : `Published evidence is incomplete for this pair: ${escapeHtml(a.name)} ${a.hasBurden ? 'has a numeric burden record' : 'has no numeric burden record'}; ${escapeHtml(b.name)} ${b.hasBurden ? 'has one' : 'has no numeric burden record'}.`;
        comparisonBlock.innerHTML=`<p class="ts-intro">${context} Missing records are shown as evidence gaps, not zero. Values below use published records only.</p><div class="ts-grid">${stateColumn(a)}${stateColumn(b)}</div>`;
    };
    const setCompareMode=mode=>{
        document.querySelectorAll('#compare-modes [data-mode]').forEach(button=>{const active=button.dataset.mode===mode;button.classList.toggle('active',active);button.setAttribute('aria-selected',String(active));});
        const isTwoState=mode==='two-state';scatterWrap.hidden=isTwoState;detail.hidden=isTwoState;pickerWrap.hidden=!isTwoState;comparisonBlock.hidden=!isTwoState;
        if(isTwoState)renderTwoStates();else{renderScatter();renderPointDetail();}
    };
    document.querySelectorAll('#compare-modes [data-mode]').forEach(button=>button.addEventListener('click',()=>setCompareMode(button.dataset.mode)));
    stateA.addEventListener('change',renderTwoStates);stateB.addEventListener('change',renderTwoStates);
    rebuildComparisonSample();
    renderScatter();
    window.addEventListener('eim:issue-changed', (event) => {
        selectedIssue = event.detail.issue || '';
        selectedPoint = '';
        updateSelectors();
        display();
        rebuildComparisonSample();
        renderScatter();
        renderPointDetail();
        renderTwoStates();
        window.dispatchEvent(new Event('eim:resources-changed'));
    });
});
</script>
</body>
</html>
