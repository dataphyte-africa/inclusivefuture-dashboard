import '@tabler/icons-webfont/dist/tabler-icons.min.css';
import { geoPath, geoTransform } from 'd3-geo';

const records = window.eimRecords || [];
const states = window.eimStates || [];
const colors = ['#dcecf8', '#b7d7ef', '#6fa8dc', '#407fb8', '#12305b'];
const stateAliases = new Map([
    ['fct', 'Abuja Federal Capital Territory'], ['fct abuja', 'Abuja Federal Capital Territory'],
    ['abuja', 'Abuja Federal Capital Territory'], ['cross river', 'Cross River'],
]);
const key = (value) => String(value || '').toLocaleLowerCase().replace(/[^a-z0-9]/g, '');
const canonical = (name) => stateAliases.get(String(name || '').toLocaleLowerCase()) || name;
const allIssues = [...new Set(records.map((r) => r.issue_area).filter(Boolean))].sort();
const issueRows = () => records.filter((r) => r.type === 'issue' && (!issueSelect.value || r.issue_area === issueSelect.value));
const issueSelect = document.querySelector('#issue-filter');
const indicatorSelect = document.querySelector('#indicator-filter');
const periodSelect = document.querySelector('#period-filter');
const currencySelect = document.querySelector('#currency-filter');
const layerSelect = document.querySelector('#layer-filter');
const indicatorHelp = document.querySelector('#indicator-filter-help');
const periodHelp = document.querySelector('#period-filter-help');
const currencyHelp = document.querySelector('#currency-filter-help');
const layerHelp = document.querySelector('#layer-filter-help');
const mapSvg = document.querySelector('#nigeria-map');
const mapCanvas = document.querySelector('#map-canvas');
const heroMapSvg = document.querySelector('#hero-nigeria-map');
const panel = document.querySelector('#state-panel');
let geo = null;
let selectedState = null;
let activeTab = 'overview';
let zoom = 1;
const periodChoices = { issue: '', resource: '' };
let currencyChoice = '';

