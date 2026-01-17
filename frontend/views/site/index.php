<?php
use yii\helpers\Html;

$this->title = 'Airport Geospatial API';
?>

<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1><?= Yii::$app->name ?? "Airport Geospatial API"; ?></h1>
        <p class="lead">A high-performance, read-heavy geospatial API built with Yii2 and MySQL Spatial</p>
    </div>

    <div class="body-content">

        <h2>Project Overview</h2>

        <p>
            This application exposes a public, read-only API that provides geospatial airport data
            to client applications (such as iOS apps) for mapping, distance comparison, and route
            planning.
        </p>

        <p>
            The system is designed to efficiently handle hundreds of requests per second while
            maintaining clear architecture, strong data integrity, and predictable performance.
        </p>

        <hr>

        <h2>Key Features</h2>

        <ul>
            <li>Geospatial airport search using latitude, longitude, and radius</li>
            <li>Distance calculations between airports using spherical geometry</li>
            <li>Country-to-country airport proximity comparison</li>
            <li>Shortest-hop flight routing with configurable maximum leg distance</li>
            <li>Read-heavy, horizontally scalable API architecture</li>
        </ul>

        <hr>

        <h2>Technology Stack</h2>

        <ul>
            <li><strong>Language:</strong> PHP 8.x</li>
            <li><strong>Framework:</strong> Yii2 (Advanced Application Template)</li>
            <li><strong>Database:</strong> MySQL 8 with Spatial Indexes</li>
            <li><strong>Hosting:</strong> AWS (EC2, RDS, ALB)</li>
        </ul>

        <p>
            All geospatial calculations are performed directly within the database using native
            MySQL spatial functions, ensuring accuracy and performance.
        </p>

        <hr>

        <h2>API Design Philosophy</h2>

        <p>
            The API is intentionally <strong>stateless</strong>, <strong>read-optimized</strong>,
            and <strong>configuration-driven</strong>. Business rules such as maximum flight leg
            distance are configurable via application settings rather than hard-coded logic.
        </p>

        <p>
            Public-facing endpoints use industry-standard identifiers (such as IATA airport codes),
            while internal processing relies on numeric primary keys for efficiency.
        </p>

        <p>
            For detailed endpoint documentation and architectural decisions, see the
            <?= Html::a('About page', ['site/about']) ?>.
        </p>
<hr />
<h1>Problem #1 – Full Stack Architecture & Deployment Strategy</h1>

<p>
    This document describes the full-stack architecture chosen to build a
    high-performance, read-heavy geospatial airport API. The design emphasizes
    scalability, clarity, operational simplicity, and real-world deployability.
</p>

<h2>Overview</h2>

<p>
    The goal of this system is to expose a public, read-heavy API capable of serving
    geospatial airport data at an average load of ~500 requests per second, with
    potential bursts of ±300 RPS. The data set is relatively static, favoring
    optimizations around read performance, caching, and horizontal scalability.
</p>

<p>The stack has been intentionally selected to be:</p>

<ul>
    <li>Easy for new developers to understand</li>
    <li>Production-proven</li>
    <li>Cost-efficient at moderate scale</li>
    <li>Horizontally scalable without major architectural changes</li>
</ul>

<h2>Hosting</h2>

<p>
    Amazon Web Services (AWS) is selected due to its maturity, reliability,
    and operational flexibility.
</p>

<h3>Core Infrastructure</h3>

<ul>
    <li>
        <strong>EC2 (Application Servers)</strong>
        <ul>
            <li>Auto Scaling Group</li>
            <li>Stateless PHP API nodes</li>
        </ul>
    </li>
    <li>
        <strong>Application Load Balancer (ALB)</strong>
        <ul>
            <li>Distributes traffic evenly</li>
            <li>Health checks and SSL termination</li>
        </ul>
    </li>
    <li>
        <strong>RDS – MySQL 8.0</strong>
        <ul>
            <li>Managed database</li>
            <li>Automated backups</li>
            <li>Multi-AZ capable</li>
        </ul>
    </li>
    <li>
        <strong>S3</strong>
        <ul>
            <li>CSV ingestion storage</li>
            <li>Backups and artifacts</li>
        </ul>
    </li>
    <li>
        <strong>Route53</strong>
        <ul>
            <li>DNS and traffic routing</li>
        </ul>
    </li>
</ul>

<p>
    This architecture supports horizontal scaling and fault tolerance without
    introducing unnecessary complexity.
</p>

<h2>Language</h2>

<p><strong>PHP 8.2</strong></p>

<p>Reasons for selection:</p>

