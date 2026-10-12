import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

const cleanUnit = (unit) => String(unit || '').replace(/\s*,?\s*(?:synthetic|illustrative)\s*$/i, '').replace(/\s*%/g, '%').trim();
const formatIssueValue = (value, unit) => {
    const normalizedUnit = cleanUnit(unit);
    const formattedValue = Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 });

    return `${formattedValue}${normalizedUnit.startsWith('%') ? '%' : normalizedUnit ? ` ${normalizedUnit}` : ''}`;
};

Alpine.data('eimDashboard', (records, states) => ({
    records,
    states,
    initialIssue: records.some((record) => record.issue_area === "Girls' Education") ? "Girls' Education" : '',
    activeIssue: records.some((record) => record.issue_area === "Girls' Education") ? "Girls' Education" : '',
    activeType: 'law',
    selectedState: '',
    compareA: 'Kaduna',
    compareB: 'Kano',
    stateZones: [
        { key: 'NW', name: 'North West' },
        { key: 'NE', name: 'North East' },
        { key: 'NC', name: 'North Central' },
        { key: 'SW', name: 'South West' },
        { key: 'SE', name: 'South East' },
        { key: 'SS', name: 'South South' },
    ],
    lawMapStateOrder: ['Sokoto', 'Kebbi', 'Zamfara', 'Katsina', 'Kano', 'Jigawa', 'Kaduna', 'Yobe', 'Borno', 'Bauchi', 'Gombe', 'Adamawa', 'Taraba', 'Niger', 'Kwara', 'Kogi', 'Benue', 'Plateau', 'Nasarawa', 'FCT (Abuja)', 'Oyo', 'Ogun', 'Lagos', 'Osun', 'Ondo', 'Ekiti', 'Enugu', 'Ebonyi', 'Anambra', 'Imo', 'Abia', 'Edo', 'Delta', 'Bayelsa', 'Rivers', 'Akwa Ibom', 'Cross River'],
    lawMapAbbreviations: {
        Sokoto: 'SK', Kebbi: 'KB', Zamfara: 'ZM', Katsina: 'KT', Kano: 'KN', Jigawa: 'JG', Kaduna: 'KD',
        Yobe: 'YB', Borno: 'BO', Bauchi: 'BA', Gombe: 'GM', Adamawa: 'AD', Taraba: 'TR',
        Niger: 'NG', Kwara: 'KW', Kogi: 'KG', Benue: 'BN', Plateau: 'PL', Nasarawa: 'NS', 'FCT (Abuja)': 'FC',
        Oyo: 'OY', Ogun: 'OG', Lagos: 'LA', Osun: 'OS', Ondo: 'OD', Ekiti: 'EK',
        Enugu: 'EN', Ebonyi: 'EB', Anambra: 'AN', Imo: 'IM', Abia: 'AB',
        Edo: 'ED', Delta: 'DL', Bayelsa: 'BY', Rivers: 'RV', 'Akwa Ibom': 'AK', 'Cross River': 'CR',
    },
    get issueAreas() { return [...new Set(this.records.map((record) => record.issue_area))].sort(); },
    get stateChartTitle() {
        const issue = this.activeIssue || 'All issues';
        const layer = ({ issue: 'Issues', intervention: 'Interventions', resource: 'Resources', law: 'Law & policy status' })[this.activeType] || 'Records';
        return `${issue} — ${layer}`;
    },
    get selectedStateRecords() {
        return this.filteredRecords.filter((record) => record.state?.toLocaleLowerCase() === this.selectedState?.toLocaleLowerCase());
    },
    get selectedStateLayerRecords() {
        return this.records.filter((record) => record.state?.toLocaleLowerCase() === this.selectedState?.toLocaleLowerCase()
            && (!this.activeIssue || record.issue_area === this.activeIssue));
    },
    get selectedStateLayerCounts() {
        return ['issue', 'intervention', 'resource', 'law'].map((type) => ({
            type,
            label: ({ issue: 'burden records', intervention: 'interventions', resource: 'resources', law: 'law records' })[type],
            count: this.selectedStateLayerRecords.filter((record) => record.type === type).length,
        }));
    },
    get selectedStateBurdenRows() {
        return this.selectedStateLayerRecords.filter((record) => record.type === 'issue').map((record) => {
            const value = record.details.value;
            const numericValue = Number(value);
            const displayValue = value === null || value === undefined || value === ''
                ? ''
                : (Number.isFinite(numericValue) ? formatIssueValue(numericValue, record.details.unit) : value);

            return {
                title: record.title || 'Indicator',
                value: displayValue || 'Value not recorded',
            };
        });
    },
    get selectedStateInterventionRows() {
        return this.selectedStateLayerRecords.filter((record) => record.type === 'intervention');
    },
    get selectedStateInterventionTypes() {
        const groups = new Map();
        this.selectedStateInterventionRows.forEach((record) => {
            const label = record.details.intervention_type || 'Type not recorded';
            groups.set(label, (groups.get(label) || 0) + 1);
        });
        return [...groups].map(([label, count]) => ({ label, count }));
    },
    get selectedStateResourceRows() {
        const groups = new Map();
        this.selectedStateLayerRecords.filter((record) => record.type === 'resource').forEach((record) => {
            const category = record.details.resource_category || 'Category not recorded';
            const currency = record.details.currency || 'Unspecified currency';
            const key = `${category}|${currency}`;
            const group = groups.get(key) || { category, currency, amount: 0, records: [] };
            group.amount += Number(record.details.amount || 0);
            group.records.push(record);
            groups.set(key, group);
        });
        return [...groups.values()];
    },
    get selectedStateLawRows() {
        return this.selectedStateLayerRecords.filter((record) => record.type === 'law');
    },
    get stateLawMapRows() {
        const relevantLaws = this.records.filter((record) => {
            if (record.type !== 'law' || (this.activeIssue && record.issue_area !== this.activeIssue)) return false;
            const family = `${record.details.law_policy_family || ''} ${record.title || ''}`;
            return /child\s*rights|\bvapp\b|violence against persons/i.test(family);
        });
        const normalizeState = (name) => (name || '').toLocaleLowerCase().replace(/[^a-z0-9]/g, '');
        const stateOrder = new Map(this.lawMapStateOrder.map((name, index) => [name, index]));

        return this.states.map(([name, zone]) => {
            const stateLaws = relevantLaws.filter((record) => normalizeState(record.state) === normalizeState(name));
            const legislation = stateLaws.filter((record) => (record.details.instrument_category || '').toLocaleLowerCase().includes('legislation'));
            const stateLegislation = legislation.filter((record) => (record.details.jurisdiction || '').toLocaleLowerCase().includes('state'));
            const headline = (stateLegislation.length ? stateLegislation : legislation).at(-1);
            const status = headline?.details.status_label || '';
            const normalizedStatus = status.toLocaleLowerCase();
            const statusClass = !headline ? 'missing'
                : normalizedStatus.includes('domesticated') && !normalizedStatus.includes('pending') ? 'domesticated'
                    : normalizedStatus.includes('pending') || normalizedStatus.includes('draft') ? 'pending'
                        : normalizedStatus.includes('not adopted') || normalizedStatus.includes('expired') || normalizedStatus.includes('superseded') ? 'not-adopted'
                            : 'recorded';

            return {
                name,
                zone,
                abbreviation: this.lawMapAbbreviations[name] || name.slice(0, 2).toLocaleUpperCase(),
                status: headline ? (status || 'Status not recorded').split(' — ')[0] : 'Not recorded',
                statusClass,
                records: stateLaws,
            };
        }).sort((first, second) => (stateOrder.get(first.name) ?? 999) - (stateOrder.get(second.name) ?? 999));
    },
    formatAmount(amount, currency) {
        const formatted = Number(amount || 0).toLocaleString(undefined, { maximumFractionDigits: 2 });
        return `${currency === 'NGN' ? '₦' : currency} ${formatted}`;
    },
    get selectedStateRow() {
        return this.stateRows.find((row) => row.name === this.selectedState);
    },
    get stateRows() {
        const filtered = this.records.filter((record) => (!this.activeIssue || record.issue_area === this.activeIssue)
            && record.type === this.activeType);
        const counts = this.states.map(([name]) => filtered.filter((record) => record.state?.toLocaleLowerCase() === name.toLocaleLowerCase()).length);
        const maxCount = Math.max(1, ...counts);

        return this.states.map(([name, zone]) => {
            const rows = filtered.filter((record) => record.state?.toLocaleLowerCase() === name.toLocaleLowerCase());
            const count = rows.length;
            let summary = '';
            let statusClass = 'empty';

            if (this.activeType === 'law' && count) {
                const status = rows.find((record) => record.details.status_label)?.details.status_label || 'Status not recorded';
                summary = status.split(' — ')[0];
                statusClass = this.statusClass(status);
            } else if (this.activeType === 'resource' && count) {
                const amounts = rows.reduce((totals, record) => {
                    const amount = Number(record.details.amount || 0);
                    const currency = record.details.currency || 'Unspecified currency';
                    totals[currency] = (totals[currency] || 0) + amount;
                    return totals;
                }, {});
                summary = Object.entries(amounts).map(([currency, amount]) => `${currency} ${amount.toLocaleString()}`).join(' · ');
                statusClass = 'recorded';
            } else if (this.activeType === 'issue' && count) {
                const values = rows.filter((record) => record.details.value !== null && record.details.value !== undefined);
                summary = values.length === 1 ? formatIssueValue(values[0].details.value, values[0].details.unit) : `${count} indicator${count === 1 ? '' : 's'} recorded`;
                statusClass = 'recorded';
            } else if (count) {
                summary = count === 1 ? rows[0].title : `${rows[0].title} +${count - 1} more`;
                statusClass = 'recorded';
            }

            return {
                name,
                zone,
                zoneName: this.stateZones.find((item) => item.key === zone)?.name || zone,
                count,
                width: count ? (count / maxCount) * 100 : 0,
                summary,
                statusClass,
                records: rows,
            };
        }).sort((first, second) => second.count - first.count || first.name.localeCompare(second.name));
    },
    get filteredRecords() {
        return this.records.filter((record) => {
            return (!this.activeIssue || record.issue_area === this.activeIssue)
                && (this.activeType === 'all' || record.type === this.activeType)
                && (!this.selectedState || record.state === this.selectedState);
        });
    },
    stateHasData(state) {
        return this.records.some((record) => record.state?.toLocaleLowerCase() === state.toLocaleLowerCase() && (this.activeType === 'all' || record.type === this.activeType));
    },
    stateSummary(state) {
        const count = this.records.filter((record) => record.state?.toLocaleLowerCase() === state.toLocaleLowerCase() && (this.activeType === 'all' || record.type === this.activeType)).length;
        return `${state}: ${count} published records`;
    },
    statusClass(status) {
        const normalized = status.toLocaleLowerCase();
        if (normalized.includes('domesticated') && !normalized.includes('pending')) return 'adopted';
        if (normalized.includes('pending') || normalized.includes('draft')) return 'pending';
        if (normalized.includes('not adopted') || normalized.includes('expired') || normalized.includes('superseded')) return 'not-adopted';
        return 'recorded';
    },
    comparisonValue(state, type) {
        const rows = this.records.filter((record) => record.state === state && record.type === type);
        if (!rows.length) return 'No published record';
        if (type === 'issue') return rows.map((row) => row.details.value === null || row.details.value === undefined ? row.title : formatIssueValue(row.details.value, row.details.unit)).join(', ');
        if (type === 'resource') {
            const totals = {};
            rows.forEach((row) => { const currency = row.details.currency || ''; totals[currency] = (totals[currency] || 0) + Number(row.details.amount || 0); });
            return Object.entries(totals).map(([currency, amount]) => `${rows.length} · ${currency} ${amount.toLocaleString()}`).join(' / ');
        }
        if (type === 'law') return `${rows.length} record${rows.length === 1 ? '' : 's'} · ${rows.map((row) => row.details.status_label).filter(Boolean).join(', ')}`;
        return `${rows.length} record${rows.length === 1 ? '' : 's'}`;
    },
    typeLabel(type) { return ({ issue: 'Issue', intervention: 'Intervention', resource: 'Resource', law: 'Law / policy' })[type] || type; },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    AOS.init({ duration: 650, once: true, offset: 24 });

    const records = window.eimRecords || [];
    const issueRows = records.filter((record) => record.type === 'issue' && record.details.value !== null && record.details.value !== undefined && String(record.details.value).trim() !== '' && Number.isFinite(Number(record.details.value)));
    const issueCanvas = document.getElementById('issue-chart');
    if (issueCanvas) {
        new Chart(issueCanvas, {
            type: 'bar',
            data: {
                labels: issueRows.map((record) => record.state),
                datasets: [{ label: 'Published value', data: issueRows.map((record) => Number(record.details.value)), backgroundColor: '#143c35', borderRadius: 7, maxBarThickness: 62 }],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (context) => formatIssueValue(context.raw, issueRows[context.dataIndex]?.details.unit) } } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#e9e5dc' } } } },
        });
    }

    const moneyLegend = document.getElementById('money-legend');
    const moneyRows = document.getElementById('money-rows');
    if (moneyLegend && moneyRows) {
        const colors = ['#12305B', '#2F5F91', '#8E332E', '#9C6B0A', '#405B7E', '#0C756B', '#5F6D7A'];
        const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char]);
        const formatAmount = (amount, currency) => `${currency === 'NGN' ? '₦' : currency} ${Number(amount || 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
        const renderMoney = () => {
            const issue = document.getElementById('dashboard-issue-filter')?.value || '';
            const currency = document.getElementById('dashboard-currency-filter')?.value || '';
            const rows = records.filter((record) => record.type === 'resource' && (!issue || record.issue_area === issue) && (!currency || record.details.currency === currency));
            const categoryNames = [...new Set(rows.map((record) => record.details.resource_category || 'Category not recorded'))].sort();
            const categories = categoryNames.filter((category) => !/expenditure|release|disbursement/i.test(category)).map((title, index) => ({ title, color: colors[index % colors.length] }));
            const states = [...new Set(rows.map((record) => record.state).filter(Boolean))].map((name) => {
                const stateRecords = rows.filter((record) => record.state === name);
                const amounts = Object.fromEntries(categoryNames.map((category) => [category, stateRecords.filter((record) => (record.details.resource_category || 'Category not recorded') === category).reduce((sum, record) => sum + Number(record.details.amount || 0), 0)]));
                const releaseCategories = categoryNames.filter((category) => /expenditure|release|disbursement/i.test(category));
                const release = releaseCategories.reduce((sum, category) => sum + amounts[category], 0);
                const total = categories.reduce((sum, category) => sum + amounts[category.title], 0);
                const allocation = Object.entries(amounts).filter(([category]) => /allocation/i.test(category)).reduce((sum, [, amount]) => sum + amount, 0);
                return { name, amounts, release, total, allocation };
            }).sort((first, second) => second.total - first.total || first.name.localeCompare(second.name));
            const total = states.reduce((sum, state) => sum + state.total, 0);
            const max = Math.max(...states.map((state) => state.total), 1);
            const totalByCurrency = Object.entries(rows.reduce((totals, record) => {
                const key = record.details.currency || 'Unspecified';
                totals[key] = (totals[key] || 0) + Number(record.details.amount || 0);
                return totals;
            }, {})).map(([code, amount]) => formatAmount(amount, code)).join(' + ');

            moneyLegend.innerHTML = categories.map((category) => `<span><i class="sw" style="background:${category.color}"></i>${escapeHtml(category.title)}</span>`).join('')
                + (categoryNames.some((category) => /expenditure|release|disbursement/i.test(category)) ? '<span><i class="sw money-release-key"></i>Releases and expenditure shown separately</span>' : '')
                + `<span class="money-total-note">${formatAmount(total, currency || 'NGN')} in recorded resources (excluding releases and expenditure) across ${states.length} ${states.length === 1 ? 'state' : 'states'} · ${rows.length} records</span>`;
            document.getElementById('money-summary-copy').textContent = `${rows.length} resource records${issue ? ` for ${issue}` : ''}${currency ? ` in ${currency}` : ''}. Releases and expenditure are shown separately and are not added to allocations.`;
            moneyRows.innerHTML = states.length ? states.map((state) => {
                const segments = categories.map((category) => {
                    const amount = state.amounts[category.title];
                    if (!amount) return '';
                    return `<span class="money-seg" style="flex-basis:${amount / max * 100}%;background:${category.color}" title="${escapeHtml(category.title)}: ${formatAmount(amount, currency)}"></span>`;
                }).join('');
                const releaseNote = state.release ? `<p class="money-release-note">↳ ${formatAmount(state.release, currency)} recorded as release or expenditure${state.allocation ? ` (${Math.round(state.release / state.allocation * 100)}% of recorded allocation)` : ''}</p>` : '';
                return `<article class="money-item" role="listitem" aria-label="${escapeHtml(state.name)}: ${formatAmount(state.total, currency)} recorded resources"><div class="money-row"><span class="mrlabel" title="${escapeHtml(state.name)}">${escapeHtml(state.name)}</span><div class="money-track" role="img" aria-label="${escapeHtml(state.name)} funding categories: ${categories.filter((category) => state.amounts[category.title]).map((category) => `${escapeHtml(category.title)} ${formatAmount(state.amounts[category.title], currency)}`).join(', ')}">${segments}</div><span class="money-total">${formatAmount(state.total, currency)}</span></div>${releaseNote}</article>`;
            }).join('') : '<p class="money-empty">No published resource records match this issue and currency yet.</p>';
        };
        renderMoney();
        window.addEventListener('eim:issue-changed', renderMoney);
        window.addEventListener('eim:resources-changed', renderMoney);
        document.getElementById('dashboard-currency-filter')?.addEventListener('change', renderMoney);
    }
});

window.addEventListener('load', () => AOS.refresh());