const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
const normalizeType = (type) => ({ law: 'law', issue: 'issue', resource: 'resource', intervention: 'intervention' })[type];
const getRecordsForState = (state) => records.filter((record) => key(canonical(record.state)) === key(canonical(state)) && (!issueSelect.value || record.issue_area === issueSelect.value));
const numeric = (record) => {
    const value = Number(record?.details?.value);
    return record && record.details?.value !== null && record.details?.value !== '' && Number.isFinite(value) ? value : null;
};
const formatNumber = (value, maximumFractionDigits = 1) => Number(value).toLocaleString(undefined, { maximumFractionDigits });
const money = (value, currency = 'NGN') => `${currency === 'NGN' ? '₦' : `${currency} `}${formatNumber(value, 0)}`;
const cleanUnit = (unit) => String(unit || '').replace(/\s*[,;]?\s*(?:synthetic|illustrative|fictional)\b[^)]*\)?\s*$/i, '').replace(/\s*\($/, '').replace(/\s*%/g, '%').trim();
const isSampleLabel = (value) => /synthetic|illustrative|fictional/i.test(String(value || ''));
const issueValueLabel = (value, unit) => `${formatNumber(value)}${cleanUnit(unit).startsWith('%') ? '%' : cleanUnit(unit) ? ` ${cleanUnit(unit)}` : ''}`;
const isReleaseCategory = (category) => /expenditure|release|disbursement/i.test(category || '');

function fillSelect(select, options, chosen) {
    select.innerHTML = options.map((option) => `<option value="${escapeHtml(option.value)}" ${option.value === chosen ? 'selected' : ''}>${escapeHtml(option.label)}</option>`).join('');
}

function initFilters() {
    fillSelect(issueSelect, [{ value: '', label: 'All issue areas' }, ...allIssues.map((value) => ({ value, label: value }))], allIssues.includes('Girls\' Education') ? 'Girls\' Education' : allIssues[0] || '');
    const refreshIndicators = () => {
        const previousChoice = indicatorSelect.value;
        const indicators = [...new Set(issueRows().filter((row) => numeric(row) !== null).map((row) => row.title).filter(Boolean))].sort();
        fillSelect(
            indicatorSelect,
            indicators.length ? indicators.map((value) => ({ value, label: value })) : [{ value: '', label: 'No indicators available' }],
            indicators.includes(previousChoice) ? previousChoice : indicators[0] || '',
        );
    };
    issueSelect.addEventListener('change', () => { refreshIndicators(); render(); });
    refreshIndicators();
    indicatorSelect.addEventListener('change', render);
    periodSelect.addEventListener('change', () => {
        if (['issue', 'resource'].includes(layerSelect.value)) periodChoices[layerSelect.value] = periodSelect.value;
        render();
    });
    currencySelect.addEventListener('change', () => { currencyChoice = currencySelect.value; render(); });
    layerSelect.addEventListener('change', () => { selectedState && renderStatePanel(); render(); });
}

function activeRecordsForState(state) {
    if (layerSelect.value === 'issue') {
        return issueRecordsForState(state);
    }
    return getRecordsForState(state).filter((r) => r.type === layerSelect.value);
}

function issueRecordsForState(state) {
    const selected = getRecordsForState(state).filter((r) => r.type === 'issue' && r.title === indicatorSelect.value);
    if (layerSelect.value === 'issue' && periodSelect.value) {
        return selected.filter((r) => String(r.details?.period || '') === periodSelect.value);
    }
    const latestByKey = new Map();
    selected.forEach((r) => {
        const prev = latestByKey.get(r.title);
        if (!prev || String(r.details?.period || '').localeCompare(String(prev.details?.period || '')) > 0) latestByKey.set(r.title, r);
    });
    return [...latestByKey.values()];
}

function valueForState(state) {
    const matches = activeRecordsForState(state);
    if (layerSelect.value === 'issue') return numeric(matches[0]);
    if (layerSelect.value === 'resource') {
        const currency = currencySelect.value;
        const allocations = matches.filter((r) => !isReleaseCategory(r.details?.resource_category)
            && (!currency || (r.details?.currency || 'Unspecified') === currency)
            && (!periodSelect.value || String(r.details?.period || r.details?.allocation_year || '') === periodSelect.value));
        return allocations.length ? allocations.reduce((sum, r) => sum + Number(r.details?.amount || 0), 0) : null;
    }
    return matches.length || null;
}

function stateNameForFeature(feature) {
    const shapeName = feature.properties.shapeName;
    return shapeName === 'Abuja Federal Capital Territory' ? 'FCT (Abuja)' : shapeName;
}

function colorScale(values, value) {
    const sorted = [...values].sort((a, b) => a - b);
    if (value === null || !sorted.length) return 'url(#missing-pattern)';
    const rank = sorted.filter((n) => n <= value).length / sorted.length;
    return colors[Math.min(colors.length - 1, Math.floor(rank * colors.length))];
}

function renderMap() {
    if (!geo) return;
    const width = Math.max(320, mapCanvas.clientWidth);
    const height = Math.max(390, Math.min(600, width * 0.8));
    mapSvg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    mapSvg.innerHTML = '';
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    defs.innerHTML = '<pattern id="missing-pattern" width="7" height="7" patternTransform="rotate(45)" patternUnits="userSpaceOnUse"><rect width="7" height="7" fill="#eeeeeb"></rect><path d="M0 0V7" stroke="#d8d8d4" stroke-width="2"></path></pattern><filter id="state-shadow" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="1" stdDeviation="1.3" flood-color="#6f5144" flood-opacity=".22"/></filter>';
    mapSvg.append(defs);
    const coordinatePairs = [];
    const collectCoordinates = (coordinates) => {
        if (Array.isArray(coordinates) && coordinates.length >= 2 && typeof coordinates[0] === 'number' && typeof coordinates[1] === 'number') coordinatePairs.push(coordinates);
        else if (Array.isArray(coordinates)) coordinates.forEach(collectCoordinates);
    };
    geo.features.forEach((feature) => collectCoordinates(feature.geometry.coordinates));
    const bounds = coordinatePairs.reduce((result, point) => [Math.min(result[0], point[0]), Math.min(result[1], point[1]), Math.max(result[2], point[0]), Math.max(result[3], point[1])], [Infinity, Infinity, -Infinity, -Infinity]);
    const mapScale = Math.min((width - 36) / (bounds[2] - bounds[0]), (height - 36) / (bounds[3] - bounds[1]));
    const offsetX = (width - (bounds[2] - bounds[0]) * mapScale) / 2;
    const offsetY = (height - (bounds[3] - bounds[1]) * mapScale) / 2;
    const projection = geoTransform({ point(longitude, latitude) { this.stream.point(offsetX + (longitude - bounds[0]) * mapScale, height - offsetY - (latitude - bounds[1]) * mapScale); } });
    const path = geoPath(projection);
    const vals = states.map(([name]) => valueForState(name)).filter((v) => v !== null);
    geo.features.forEach((feature) => {
        const name = stateNameForFeature(feature);
        const state = states.find(([n]) => key(n) === key(name))?.[0] || name;
        const value = valueForState(state);
        const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        group.classList.add('map-state');
        group.setAttribute('role', 'button');
        group.setAttribute('tabindex', '0');
        group.setAttribute('aria-label', `${state}: ${value === null ? 'No matching figure' : layerSelect.value === 'issue' ? formatNumber(value) : `${value} records`}`);
        group.dataset.state = state;
        const shape = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        shape.setAttribute('d', path(feature)); shape.setAttribute('fill', colorScale(vals, value)); shape.classList.add('state-shape');
        if (selectedState && key(selectedState) === key(state)) shape.classList.add('is-selected');
        group.append(shape);
        const [x, y] = path.centroid(feature);
        if (Number.isFinite(x) && Number.isFinite(y)) {
            const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            label.setAttribute('x', x); label.setAttribute('y', y + 3); label.textContent = abbreviations[state] || state.slice(0, 2).toUpperCase();
            label.classList.add('map-label'); group.append(label);
        }
        group.addEventListener('click', () => selectState(state));
        group.addEventListener('keydown', (event) => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); selectState(state); } });
        const title = document.createElementNS('http://www.w3.org/2000/svg', 'title'); title.textContent = `${state}: ${value === null ? 'No matching figure' : layerSelect.value === 'issue' ? formatNumber(value) : `${value} records`}`; group.append(title);
        mapSvg.append(group);
    });
    document.querySelector('#map-loading').hidden = true;
    renderLegend(vals);
}

