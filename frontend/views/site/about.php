<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">

<p>
Airport data is stored in MySQL using spatial <code>POINT</code> columns with
<code>SPATIAL INDEX</code> support, enabling efficient geographic queries at scale.
</p>


<hr>


<h2>Geospatial Strategy</h2>


<ul>
<li>Coordinates stored as <code>POINT(longitude, latitude)</code></li>
<li>Distance calculations via <code>ST_Distance_Sphere</code></li>
<li>Bounding-radius queries for nearby airport searches</li>
<li>Database-level computation to minimize application overhead</li>
</ul>


<hr>


<h2>Routing Logic</h2>


<p>
Flight routing is implemented as a graph traversal problem where airports represent nodes
and edges exist between airports that are within a configurable maximum distance.
</p>


<p>
The routing algorithm:
</p>


<ul>
<li>Uses breadth-first search (BFS) to minimize the number of stops</li>
<li>Enforces a configurable maximum leg distance (default: 500 miles)</li>
<li>Allows per-request overrides without requiring code changes</li>
</ul>


<p>
This logic is encapsulated in a reusable Yii component, keeping controllers thin and
business rules centralized.
</p>


<hr>


<h2>Console Tooling</h2>


<p>
Airport data is imported via a Yii console command that accepts a CSV file path. This
approach ensures portability across environments and avoids database-level file access
constraints common in managed hosting environments.
</p>


<hr>


<h2>Scalability & Performance</h2>


<ul>
<li>Read-heavy workload optimized via indexing and stateless design</li>
<li>Horizontal scaling through load-balanced API nodes</li>
<li>Optional caching layers (Redis, CDN) for future growth</li>
</ul>


<p>
The architecture is intentionally conservative, prioritizing clarity, maintainability,
and predictable performance over unnecessary complexity.
</p>


<hr>


<h2>Intended Audience</h2>


<p>
These views are designed to help developers, reviewers, and stakeholders quickly
understand how the system works, why specific decisions were made, and how the
application can evolve over time.
</p>


</div>

</div>
