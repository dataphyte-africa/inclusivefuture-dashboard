<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Explore evidence on issues affecting women, children and young people across Nigeria.">
    <meta name="theme-color" content="#fbfaf6">
    <title>Ecosystem Intelligence Map · IFI</title>
    @vite(['resources/css/atlas.css', 'resources/js/atlas.js'])
</head>
<body>
<div class="atlas" id="atlas">
    <script>window.eimRecords = @js($records); window.eimStates = @js($states);</script>
    <header class="topbar">
        <a class="brand" href="#explore" aria-label="Inclusive Futures Initiative home">
            <img src="{{ asset('images/ifi-brandmark-header.png') }}" alt="Inclusive Futures Initiative">
        </a>
        <p class="atlas-name">Ecosystem Intelligence Map</p>
    </header>

    <main>
        <section class="hero-stage" id="explore" aria-label="See the ecosystem across Nigeria">
            <div class="hero-intro-copy">
                <p class="hero-kicker">Nigeria · Evidence atlas</p>
                <h1><span class="hero-title-line">See the ecosystem,</span><span class="hero-title-line">not a report about it.</span></h1>
                <p class="hero-description">Explore how issues affecting women, children and young people vary across Nigeria, and see how interventions, resources, laws and policies connect around them.</p>
                <a class="hero-cta" href="#atlas-controls"><i class="ti ti-map-2" aria-hidden="true"></i><span>Explore the map</span><i class="ti ti-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="hero-scene" aria-label="Nigeria's evidence ecosystem">
                <div class="hero-map-visual">
                    <svg id="hero-nigeria-map" class="hero-nigeria-outline" viewBox="0 0 500 500" preserveAspectRatio="xMidYMax meet" role="img" aria-label="Nigeria with a portrait of two women" data-portrait-src="{{ asset('images/hero-women-duo-cutout.png') }}"></svg>
                </div>
            </div>
            <img class="hero-landscape" src="{{ asset('images/hero-nigeria-cityscape.png') }}" alt="" aria-hidden="true" fetchpriority="high">
        </section>

        <section class="evidence-sections" id="evidence">
            <div class="section-title"><p class="kicker">Explore the evidence</p><h2>One issue. Four connected layers.</h2><p>Follow evidence from the issue itself through programmes, funding, and the laws and policies shaping the response.</p></div>
            <div class="layer-cards">
                <a href="#explore" data-layer-link="issue"><span class="layer-icon"><i class="ti ti-chart-bar"></i></span><span class="layer-number">01 · ISSUE</span><h3>What is the burden?</h3><p>Indicators and reported values, with sources and reporting periods.</p><b id="count-issue">— records <i class="ti ti-arrow-up-right"></i></b></a>
                <a id="interventions" href="#explore" data-layer-link="intervention"><span class="layer-icon"><i class="ti ti-heart-handshake"></i></span><span class="layer-number">02 · INTERVENTIONS</span><h3>What is being done?</h3><p>Programmes and services connected to an issue and the states where they are active.</p><b id="count-intervention">— records <i class="ti ti-arrow-up-right"></i></b></a>
                <a id="resources" href="#explore" data-layer-link="resource"><span class="layer-icon"><i class="ti ti-coins"></i></span><span class="layer-number">03 · RESOURCES</span><h3>Where are resources flowing?</h3><p>Allocations, grants, and releases, shown separately from expenditure.</p><b id="count-resource">— records <i class="ti ti-arrow-up-right"></i></b></a>
                <a id="laws" href="#explore" data-layer-link="law"><span class="layer-icon"><i class="ti ti-scale"></i></span><span class="layer-number">04 · LAWS &amp; POLICY</span><h3>What frameworks apply?</h3><p>Laws, policies, and strategies, with their jurisdiction and status.</p><b id="count-law">— records <i class="ti ti-arrow-up-right"></i></b></a>
            </div>
        </section>

        <section class="atlas-workspace" id="atlas-controls" aria-label="Explore and filter the evidence atlas">
            <aside class="filter-panel" aria-label="Map filters">
                <h2>Explore the map</h2>
                <label>Issue area<select id="issue-filter" aria-label="Issue area"></select></label>
                <label id="indicator-control">Indicator<select id="indicator-filter" aria-label="Indicator"></select></label>
                <label>Period<select id="period-filter" aria-label="Reporting period"></select></label>
                <label>Evidence layer<select id="layer-filter" aria-label="Evidence layer"><option value="issue">Burden</option><option value="intervention">Interventions</option><option value="resource">Resources</option><option value="law">Laws &amp; Policy</option></select></label>
                <div class="filter-note"><i class="ti ti-info-circle" aria-hidden="true"></i><div><strong>About this data</strong><p>Figures come from different sources and reporting years. Select a state to see its references.</p><a href="#about">About the evidence <i class="ti ti-arrow-right" aria-hidden="true"></i></a><span class="data-disclosure">Some values are illustrative. Check the listed source before relying on a figure.</span></div></div>
            </aside>

            <section class="map-panel" aria-label="Nigeria state evidence view">
                <div class="map-heading"><div class="map-heading-actions"><div class="view-toggle" role="group" aria-label="Map or list view"><button class="selected" id="map-view-button" type="button"><i class="ti ti-map-2" aria-hidden="true"></i> Map</button><button id="list-view-button" type="button"><i class="ti ti-list" aria-hidden="true"></i> List</button></div></div></div>
                <div class="map-caption"><h2 id="map-title">Girls' Education: Out-of-school girls</h2><p id="map-subtitle">Higher values indicate greater burden. States without a matching figure are shown in grey.</p></div>
                <div class="map-canvas" id="map-canvas"><svg id="nigeria-map" role="img" aria-label="Nigeria state-level evidence map"></svg><div class="map-loading" id="map-loading">Loading state boundaries…</div><div class="map-zoom"><button id="zoom-in" type="button" aria-label="Zoom in"><i class="ti ti-plus"></i></button><button id="zoom-out" type="button" aria-label="Zoom out"><i class="ti ti-minus"></i></button><button id="zoom-reset" type="button" aria-label="Reset map"><i class="ti ti-focus-2"></i></button></div></div>
                <div class="map-legend" id="map-legend"></div>
                <div class="state-list" id="state-list" hidden></div>
            </section>

            <aside class="state-panel" id="state-panel" aria-label="Selected state details" aria-live="polite">
                <div class="state-panel-empty" id="state-empty"><i class="ti ti-map-pin" aria-hidden="true"></i><h2>Select a state</h2><p>Choose a state on the map or in the list to explore its evidence and sources.</p></div>
                <div id="state-content" hidden>
                    <div class="state-title"><div><p class="state-region" id="state-region"></p><h2 id="state-name"></h2></div><button type="button" id="close-state" aria-label="Close state details"><i class="ti ti-x"></i></button></div>
                    <nav class="state-tabs" role="tablist" aria-label="State evidence details"><button class="active" type="button" role="tab" aria-selected="true" data-tab="overview">Overview</button><button type="button" role="tab" aria-selected="false" data-tab="evidence">Evidence</button><button type="button" role="tab" aria-selected="false" data-tab="intervention">Interventions</button><button type="button" role="tab" aria-selected="false" data-tab="law">Laws &amp; Policy</button></nav>
                    <div class="state-tab-content" id="state-tab-content"></div>
                </div>
            </aside>
        </section>

        <aside class="gap-note atlas-gap-note" aria-label="About missing evidence"><i class="ti ti-shield-exclamation"></i><div><strong>Missing evidence does not mean nothing is happening.</strong><p>No matching information is available for this selection. That does not mean related activity is absent.</p></div></aside>

    </main>

    <footer class="atlas-footer" id="about"><div class="footer-brand"><strong>Inclusive Future Initiatives IFI</strong><span>Evidence. People. Possibility.</span></div><div><h2>About the platform</h2><p>The Ecosystem Intelligence Map brings together evidence on issues affecting women, children and young people across Nigeria, alongside connected interventions, resources, and laws and policies. Explore state-level data and sources to understand the wider response.</p></div><div class="footer-bottom"><span>IFI - Ecosystem Intelligence Map</span><span>© 2026 Inclusive Future Initiatives. All rights reserved.</span></div></footer>
</div>
</body>
</html>