function renderHeroMap() {
    if (!geo || !heroMapSvg) return;
    const width = 500;
    const height = 500;
    const coordinates = [];
    const collect = (items) => {
        if (Array.isArray(items) && typeof items[0] === 'number') coordinates.push(items);
        else if (Array.isArray(items)) items.forEach(collect);
    };
    geo.features.forEach((feature) => collect(feature.geometry.coordinates));
    const bounds = coordinates.reduce((result, point) => [Math.min(result[0], point[0]), Math.min(result[1], point[1]), Math.max(result[2], point[0]), Math.max(result[3], point[1])], [Infinity, Infinity, -Infinity, -Infinity]);
    const scale = Math.min((width - 48) / (bounds[2] - bounds[0]), (height - 48) / (bounds[3] - bounds[1]));
    const offsetX = (width - (bounds[2] - bounds[0]) * scale) / 2;
    const offsetY = (height - (bounds[3] - bounds[1]) * scale) / 2;
    const projection = geoTransform({ point(longitude, latitude) { this.stream.point(offsetX + (longitude - bounds[0]) * scale, height - offsetY - (latitude - bounds[1]) * scale); } });
    const path = geoPath(projection);
    heroMapSvg.replaceChildren();
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    const clip = document.createElementNS('http://www.w3.org/2000/svg', 'clipPath');
    clip.setAttribute('id', 'hero-nigeria-clip');
    clip.setAttribute('clipPathUnits', 'userSpaceOnUse');
    const mapShapes = geo.features.map((feature) => path(feature));
    const silhouette = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    silhouette.setAttribute('d', mapShapes.join(''));
    silhouette.setAttribute('fill-rule', 'nonzero');
    clip.append(silhouette);
    defs.append(clip);
    heroMapSvg.append(defs);
    geo.features.forEach((feature, index) => {
        const shape = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        shape.setAttribute('id', `hero-state-${index}`);
        shape.setAttribute('d', mapShapes[index]);
        heroMapSvg.append(shape);
    });
    const portrait = document.createElementNS('http://www.w3.org/2000/svg', 'image');
    portrait.setAttribute('class', 'hero-map-photo');
    portrait.setAttribute('href', heroMapSvg.dataset.portraitSrc);
    portrait.setAttribute('x', '0');
    portrait.setAttribute('y', '0');
    portrait.setAttribute('width', '500');
    portrait.setAttribute('height', '500');
    portrait.setAttribute('preserveAspectRatio', 'xMidYMid meet');
    portrait.setAttribute('clip-path', 'url(#hero-nigeria-clip)');
    heroMapSvg.append(portrait);
}