<ul>
    <li>Strong ecosystem for API development</li>
    <li>Excellent MySQL and spatial query support</li>
    <li>High developer availability</li>
    <li>Mature tooling and debugging support</li>
</ul>

<p>
    PHP performs very well for IO-bound, read-heavy APIs when paired with proper
    indexing and caching strategies.
</p>

<h2>Framework</h2>

<p><strong>Yii2 – Advanced Application Template</strong></p>

<p>Reasons for selection:</p>

<ul>
    <li>Clear separation of concerns (frontend / backend / console)</li>
    <li>First-class support for REST APIs</li>
    <li>Excellent migration and database tooling</li>
    <li>Strong ActiveRecord and Query Builder</li>
    <li>Predictable performance characteristics</li>
</ul>

<p>
    Yii2 provides structure without unnecessary abstraction and is easy for other
    developers to onboard into quickly.
</p>

<h2>Storage</h2>

<p><strong>MySQL 8.0 with Spatial Extensions</strong></p>

<h3>Why MySQL?</h3>

<ul>
    <li>Native support for <code>POINT</code> data types</li>
    <li>Built-in spatial indexes</li>
    <li>Functions such as <code>ST_Distance_Sphere</code></li>
    <li>Lower operational complexity compared to PostGIS</li>
</ul>

<p>
    Given the relatively small size of global airport datasets, MySQL spatial
    indexing is more than sufficient and avoids the overhead of a more complex GIS stack.
</p>

<h3>Schema Strategy</h3>

<ul>
    <li>Airport coordinates stored as <code>POINT(longitude, latitude)</code></li>
    <li><code>SPATIAL INDEX</code> on location column</li>
    <li>Secondary indexes on country and identifiers</li>
</ul>

<h2>Performance Strategy</h2>

<p>The API is optimized for read performance using the following techniques:</p>

<h3>Spatial Indexing</h3>
<ul>
    <li>Bounding-box prefiltering</li>
    <li>Accurate distance calculations only after index narrowing</li>
</ul>

<h3>Stateless API Nodes</h3>
<ul>
    <li>Enables horizontal scaling</li>
    <li>No session affinity required</li>
</ul>

<h3>Efficient Queries</h3>
<ul>
    <li>Database-side distance calculations</li>
    <li>Minimal data transfer</li>
</ul>

<h3>Optional Enhancements</h3>
<ul>
    <li>Redis for hot query caching</li>
    <li>HTTP cache headers (ETag / Cache-Control)</li>
    <li>CloudFront in front of the API for global caching</li>
</ul>

<p>
    This design comfortably supports sustained traffic while remaining cost-efficient.
</p>

<h2>Scalability</h2>

<h3>Vertical Scaling</h3>
<ul>
    <li>Increase EC2 instance size</li>
    <li>Upgrade RDS instance class</li>
</ul>

<h3>Horizontal Scaling</h3>
<ul>
    <li>Add API nodes via Auto Scaling Group</li>
    <li>Add MySQL read replicas</li>
</ul>

<h3>Future Growth</h3>
<ul>
    <li>Migrate to Aurora MySQL if needed</li>
    <li>Introduce asynchronous processing for analytics</li>
</ul>

<p>
    The system can scale incrementally without requiring architectural rewrites.
</p>

<h2>Estimated Monthly Costs (USD)</h2>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Component</th>
            <th>Estimated Cost</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>EC2 (2× t3.small)</td>
            <td>~$40</td>
        </tr>
        <tr>
            <td>RDS MySQL (db.t3.medium)</td>
            <td>~$70</td>
        </tr>
        <tr>
            <td>Load Balancer</td>
            <td>~$18</td>
        </tr>
        <tr>
            <td>Data Transfer & Storage</td>
            <td>~$20</td>
        </tr>
        <tr>
            <th>Total</th>
            <th>~$150 / month</th>
        </tr>
    </tbody>
</table>

<p>
    Costs can be reduced further for lower traffic or increased linearly as demand grows.
</p>

<h2>Miscellaneous Considerations</h2>

<ul>
    <li><strong>Deployment:</strong> CI/CD via GitHub Actions or similar</li>
    <li><strong>Observability:</strong> CloudWatch logs and metrics</li>
    <li>
        <strong>Security:</strong>
        <ul>
            <li>IAM roles for infrastructure access</li>
            <li>Private RDS subnet</li>
            <li>HTTPS-only API</li>
        </ul>
    </li>
</ul>

<h2>Summary</h2>

<p>
    This stack balances performance, clarity, and operational simplicity. It is
    intentionally conservative, production-tested, and easy for additional engineers
    to understand and extend. The architecture supports both current requirements and
    future growth without premature complexity.
</p>



    </div>
</div>
