# Problem #1 – Full Stack Architecture & Deployment Strategy

This document describes the full-stack architecture chosen to build a high-performance, read-heavy geospatial airport API. The design emphasizes scalability, clarity, operational simplicity, and real-world deployability.

---

## Overview

The goal of this system is to expose a **public, read-heavy API** capable of serving geospatial airport data at an average load of **~500 requests per second**, with potential bursts of **±300 RPS**. The data set is relatively static, favoring optimizations around read performance, caching, and horizontal scalability.

The stack has been intentionally selected to be:

* Easy for new developers to understand
* Production-proven
* Cost-efficient at moderate scale
* Horizontally scalable without major architectural changes

---

## Hosting

**Amazon Web Services (AWS)** is selected due to its maturity, reliability, and operational flexibility.

### Core Infrastructure

* **EC2 (Application Servers)**

  * Auto Scaling Group
  * Stateless PHP API nodes

* **Application Load Balancer (ALB)**

  * Distributes traffic evenly
  * Health checks and SSL termination

* **RDS – MySQL 8.0**

  * Managed database
  * Automated backups
  * Multi-AZ capable

* **S3**

  * CSV ingestion storage
  * Backups and artifacts

* **Route53**

  * DNS and traffic routing

This architecture supports horizontal scaling and fault tolerance without introducing unnecessary complexity.

---

## Language

**PHP 8.2**

Reasons for selection:

* Strong ecosystem for API development
* Excellent MySQL and spatial query support
* High developer availability
* Mature tooling and debugging support

PHP performs very well for IO-bound, read-heavy APIs when paired with proper indexing and caching strategies.

---

## Framework

**Yii2 – Advanced Application Template**

Reasons for selection:

* Clear separation of concerns (frontend / backend / console)
* First-class support for REST APIs
* Excellent migration and database tooling
* Strong ActiveRecord + Query Builder
* Predictable performance characteristics

Yii2 provides structure without unnecessary abstraction and is easy for other developers to onboard into quickly.

---

## Storage

**MySQL 8.0 with Spatial Extensions**

### Why MySQL?

* Native support for `POINT` data types
* Built-in spatial indexes
* Functions such as `ST_Distance_Sphere`
* Lower operational complexity compared to PostGIS

Given the relatively small size of global airport datasets, MySQL spatial indexing is more than sufficient and avoids the overhead of a more complex GIS stack.

### Schema Strategy

* Airport coordinates stored as `POINT(longitude, latitude)`
* `SPATIAL INDEX` on location column
* Secondary indexes on country and identifiers

---

## Performance Strategy

The API is optimized for read performance using the following techniques:

* **Spatial Indexing**

  * Bounding-box prefiltering
  * Accurate distance calculations only after index narrowing

* **Stateless API Nodes**

  * Enables horizontal scaling
  * No session affinity required

* **Efficient Queries**

  * Database-side distance calculations
  * Minimal data transfer

* **Optional Enhancements**

  * Redis for hot query caching
  * HTTP cache headers (ETag / Cache-Control)
  * CloudFront in front of the API for global caching

This design comfortably supports sustained traffic while remaining cost-efficient.

---

## Scalability

### Vertical Scaling

* Increase EC2 instance size
* Upgrade RDS instance class

### Horizontal Scaling

* Add API nodes via Auto Scaling Group
* Add MySQL read replicas

### Future Growth

* Migrate to Aurora MySQL if needed
* Introduce asynchronous processing for analytics

The system can scale incrementally without requiring architectural rewrites.

---

## Estimated Monthly Costs (USD)

| Component                | Estimated Cost    |
| ------------------------ | ----------------- |
| EC2 (2× t3.small)        | ~$40              |
| RDS MySQL (db.t3.medium) | ~$70              |
| Load Balancer            | ~$18              |
| Data Transfer & Storage  | ~$20              |
| **Total**                | **~$150 / month** |

Costs can be reduced further for lower traffic or increased linearly as demand grows.

---

## Miscellaneous Considerations

* **Deployment**: CI/CD via GitHub Actions or similar
* **Observability**: CloudWatch logs + metrics
* **Security**:

  * IAM roles for infrastructure access
  * Private RDS subnet
  * HTTPS-only API

---

## Summary

This stack balances performance, clarity, and operational simplicity. It is intentionally conservative, production-tested, and easy for additional engineers to understand and extend. The architecture supports both current requirements and future growth without premature complexity.

---

<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Advanced Project Template</h1>
    <br>
</p>

Yii 2 Advanced Project Template is a skeleton [Yii 2](https://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![build](https://github.com/yiisoft/yii2-app-advanced/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-advanced/actions?query=workflow%3Abuild)

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