const abbreviations = { 'Abia': 'AB', 'Adamawa': 'AD', 'Akwa Ibom': 'AK', 'Anambra': 'AN', 'Bauchi': 'BA', 'Bayelsa': 'BY', 'Benue': 'BE', 'Borno': 'BO', 'Cross River': 'CR', 'Delta': 'DE', 'Ebonyi': 'EB', 'Edo': 'ED', 'Ekiti': 'EK', 'Enugu': 'EN', 'Gombe': 'GO', 'Imo': 'IM', 'Jigawa': 'JI', 'Kaduna': 'KD', 'Kano': 'KN', 'Katsina': 'KT', 'Kebbi': 'KE', 'Kogi': 'KO', 'Kwara': 'KW', 'Lagos': 'LA', 'Nasarawa': 'NA', 'Niger': 'NI', 'Ogun': 'OG', 'Ondo': 'ON', 'Osun': 'OS', 'Oyo': 'OY', 'Plateau': 'PL', 'Rivers': 'RI', 'Sokoto': 'SO', 'Taraba': 'TA', 'Yobe': 'YO', 'Zamfara': 'ZA', 'FCT (Abuja)': 'FCT' };

function renderLegend(values) {
    const legend = document.querySelector('#map-legend');
    const layer = layerSelect.value;
    const labelLowHigh = layer === 'issue' ? ['Lower value', 'Higher value'] : layer === 'resource' ? ['Lower recorded allocation', 'Higher recorded allocation'] : ['Fewer records', 'More records'];
    const sorted = [...values].sort((a, b) => a - b);
    const median = sorted.length ? (sorted.length % 2 ? sorted[(sorted.length - 1) / 2] : (sorted[sorted.length / 2 - 1] + sorted[sorted.length / 2]) / 2) : null;
    const indicatorRows = issueRows().filter((row) => row.title === indicatorSelect.value);
    const periodRows = periodSelect.value ? indicatorRows.filter((row) => String(row.details?.period || '') === periodSelect.value) : indicatorRows;
    const unit = periodRows[0]?.details?.unit;
    legend.innerHTML = `<div class="legend-gradient"><span>${labelLowHigh[0]}</span><i></i><span>${labelLowHigh[1]}</span></div><span class="legend-missing"><i></i>No matching record</span><span class="legend-coverage">Coverage ${values.length}/${states.length} states</span>${layer === 'issue' && median !== null ? `<span class="legend-mid">Median ${escapeHtml(issueValueLabel(median, unit))}</span>` : ''}`;
}

function renderList() {
    const list = document.querySelector('#state-list');
    const values = states.map(([name, zone]) => ({ name, zone, value: valueForState(name) })).sort((a, b) => (b.value ?? -Infinity) - (a.value ?? -Infinity));
    list.innerHTML = values.map(({ name, zone, value }) => `<button type="button" class="state-list-row ${key(selectedState) === key(name) ? 'is-selected' : ''}" data-state="${escapeHtml(name)}"><span class="state-name"><b>${escapeHtml(name)}</b><small>${escapeHtml(zoneName(zone))}</small></span><span class="state-bar"><i style="width:${value === null ? 0 : Math.max(5, (value / (Math.max(...values.map((r) => r.value || 0)) || 1)) * 100)}%"></i></span><span class="state-value">${value === null ? 'No record' : layerSelect.value === 'issue' ? escapeHtml(issueValueLabel(value, issueRows()[0]?.details?.unit)) : layerSelect.value === 'resource' ? escapeHtml(money(value, currencySelect.value || 'NGN')) : `${value} records`}</span></button>`).join('');
    list.querySelectorAll('[data-state]').forEach((button) => button.addEventListener('click', () => selectState(button.dataset.state)));
}

function zoneName(code) { return ({ SW: 'South West', SE: 'South East', SS: 'South South', NC: 'North Central', NE: 'North East', NW: 'North West' })[code] || ''; }

function selectState(state) {
    selectedState = state; activeTab = 'overview';
    document.querySelector('#state-empty').hidden = true; document.querySelector('#state-content').hidden = false;
    document.querySelector('#state-name').textContent = state;
    const zone = states.find(([name]) => key(name) === key(state))?.[1];
    document.querySelector('#state-region').textContent = zoneName(zone) || 'Nigeria';
    panel.classList.add('has-state');
    renderMap(); renderList(); renderStatePanel();
}

function renderStatePanel() {
    if (!selectedState) return;
    const stateRecords = getRecordsForState(selectedState);
    const issueRecords = stateRecords.filter((r) => r.type === 'issue' && (!issueSelect.value || r.issue_area === issueSelect.value));
    const selectedIssue = issueRecordsForState(selectedState).find((r) => numeric(r) !== null);
    const interventionRecords = stateRecords.filter((r) => r.type === 'intervention');
    const resourceRecords = stateRecords.filter((r) => r.type === 'resource');
    const lawRecords = stateRecords.filter((r) => r.type === 'law');
        const lawStatuses = lawRecords.map((r) => r.details.status_label).filter((status) => status && !isSampleLabel(status));
        const sources = [...new Set(stateRecords.map((r) => r.details.source_name).filter((source) => source && !isSampleLabel(source)))];
    const tab = activeTab;
    document.querySelectorAll('.state-tabs [data-tab]').forEach((button) => {
        const active = button.dataset.tab === tab; button.classList.toggle('active', active); button.setAttribute('aria-selected', String(active));
    });
    const panelContent = document.querySelector('#state-tab-content');
    if (tab === 'overview') {
        const issueValue = selectedIssue ? issueValueLabel(numeric(selectedIssue), selectedIssue.details.unit) : 'No value available';
        const resourceCurrency = (layerSelect.value === 'resource' ? currencySelect.value : '') || resourceRecords[0]?.details.currency || 'NGN';
        const selectedResources = resourceRecords.filter((record) => (record.details.currency || 'Unspecified') === resourceCurrency
            && (!periodSelect.value || String(record.details.period || record.details.allocation_year || '') === periodSelect.value));
        const resources = selectedResources.filter((record) => !isReleaseCategory(record.details.resource_category)).reduce((sum, record) => sum + Number(record.details.amount || 0), 0);
        const linkedRecords = selectedIssue?.related_records || [];
        const relationshipMarkup = linkedRecords.length
            ? `<div class="record-relationships"><strong>Explicitly linked records</strong><ul>${linkedRecords.map((record) => `<li><span>${escapeHtml(record.relationship_type.replaceAll('_', ' '))}</span> · ${escapeHtml(record.title)} (${escapeHtml(record.type)})${record.source_reference ? ` <small>· ${escapeHtml(record.source_reference)}</small>` : ''}</li>`).join('')}</ul></div>`
            : '<div class="record-relationships"><strong>Record relationships</strong><p>No explicit record-level links are recorded for this issue indicator. Other layers below are shown by state and issue area only.</p></div>';
        panelContent.innerHTML = `<div class="overview-metric"><span>Selected value</span><strong>${escapeHtml(issueValue)}</strong><small>${escapeHtml(indicatorSelect.value || 'Selected indicator')}</small></div><div class="linked-grid"><div><span>Interventions in this state and issue area</span><strong>${interventionRecords.length}</strong><small>state/issue-area co-occurrence</small></div><div><span>Resources in this state and issue area</span><strong>${selectedResources.length ? escapeHtml(money(resources, resourceCurrency)) : '—'}</strong><small>${selectedResources.length} ${escapeHtml(resourceCurrency)} records · releases excluded from total · co-occurrence only</small></div></div><div class="law-summary"><span>Laws and policies</span><strong>${lawRecords.length || 'No'} <small>records in this state and issue area</small></strong><small>${lawStatuses.length ? lawStatuses.map(escapeHtml).join(' · ') : lawRecords.length ? 'Status information is not available' : 'No matching evidence'} · co-occurrence only</small></div>${relationshipMarkup}<div class="insight-box"><div class="insight-title"><i class="ti ti-bulb"></i><strong>Selected evidence</strong></div><p>${selectedIssue ? `${escapeHtml(selectedState)} has a recorded value for ${escapeHtml(indicatorSelect.value)} in ${escapeHtml(selectedIssue.details.period || 'an unspecified period')}. ${linkedRecords.length ? 'Explicit links are listed above with their relationship type and source reference.' : 'Other layer records share the selected state and issue area; no direct record link is defined.'}` : `No ${escapeHtml(indicatorSelect.value)} figure matches the selected period in ${escapeHtml(selectedState)}.`}</p></div><div class="source-summary"><strong>Reporting period</strong><p>${escapeHtml(selectedIssue?.details.period || 'Not recorded')}</p><strong>Sources</strong><p>${escapeHtml(sources.join('; ') || 'Source names are not recorded for this selection.')}</p>${selectedIssue?.details.source_url ? `<a href="${escapeHtml(selectedIssue.details.source_url)}" target="_blank" rel="noopener">View source <i class="ti ti-arrow-up-right"></i></a>` : ''}</div>`;
    } else {
        const rows = tab === 'evidence' ? issueRecords : tab === 'intervention' ? interventionRecords : tab === 'resource' ? resourceRecords : lawRecords;
        const titles = { evidence: 'Issue evidence', intervention: 'Interventions', resource: 'Resources', law: 'Laws and policies' };
        panelContent.innerHTML = `<h3 class="record-list-title">${titles[tab]} <span>${rows.length}</span></h3>${rows.length ? `<div class="record-list">${rows.map(recordCard).join('')}</div>` : `<div class="no-record"><i class="ti ti-file-off"></i><p>No evidence matches these filters.</p></div>`}`;
    }
}

function recordCard(record) {
    const details = record.details || {};
    const period = details.period || details.allocation_year || details.enactment_date || '';
    const source = [details.source_name, details.funder_mda, details.implementer].find((value) => value && !isSampleLabel(value)) || '';
    const extra = record.type === 'issue' ? `${details.value ?? 'Value not recorded'} ${details.unit || ''}` : record.type === 'resource' ? money(details.amount || 0, details.currency || 'NGN') : details.status_label || details.intervention_type || details.resource_category || '';
    const related = record.related_records || [];
    const relatedMarkup = related.length ? `<p class="record-link-note">Explicitly linked to ${related.map((item) => `${escapeHtml(item.title)} (${escapeHtml(item.relationship_type.replaceAll('_', ' '))})`).join('; ')}.</p>` : '';
    return `<article class="record-card"><div><h4>${escapeHtml(record.title || 'Untitled record')}</h4><p>${escapeHtml(record.issue_area || 'Issue not recorded')}${period ? ` · ${escapeHtml(period)}` : ''}</p></div><strong>${escapeHtml(extra)}</strong>${source ? `<small>${escapeHtml(source)}</small>` : ''}${relatedMarkup}<details><summary>Source details</summary><dl>${Object.entries(details).filter(([, value]) => value !== null && value !== '' && !isSampleLabel(value)).map(([field, value]) => `<div><dt>${escapeHtml(field.replaceAll('_', ' '))}</dt><dd>${field === 'source_url' ? `<a href="${escapeHtml(value)}" target="_blank" rel="noopener">Open source</a>` : escapeHtml(value)}</dd></div>`).join('')}</dl></details></article>`;
}

function render() {
    const layerLabels = { issue: 'Burden', intervention: 'Interventions', resource: 'Resources', law: 'Laws & Policy' };
    const layerTitles = { issue: `${indicatorSelect.value || 'Issue burden'} by state`, intervention: 'Interventions by state', resource: 'Resources by state', law: 'Laws and policies by state' };
    const issue = issueSelect.value || 'all issue areas';
    document.querySelector('#map-title').textContent = layerTitles[layerSelect.value];
    document.querySelector('#map-subtitle').textContent = layerSelect.value === 'issue' ? 'Reported values for the selected indicator and period. Grey areas have no matching figure.' : `Explore ${layerLabels[layerSelect.value].toLocaleLowerCase()} recorded for ${issue}. Grey areas have no matching evidence.`;
    const recordsForArea = (type) => records.filter((r) => r.type === type && (!issueSelect.value || r.issue_area === issueSelect.value));
    const relevant = recordsForArea(layerSelect.value);
    const resourceAllocations = recordsForArea('resource').filter((record) => {
        const amount = Number(record.details?.amount);
        return !isReleaseCategory(record.details?.resource_category) && record.details?.amount !== null && record.details?.amount !== '' && Number.isFinite(amount);
    });
    const layerHasData = (type) => type === 'issue'
        ? recordsForArea(type).some((record) => numeric(record) !== null)
        : type === 'resource' ? resourceAllocations.length > 0 : recordsForArea(type).length > 0;
    const layerOptions = [...layerSelect.options];
    layerOptions.forEach((option) => {
        if (!option.value) return;
        option.disabled = !layerHasData(option.value);
    });
    if (layerSelect.selectedOptions[0]?.disabled) {
        const firstAvailableLayer = layerOptions.find((option) => option.value && !option.disabled);
        if (firstAvailableLayer) layerSelect.value = firstAvailableLayer.value;
    }
    layerSelect.disabled = !layerOptions.some((option) => option.value && !option.disabled);
    issueSelect.disabled = allIssues.length === 0;
    layerHelp.textContent = layerSelect.disabled
        ? 'No evidence layers have records for this issue area.'
        : layerOptions.some((option) => option.value && option.disabled)
            ? 'Layers without records for this issue area are unavailable.'
            : '';
    layerHelp.hidden = !layerHelp.textContent;
    const periodLabel = document.querySelector('#period-filter-label');
    const previousChoice = periodChoices[layerSelect.value] ?? '';
    if (layerSelect.value === 'resource') {
        const resourcePeriods = [...new Set(resourceAllocations.map((record) => String(record.details?.period || record.details?.allocation_year || '')).filter(Boolean))].sort((a, b) => b.localeCompare(a));
        fillSelect(
            periodSelect,
            resourcePeriods.length ? [{ value: '', label: 'All available periods' }, ...resourcePeriods.map((value) => ({ value, label: value }))] : [{ value: '', label: 'No periods available' }],
            resourcePeriods.includes(previousChoice) ? previousChoice : '',
        );
        periodChoices.resource = periodSelect.value;
    } else if (layerSelect.value === 'issue') {
        const indicatorPeriods = issueRows()
            .filter((row) => numeric(row) !== null && row.title === indicatorSelect.value)
            .map((row) => row.details?.period)
            .filter(Boolean);
        const periods = [...new Set(indicatorPeriods)].sort((a, b) => String(b).localeCompare(String(a)));
        fillSelect(
            periodSelect,
            periods.length ? [{ value: '', label: 'Most recent available' }, ...periods.map((value) => ({ value, label: value }))] : [{ value: '', label: 'No periods available' }],
            periods.includes(previousChoice) ? previousChoice : '',
        );
        periodChoices.issue = periodSelect.value;
    } else {
        fillSelect(periodSelect, [{ value: '', label: 'Not used for this layer' }], '');
    }
    const currencies = [...new Set(resourceAllocations.map((record) => record.details?.currency || 'Unspecified'))].sort();
    fillSelect(
        currencySelect,
        currencies.length ? currencies.map((value) => ({ value, label: value === 'NGN' ? 'NGN · ₦' : value })) : [{ value: '', label: 'No currencies available' }],
        currencies.includes(currencyChoice) ? currencyChoice : currencies[0] || '',
    );
    currencyChoice = currencySelect.value;
    renderMap(); renderList();
    Object.keys(layerLabels).forEach((type) => { document.querySelector(`#count-${type}`).innerHTML = `${records.filter((r) => r.type === type).length.toLocaleString()} records <i class="ti ti-arrow-up-right"></i>`; });
    const indicatorsAvailable = [...indicatorSelect.options].some((option) => option.value !== '');
    const periodsAvailable = [...periodSelect.options].some((option) => option.value !== '');
    const currenciesAvailable = resourceAllocations.length > 0;
    const currencyApplies = layerSelect.value === 'resource';
    const periodApplies = ['issue', 'resource'].includes(layerSelect.value);

    indicatorSelect.disabled = layerSelect.value !== 'issue' || !indicatorsAvailable;
    indicatorHelp.textContent = layerSelect.value !== 'issue'
        ? 'Available when Evidence layer is Burden.'
        : indicatorsAvailable ? '' : 'No numeric indicators are available for this issue area.';
    indicatorHelp.hidden = !indicatorHelp.textContent;

    periodLabel.textContent = 'Period';
    periodSelect.setAttribute('aria-label', 'Reporting period');
    periodSelect.disabled = !periodApplies || !periodsAvailable;
    periodHelp.textContent = !periodApplies
        ? `Not used for ${layerLabels[layerSelect.value]}. Choose Burden or Resources to use this filter.`
        : periodsAvailable ? '' : `No reporting periods are available for this ${layerSelect.value === 'issue' ? 'indicator' : 'resource data'}.`;
    periodHelp.hidden = !periodHelp.textContent;
    currencySelect.disabled = !currencyApplies || !currenciesAvailable;
    currencyHelp.textContent = !currenciesAvailable
        ? 'No currencies are recorded for this issue area.'
        : currencyApplies ? '' : 'Available when Evidence layer is Resources.';
    currencyHelp.hidden = !currencyHelp.textContent;
    document.querySelector('#map-title').setAttribute('data-layer-label', layerLabels[layerSelect.value]);
    if (selectedState) renderStatePanel();
}

async function loadGeo() {
    try {
        const response = await fetch('/data/nigeria-states.geojson', { headers: { Accept: 'application/geo+json' } });
        if (!response.ok) throw new Error(`Boundary data returned ${response.status}`);
        geo = await response.json();
        document.querySelector('#map-loading').hidden = true;
        renderHeroMap();
        render();
    } catch (error) {
        document.querySelector('#map-loading').textContent = 'The map could not be loaded. Use the List view to explore available evidence.';
        document.querySelector('#map-loading').classList.add('map-error');
        console.error('Nigeria map boundary load failed', error);
    }
}

document.querySelectorAll('.state-tabs [data-tab]').forEach((button) => button.addEventListener('click', () => { activeTab = button.dataset.tab; renderStatePanel(); }));
document.querySelector('#close-state').addEventListener('click', () => { selectedState = null; panel.classList.remove('has-state'); document.querySelector('#state-content').hidden = true; document.querySelector('#state-empty').hidden = false; renderMap(); renderList(); });
document.querySelector('#map-view-button').addEventListener('click', () => setView('map'));
document.querySelector('#list-view-button').addEventListener('click', () => setView('list'));
function setView(view) { const list = view === 'list'; document.querySelector('#state-list').hidden = !list; mapCanvas.hidden = list; document.querySelector('#map-legend').hidden = list; document.querySelector('#map-view-button').classList.toggle('selected', !list); document.querySelector('#list-view-button').classList.toggle('selected', list); }
document.querySelectorAll('[data-layer-link]').forEach((link) => link.addEventListener('click', (event) => {
    event.preventDefault();
    layerSelect.value = link.dataset.layerLink;
    setView('map');
    render();
    window.history.replaceState(null, '', '#atlas-controls');
    document.querySelector('#atlas-controls').scrollIntoView({ behavior: 'smooth', block: 'start' });
}));
document.querySelectorAll('[data-layer-nav]').forEach((link) => link.addEventListener('click', (event) => { event.preventDefault(); layerSelect.value = link.dataset.layerNav; document.querySelectorAll('.primary-nav a').forEach((item) => item.classList.toggle('current', item === link)); setView('map'); render(); document.querySelector('.atlas-workspace').scrollIntoView({ behavior: 'smooth', block: 'start' }); }));
document.querySelector('#zoom-in').addEventListener('click', () => { zoom = Math.min(1.8, zoom + 0.15); mapSvg.style.transform = `scale(${zoom})`; });
document.querySelector('#zoom-out').addEventListener('click', () => { zoom = Math.max(0.8, zoom - 0.15); mapSvg.style.transform = `scale(${zoom})`; });
document.querySelector('#zoom-reset').addEventListener('click', () => { zoom = 1; mapSvg.style.transform = ''; });
window.addEventListener('resize', renderMap);

initFilters();
loadGeo();
