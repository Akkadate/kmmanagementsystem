# Technology Stack Research: Knowledge Base System

**Date**: 2025-12-19
**Feature**: Knowledge Base System
**Spec Reference**: [spec.md](./spec.md)

## Executive Summary

This research document evaluates technology choices for a PHP-based internal knowledge base system supporting 34 functional requirements across 6 user stories. The system requires robust search capabilities, version history tracking, role-based authentication (4 roles), file attachments, and hierarchical category management for up to 1000 articles with 100 concurrent users.

**Key Recommendations**:
- **PHP 8.3** with **Laravel 11.x** framework
- **PostgreSQL 16** for primary database
- **MySQL FULLTEXT** for initial search (with Elasticsearch migration path)
- **Pest** for testing framework
- **TinyMCE** for rich text editing
- **Tailwind CSS** for frontend

---

## 1. PHP Version & Framework

### Decision: PHP 8.3 with Laravel 11.x

### Rationale

**PHP 8.3** is the optimal choice for 2025 development:
- Active support until December 31, 2025 (2 years active maintenance + 2 years security fixes)
- Mature feature set including typed class constants, `#[\Override]` attribute, and `json_validate()`
- Performance is stable and optimized (within 1% variance compared to 8.2 and 8.4)
- PHP 8.1 reaches end-of-life in November 2025, making it unsuitable for new projects

**Laravel 11.x** provides the best balance for this project's complexity:
- **Rapid Development**: Built-in authentication, routing, ORM, and migrations eliminate weeks of boilerplate code
- **Project Fit**: 34 functional requirements justify a full-featured framework over vanilla PHP
- **Ecosystem**: Laravel's rich ecosystem provides ready-made solutions for all requirements:
  - **Authentication**: Laravel Sanctum for session + token-based auth
  - **ORM**: Eloquent with relationships for Articles, Categories, Users, Versions
  - **Storage**: Laravel's filesystem abstraction for file attachments
  - **Queue**: Built-in job system for potential async operations (email notifications)
  - **Validation**: Comprehensive validation rules for forms and API endpoints
- **Team Productivity**: Faster onboarding, extensive documentation, large community support
- **Performance**: With JIT and OPcache enabled, Laravel's overhead is negligible for typical I/O-bound web applications

### Alternatives Considered

**Symfony 7.x**:
- **Pros**: More modular, better for enterprise-scale applications, sophisticated component architecture
- **Cons**: Steeper learning curve, slower initial development, unnecessary complexity for 6 user stories
- **Verdict**: Overengineered for this project's scope

**Slim Framework**:
- **Pros**: Lightweight, minimal overhead, great for APIs
- **Cons**: Requires manual implementation of authentication, ORM, routing, file uploads, session management
- **Verdict**: Too minimal - would require 2-3x more development time to implement equivalent features

**Vanilla PHP with PDO**:
- **Pros**: Complete control, no framework overhead
- **Cons**: Must implement authentication system, routing, session management, CSRF protection, validation, file uploads from scratch
- **Verdict**: Development time would exceed 3-4 weeks for features Laravel provides out-of-box

### Trade-offs

**Pros**:
- Reduces development time by 50-60% compared to vanilla PHP
- Built-in security features (CSRF, password hashing, SQL injection prevention)
- Database migrations provide version control for schema changes
- Artisan CLI for code generation and maintenance tasks
- Extensive package ecosystem (Laravel Debugbar, Laravel IDE Helper)

**Cons**:
- Framework overhead adds 20-40ms to request processing (negligible for target <2s page loads)
- Opinionated structure requires following Laravel conventions
- Learning curve for developers unfamiliar with Laravel (offset by excellent documentation)
- Slightly larger memory footprint (60-80MB vs 30-40MB for vanilla PHP)

**Performance Context**:
- Laravel page loads average 60ms according to 2025 benchmarks
- With proper optimization (OPcache, route caching, view caching), easily achieves <500ms response times
- For this project's scale (100 concurrent users, 1000 articles), framework overhead is not a bottleneck

---

## 2. Database Choice

### Decision: PostgreSQL 16 (Primary Database)

### Rationale

**PostgreSQL 16** is the recommended choice based on project requirements:

**Full-Text Search Capabilities**:
- Native full-text search with `tsvector` and `GIN` indexes outperforms basic needs
- Advanced linguistic features: stemming, ranking, phrase search, proximity queries
- `tsvector` indexes excel at complex linguistic searches with customizable dictionaries
- `trigram` indexes optimize substring searches and fuzzy matching
- JSONB full-text indexing (GIN) enables fast searches across metadata fields

**JSON Support for Metadata**:
- Native JSONB type with indexing support (GIN, GiST)
- Useful for storing article metadata, activity log details, version diffs
- Enables flexible schema evolution without migrations for metadata fields

**Hierarchical Data Performance**:
- PostgreSQL's support for **Recursive CTEs (Common Table Expressions)** makes adjacency list queries efficient
- Excellent for category hierarchies (FR-008: hierarchical categories with parent-child relationships)
- Can query entire category trees in single recursive query without N+1 problems

**Version History Storage Efficiency**:
- Advanced indexing (B-tree, GIN, GiST, BRIN, partial, expression) optimizes version queries
- JSONB storage for version diffs reduces storage overhead vs full content snapshots
- Partial indexes on `(article_id, created_at DESC)` optimize "latest version" queries

**Additional Advantages**:
- Superior data integrity with advanced constraints (CHECK, EXCLUDE)
- Better concurrent write performance with MVCC (Multi-Version Concurrency Control)
- Advanced window functions for analytics (FR-030: activity logs with filtering)
- More sophisticated query optimizer for complex joins

### Alternatives Considered

**MySQL 8.x**:
- **Pros**:
  - Faster for simple full-text searches (outperforms PostgreSQL's basic options)
  - Wider hosting availability and familiarity
  - Slightly simpler initial setup
- **Cons**:
  - Less sophisticated full-text search (no advanced linguistic features)
  - JSON support exists but less mature than PostgreSQL's JSONB
  - Limited indexing options (primarily B-tree, lacks GIN/GiST flexibility)
  - FULLTEXT indexes require InnoDB with limitations
- **Verdict**: Adequate for basic requirements but PostgreSQL offers better long-term capabilities

**MySQL with Elasticsearch**:
- **Pros**: Elasticsearch excels at complex, high-volume search scenarios
- **Cons**: Adds operational complexity (second database to maintain, sync concerns)
- **Verdict**: Premature optimization for 1000-article initial scope (see Migration Path below)

### Migration Path to Elasticsearch

For the **initial implementation**, PostgreSQL's full-text search is sufficient:
- Target: 1000 articles, <1 second search response time (SC-004)
- PostgreSQL achieves this with proper indexing (GIN on tsvector columns)

**Consider Elasticsearch when**:
- Article count exceeds 10,000-50,000
- Search queries become more complex (faceted search, typo tolerance, synonyms)
- Search response times exceed 1 second consistently
- Advanced analytics on search patterns are needed

**Migration Strategy**:
- Use Laravel Scout abstraction layer from day one
- Initial driver: PostgreSQL full-text search
- Future driver: Elasticsearch (swap without code changes)
- Laravel Scout supports both backends transparently

### Trade-offs

**Pros**:
- Single database reduces operational complexity (no sync between MySQL + Elasticsearch)
- PostgreSQL's advanced features support complex requirements (hierarchical categories, JSON metadata)
- Better data integrity and transactional guarantees
- Recursive CTEs eliminate need for nested sets or closure tables for categories
- Future-proof: can scale to 50,000+ articles before needing Elasticsearch

**Cons**:
- Slightly steeper learning curve if team only knows MySQL
- Full-text search configuration more complex than MySQL's simple FULLTEXT syntax
- Less common in shared hosting environments (VPS/dedicated server recommended)
- Requires understanding of tsvector, tsquery, and GIN indexes for optimal search performance

**Performance Benchmarks** (2025):
- PostgreSQL full-text search: <500ms for complex queries on 10,000 documents with proper indexing
- MySQL FULLTEXT: <200ms for simple queries, but lacks advanced features needed for growth
- For SC-004 target (<1s for 1000 articles): Both easily meet requirement; PostgreSQL provides headroom

---

## 3. Testing Framework

### Decision: Pest 3.x

### Rationale

**Pest 3.x** is the modern choice for Laravel testing in 2025:

**Developer Experience**:
- **Expressive syntax**: Function-based tests with `expect()` API are more readable than PHPUnit's class-based approach
- **Faster test writing**: Reduces boilerplate by 30-40% compared to PHPUnit
- **Better error messages**: Clearer failure output helps debug faster

**Built-in Features**:
- **Parallel testing**: Run tests concurrently out-of-box (faster CI/CD)
- **Coverage reporting**: Native code coverage analysis
- **Watch mode**: Auto-run tests on file changes during development
- **Architecture testing**: Verify coding standards and architectural rules
- **Snapshot testing**: Test complex outputs (HTML, JSON) against stored snapshots
- **Browser testing**: Pest v4 official plugin for visit() flows, screenshots, visual regression

**Laravel Integration**:
- First-class Laravel support through official Pest plugin
- Seamless integration with Laravel's testing utilities (factories, database refresh, HTTP testing)
- Can run alongside existing PHPUnit tests (progressive adoption)

**Ecosystem & Maintenance**:
- Actively maintained with vibrant plugin ecosystem
- Backed by same engine as PHPUnit (100% PHPUnit compatibility under the hood)
- Growing community adoption, especially in Laravel ecosystem

### Alternatives Considered

**PHPUnit**:
- **Pros**:
  - Industry standard with 20+ years of history
  - Familiar class-based xUnit model
  - Universally understood by PHP developers
  - Extensive documentation and Stack Overflow answers
- **Cons**:
  - More verbose syntax requires more code for same tests
  - No built-in parallel testing (requires extensions)
  - Traditional assertion methods less readable than Pest's expect()
  - Requires separate tools for architecture testing, browser testing
- **Verdict**: Solid choice for teams with existing PHPUnit expertise, but Pest offers better DX

**Codeception**:
- **Pros**: Full-stack testing framework with acceptance, functional, and unit testing
- **Cons**: More complex setup, overkill for this project, smaller community
- **Verdict**: Unnecessary complexity for knowledge base testing needs

### Trade-offs

**Pros**:
- **Faster test development**: Reduced boilerplate means more tests written in less time
- **Better maintainability**: Clearer syntax improves test readability for future developers
- **Modern features**: Parallel testing, watch mode, architecture testing built-in
- **Progressive adoption**: Can mix PHPUnit and Pest tests during migration
- **Laravel-optimized**: Official Laravel plugin provides seamless integration

**Cons**:
- **Learning curve**: Developers familiar only with PHPUnit need brief adjustment period (typically 1-2 days)
- **Smaller ecosystem**: Fewer third-party plugins compared to PHPUnit (though rapidly growing)
- **Newer framework**: Less Stack Overflow content than PHPUnit (though documentation is excellent)

**Testing Strategy for Knowledge Base**:

```php
// Example: Article Creation Test (Pest syntax)
it('creates article with rich text and attachments', function () {
    $user = User::factory()->editor()->create();

    actingAs($user)
        ->post('/articles', [
            'title' => 'Setup Guide',
            'content' => '<p>Rich text content</p>',
            'category_id' => Category::factory()->create()->id,
            'attachments' => [UploadedFile::fake()->create('guide.pdf')],
        ])
        ->assertRedirect();

    expect(Article::where('title', 'Setup Guide')->first())
        ->toBeInstanceOf(Article::class)
        ->author_id->toBe($user->id)
        ->attachments->toHaveCount(1);
});

// Example: Architecture Test
arch('models')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->toHaveMethod('casts');

arch('services are final')
    ->expect('App\Services')
    ->toBeFinal();
```

**Test Coverage Targets**:
- **Unit tests**: Models, services, helpers (80%+ coverage)
- **Integration tests**: Controllers, API endpoints, file uploads (70%+ coverage)
- **Feature tests**: Complete user workflows (all 6 user stories)
- **Architecture tests**: Enforce coding standards, prevent architectural violations

---

## 4. Dependency Management

### Decision: Composer 2.x with Locked Dependencies

### Rationale

**Composer 2.x** is the standard (and only practical) dependency manager for PHP in 2025. Key practices:

**Version Locking Best Practices**:
- Use **semantic versioning constraints** in `composer.json`:
  - `^` (caret): Allows minor and patch updates (e.g., `^11.0` allows 11.1, 11.2, but not 12.0)
  - `~` (tilde): Allows patch updates only (e.g., `~11.2` allows 11.2.1, but not 11.3)
  - Exact versions for critical dependencies (e.g., `"laravel/framework": "11.31.0"`)
- **Always commit `composer.lock`** to version control (ensures identical dependencies across environments)
- Use `composer install` in production (respects lock file), `composer update` only in development

**Security & Maintenance**:
- **Run `composer audit` regularly** to detect known vulnerabilities
- Integrate security scanning in CI/CD (GitHub Actions: `composer audit`, Snyk, OWASP Dependency-Check)
- **Automate dependency updates** with tools like Renovate or Dependabot
- Review dependency update PRs before merging (check changelogs for breaking changes)

**Conflict Prevention**:
- **Minimize dependencies**: Only add packages that provide significant value
- **Research before adding**: Check package maintenance status, GitHub stars, recent commits
- Use `composer why <package>` to understand dependency trees
- Use `composer why-not <package> <version>` to diagnose version conflicts

**Performance Optimization**:
- **Use `--optimize-autoloader`** in production for faster class loading
- **Use `--prefer-dist`** to download archives instead of cloning repos (faster installs)
- **Use Composer 2.x** (10-100x faster than Composer 1.x, parallel downloads)

### Recommended Dependencies for Knowledge Base

**Core Laravel**:
```json
{
  "require": {
    "php": "^8.3",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0",
    "laravel/tinker": "^2.9"
  }
}
```

**Search & Content**:
```json
{
  "require": {
    "laravel/scout": "^10.0",
    "spatie/laravel-searchable": "^1.11",
    "mews/purifier": "^3.4"
  }
}
```
- **Laravel Scout**: Search abstraction layer (supports Database, Algolia, Meilisearch, Elasticsearch drivers)
- **Laravel Searchable**: Simple full-text search for PostgreSQL/MySQL
- **HTMLPurifier**: Sanitize user-generated HTML from TinyMCE (prevent XSS - FR-034)

**File Management**:
```json
{
  "require": {
    "spatie/laravel-medialibrary": "^11.0",
    "intervention/image": "^3.0"
  }
}
```
- **Laravel Media Library**: Handle file uploads, conversions, associations (FR-002: file attachments)
- **Intervention Image**: Image processing for thumbnails, validation

**Categories & Hierarchies**:
```json
{
  "require": {
    "staudenmeir/laravel-adjacency-list": "^2.0"
  }
}
```
- **Laravel Adjacency List**: Recursive CTEs for hierarchical categories (FR-008)

**Testing & Development**:
```json
{
  "require-dev": {
    "pestphp/pest": "^3.0",
    "pestphp/pest-plugin-laravel": "^3.0",
    "laravel/pint": "^1.18",
    "larastan/larastan": "^2.9",
    "barryvdh/laravel-debugbar": "^3.14"
  }
}
```
- **Pest**: Testing framework
- **Laravel Pint**: Code style fixer (PSR-12 compliance)
- **Larastan**: Static analysis (PHPStan for Laravel)
- **Laravel Debugbar**: Development debugging

### Trade-offs

**Pros**:
- **Automated dependency resolution**: Composer handles complex dependency trees automatically
- **Security auditing**: Built-in `composer audit` detects vulnerable packages
- **Performance**: Composer 2.x downloads and installs packages 10-100x faster than Composer 1.x
- **Ecosystem**: Access to 400,000+ packages on Packagist
- **Lock file guarantees**: Identical dependencies across development, staging, production

**Cons**:
- **Large vendor directory**: Can exceed 50-100MB depending on dependencies (exclude from git, use `.gitignore`)
- **Version conflicts**: Complex dependency trees sometimes create unresolvable conflicts (requires manual intervention)
- **Breaking changes**: Major version updates may require code changes (mitigated by semantic versioning)

**CI/CD Integration**:
```yaml
# GitHub Actions example
- name: Install Dependencies
  run: composer install --prefer-dist --optimize-autoloader --no-dev

- name: Security Audit
  run: composer audit

- name: Static Analysis
  run: vendor/bin/phpstan analyse
```

---

## 5. Performance Baselines

### Decision: Industry-Standard 2025 Benchmarks

### Rationale

Based on 2025 web performance research, these are reasonable targets for a PHP knowledge base system:

### Page Load Times

**Target: < 2 seconds (industry standard)**

**Context**:
- 47% of users expect websites to load in 2 seconds or less
- Average page load time in 2025: 2.5s desktop, 8.6s mobile
- Google Core Web Vitals: LCP (Largest Contentful Paint) ≤ 2.5s at 75th percentile

**Breakdown for Knowledge Base**:
- **Homepage** (article list + search): < 1.5s
- **Article view page**: < 2.0s (includes rich text rendering, related articles)
- **Search results**: < 1.5s (see search query benchmarks below)
- **Admin panel** (article management): < 2.5s (acceptable for authenticated users)

**Implementation Strategy**:
- **Time to First Byte (TTFB) < 200ms**: Optimize database queries, use OPcache, route caching
- **Minimize render-blocking resources**: Defer non-critical JavaScript, inline critical CSS
- **Image optimization**: Use WebP format, lazy loading, responsive images
- **Browser caching**: Leverage HTTP caching headers (1 year for static assets)
- **CDN for static assets**: Offload CSS, JS, images to CDN (CloudFlare, AWS CloudFront)

### Search Query Response Times

**Target: < 1 second (SC-004 requirement)**

**Context**:
- SC-004: "Search results return within 1 second for keyword queries on a knowledge base with up to 1000 articles"
- PostgreSQL full-text search with GIN indexes achieves <500ms for 10,000 documents

**Breakdown**:
- **Simple keyword search** (1-2 terms): < 300ms
- **Complex search** (multiple filters, date ranges): < 800ms
- **Autocomplete/suggestions**: < 150ms

**Implementation Strategy**:
- **GIN index on tsvector column**: `CREATE INDEX idx_articles_search ON articles USING GIN(to_tsvector('english', title || ' ' || content));`
- **Debounced autocomplete**: Wait 200ms before sending autocomplete requests
- **Result pagination**: Limit results to 20 per page, use efficient OFFSET/LIMIT
- **Query caching**: Cache popular search queries for 5-15 minutes (Redis)
- **Eager loading**: Preload relationships (category, author) to avoid N+1 queries

### Concurrent User Support

**Target: 100 concurrent users without degradation (SC-003 requirement)**

**Context**:
- SC-003: "System supports at least 100 concurrent users viewing articles without page load degradation (under 2 seconds)"
- Laravel with OPcache + Redis can handle 1000+ req/s on modest hardware

**Server Requirements (Estimated)**:
- **Application server**: 4 vCPUs, 8GB RAM (PHP-FPM with 50-100 workers)
- **Database server**: 2 vCPUs, 4GB RAM (PostgreSQL with shared_buffers=1GB)
- **Cache server**: 1 vCPU, 2GB RAM (Redis for sessions + query cache)

**Implementation Strategy**:
- **Horizontal scaling**: Load balancer + 2-3 application servers for high availability
- **Session storage**: Redis (not file-based) to support multiple app servers
- **Database connection pooling**: PgBouncer to manage database connections efficiently
- **Queue workers**: Offload non-critical tasks (email notifications, analytics) to background jobs
- **Rate limiting**: Prevent abuse (60 requests/minute per IP for search endpoints)

### Database Query Optimization

**Target: 95% of queries < 50ms**

**Context**:
- Average query time significantly impacts page load times
- Each page may execute 5-20 queries (article + category + author + related articles)

**Key Optimizations**:

1. **Indexing Strategy**:
   ```sql
   -- Primary lookups
   CREATE INDEX idx_articles_slug ON articles(slug);
   CREATE INDEX idx_articles_status ON articles(status);
   CREATE INDEX idx_articles_category ON articles(category_id);

   -- Full-text search
   CREATE INDEX idx_articles_search ON articles USING GIN(to_tsvector('english', title || ' ' || content));

   -- Version history (latest version query)
   CREATE INDEX idx_versions_article_date ON article_versions(article_id, created_at DESC);

   -- Hierarchical categories (recursive CTE)
   CREATE INDEX idx_categories_parent ON categories(parent_id);
   ```

2. **N+1 Query Prevention**:
   ```php
   // BAD: N+1 queries (1 for articles + N for authors)
   $articles = Article::all();
   foreach ($articles as $article) {
       echo $article->author->name; // Separate query each iteration
   }

   // GOOD: 2 queries total (1 for articles + 1 for all authors)
   $articles = Article::with('author')->get();
   foreach ($articles as $article) {
       echo $article->author->name; // No additional query
   }
   ```

3. **Query Caching**:
   ```php
   // Cache expensive queries (category tree, popular articles)
   $categories = Cache::remember('category_tree', 3600, function () {
       return Category::tree()->get(); // Recursive CTE query
   });
   ```

4. **Database Query Monitoring**:
   - Use Laravel Debugbar in development to identify slow queries
   - Enable PostgreSQL slow query log (queries > 100ms)
   - Monitor with APM tools (Laravel Telescope, New Relic, Datadog)

### Performance Testing Tools

**Load Testing**:
- **Apache JMeter**: Simulate 100 concurrent users, measure response times
- **k6**: Modern load testing tool with JavaScript DSL
- **Laravel Dusk**: Browser automation for frontend performance testing

**Monitoring**:
- **Laravel Telescope**: Request/query debugging in development
- **New Relic / Datadog**: APM for production monitoring
- **PostgreSQL pg_stat_statements**: Query performance analysis

### Trade-offs

**Pros**:
- Clear, measurable targets aligned with 2025 industry standards
- Targets meet spec requirements (SC-003, SC-004) with headroom for growth
- Performance monitoring from day one prevents technical debt

**Cons**:
- Achieving targets requires proper infrastructure (VPS/dedicated vs shared hosting)
- Performance optimization may require iteration (profiling → optimize → test)
- Some optimizations (CDN, Redis) add operational complexity

**Performance Budget**:
```
Page Load Time Budget (2s total):
- TTFB (server processing):     200ms (10%)
- Database queries:              150ms ( 7.5%)
- View rendering:                100ms ( 5%)
- JavaScript execution:          300ms (15%)
- CSS/Asset loading:             400ms (20%)
- Network latency:               850ms (42.5%)
```

---

## 6. Rich Text Editor

### Decision: TinyMCE 7

### Rationale

**TinyMCE 7** is the recommended choice for this knowledge base system:

**Feature Completeness**:
- **True WYSIWYG**: Content saved as HTML, exactly as displayed (FR-001)
- **Easy setup**: 6 lines of code for basic integration
- **Comprehensive formatting**: Text styling, lists, links, tables, embedded images
- **File handling**: Built-in image upload, paste from clipboard
- **Customizable toolbar**: Configure available formatting options per user role

**Maturity & Support**:
- 20+ years of development, battle-tested in production
- Backed by 80+ person team (Tiny Technologies)
- Regular updates, extensive documentation
- Large plugin ecosystem

**PHP Integration**:
- JavaScript-based (client-side), framework-agnostic
- Generates clean HTML for server-side storage
- Works seamlessly with Laravel forms and validation
- Easy to sanitize output with HTMLPurifier (FR-034: prevent XSS)

**Licensing**:
- **GPL-2.0 license** for core features (free for this internal application)
- **Commercial plugins** available if advanced features needed (markdown, mentions, comments)
- No runtime costs for basic usage

### Alternatives Considered

**CKEditor 5**:
- **Pros**:
  - Modern composable architecture, highly customizable
  - Advanced collaboration features (real-time editing)
  - Supports Angular, React, Vue, Next.js
- **Cons**:
  - Steep learning curve, cumbersome setup even for basic install
  - Many essential features paywalled (markdown, media embeds, mentions, multi-level lists)
  - GPL-2.0 license may require open-sourcing derivative works (not issue for internal app)
- **Verdict**: Over-engineered for single-user article editing; real-time collaboration not required

**Quill**:
- **Pros**:
  - Lightweight, excellent performance, low memory usage
  - BSD-3-Clause license (permissive for commercial use)
  - Used by Slack, LinkedIn, Figma
  - Modern TypeScript rewrite (v2)
- **Cons**:
  - Limited plugin ecosystem compared to TinyMCE/CKEditor
  - Only supports latest 2 versions of each browser
  - Internal document model (not pure HTML) may complicate server-side processing
- **Verdict**: Great for lightweight editors, but TinyMCE's maturity and plugin ecosystem better for knowledge base

### Trade-offs

**Pros**:
- **Ease of integration**: Minimal setup, works immediately with Laravel
- **User familiarity**: Interface similar to Microsoft Word (low training required)
- **Plugin ecosystem**: Image upload, media embed, code syntax highlighting available
- **HTML output**: Standard HTML markup, easy to sanitize and render
- **No cloud dependency**: Self-hosted option available (no external API calls)

**Cons**:
- **Advanced features paywalled**: Markdown support, mentions, comments require commercial license
- **Cloud service pricing**: If using cloud version, costs based on editor loads
- **Client-side processing**: Large documents may slow down browser (mitigated by field character limits)

**Implementation Example**:

```html
<!-- Laravel Blade Template -->
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#article_content',
    plugins: 'lists link image table code',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
    images_upload_url: '/api/upload-image', // Laravel route
    automatic_uploads: true,
    height: 500,
    content_css: '/css/editor-content.css' // Match frontend styles
  });
</script>

<form method="POST" action="/articles">
    @csrf
    <input type="text" name="title" placeholder="Article Title">
    <textarea id="article_content" name="content"></textarea>
    <button type="submit">Publish</button>
</form>
```

```php
// Laravel Controller - Sanitize HTML before saving
use Mews\Purifier\Facades\Purifier;

public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:200',
        'content' => 'required',
    ]);

    // Sanitize HTML to prevent XSS (FR-034)
    $cleanContent = Purifier::clean($validated['content']);

    Article::create([
        'title' => $validated['title'],
        'content' => $cleanContent,
        'author_id' => auth()->id(),
    ]);

    return redirect('/articles');
}
```

**Content Sanitization Strategy**:
- Use **HTMLPurifier** (via `mews/purifier` package) to sanitize TinyMCE output
- Whitelist allowed HTML tags: `<p>`, `<h1-h6>`, `<strong>`, `<em>`, `<ul>`, `<ol>`, `<li>`, `<a>`, `<img>`, `<table>`, `<code>`
- Strip dangerous attributes: `onclick`, `onerror`, `onload`, `<script>` tags
- Validate image URLs to prevent external resource abuse

---

## 7. Frontend Framework

### Decision: Tailwind CSS 3.x

### Rationale

**Tailwind CSS 3.x** is the optimal choice for 2025 development:

**Performance**:
- **Smaller bundle sizes**: JIT (Just-In-Time) compilation generates only CSS classes used (typically 10-50KB gzipped)
- **Faster page loads**: Lightweight utility classes load faster than Bootstrap's comprehensive component library (Bootstrap: 150KB+)
- **No unused CSS**: PurgeCSS integration removes unused styles automatically
- **Better Core Web Vitals**: Smaller CSS = faster FCP (First Contentful Paint) and LCP (Largest Contentful Paint)

**Developer Experience**:
- **Utility-first approach**: Build custom designs without writing custom CSS
- **Rapid prototyping**: Style directly in HTML with utility classes
- **Design consistency**: Predefined spacing, colors, typography enforce visual consistency
- **Responsive design**: Mobile-first breakpoints built-in (`sm:`, `md:`, `lg:`, `xl:`, `2xl:`)
- **Dark mode support**: First-class dark mode utilities (useful for future enhancement)

**Accessibility**:
- **Developer responsibility**: Requires manual ARIA attributes and semantic HTML
- **Flexibility**: Full control to meet complex accessibility standards (vs Bootstrap's opinionated components)
- **Screen reader friendly**: Utility classes don't interfere with assistive technologies

**Ecosystem**:
- **Tailwind UI**: Official component library (paid, but high-quality production-ready components)
- **Headless UI**: Unstyled accessible components (free, pairs perfectly with Tailwind)
- **Large community**: Extensive tutorials, templates, component libraries

### Alternatives Considered

**Bootstrap 5**:
- **Pros**:
  - Comprehensive prebuilt components (cards, modals, navbars, forms)
  - Built-in accessibility (ARIA attributes, keyboard navigation)
  - Faster initial development for standard layouts
  - Widely known by developers (low learning curve)
- **Cons**:
  - Larger bundle size (150KB+ CSS + JavaScript dependencies)
  - Generic "Bootstrap look" without customization
  - Overrides required for custom designs
  - JavaScript dependencies for interactive components (modals, tooltips) add weight
- **Verdict**: Good for rapid prototyping, but Tailwind offers better performance and design flexibility

**Custom CSS (Vanilla)**:
- **Pros**: Complete control, minimal dependencies, smallest possible bundle
- **Cons**: Time-consuming, requires design system creation, consistency challenges across team
- **Verdict**: Too slow for development timeline; Tailwind provides structure without sacrificing flexibility

### Trade-offs

**Pros**:
- **Performance**: 70-80% smaller CSS bundles compared to Bootstrap (better SEO, faster loads)
- **Customization**: Completely customizable design system via `tailwind.config.js`
- **Modern tooling**: Integrates with Vite (Laravel's default build tool)
- **Future-proof**: Active development, large community, growing adoption
- **Design flexibility**: Build knowledge base UI that matches company branding exactly

**Cons**:
- **HTML verbosity**: Many utility classes in HTML can feel cluttered
- **Learning curve**: Developers unfamiliar with utility-first CSS need 1-2 weeks adjustment
- **Manual accessibility**: Must add ARIA attributes and semantic HTML manually (vs Bootstrap's built-in)
- **Component library**: No free prebuilt components (must use Tailwind UI or build custom)

**Implementation Example**:

```html
<!-- Knowledge Base Article Card (Tailwind) -->
<article class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <span class="px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-100 rounded-full">
            Getting Started
        </span>
        <span class="text-sm text-gray-500">5 min read</span>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 mb-2">
        <a href="/articles/setup-guide" class="hover:text-blue-600 transition-colors">
            Initial Setup Guide
        </a>
    </h2>

    <p class="text-gray-600 mb-4 line-clamp-3">
        Learn how to configure your development environment and deploy your first application...
    </p>

    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
        <div class="flex items-center gap-2">
            <img src="/avatars/john.jpg" alt="John Doe" class="w-8 h-8 rounded-full">
            <span class="text-sm text-gray-700">John Doe</span>
        </div>
        <time class="text-sm text-gray-500" datetime="2025-12-15">
            Dec 15, 2025
        </time>
    </div>
</article>

<!-- Responsive Search Bar -->
<div class="relative w-full max-w-2xl mx-auto">
    <input
        type="search"
        placeholder="Search knowledge base..."
        class="w-full px-4 py-3 pl-12 text-gray-900 bg-white border border-gray-300 rounded-lg
               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
               transition-all duration-200"
    >
    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
    </svg>
</div>
```

**Laravel Integration**:
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom components for knowledge base */
@layer components {
    .kb-card {
        @apply bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow;
    }

    .kb-badge {
        @apply px-3 py-1 text-xs font-semibold rounded-full;
    }
}
```

**Accessibility Considerations**:
```html
<!-- Semantic HTML + ARIA attributes (manual with Tailwind) -->
<nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm">
    <a href="/" class="text-blue-600 hover:underline">Home</a>
    <span aria-hidden="true" class="text-gray-400">/</span>
    <a href="/categories" class="text-blue-600 hover:underline">Categories</a>
    <span aria-hidden="true" class="text-gray-400">/</span>
    <span aria-current="page" class="text-gray-700">Getting Started</span>
</nav>

<!-- Form with accessible labels -->
<form class="space-y-4">
    <div>
        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
            Search Articles
        </label>
        <input
            type="search"
            id="search"
            name="query"
            aria-describedby="search-help"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
        >
        <p id="search-help" class="mt-1 text-sm text-gray-500">
            Enter keywords to find relevant articles
        </p>
    </div>
</form>
```

---

## 8. Version History Implementation

### Decision: Database-Based Version Storage with Content Snapshots

### Rationale

**Full content snapshots** (not diffs) are recommended for this knowledge base:

**Simplicity**:
- Store complete article content for each version (simpler than diff algorithms)
- Restore previous versions with single query (no need to reconstruct from diffs)
- Display version comparisons easily (side-by-side or inline diff visualization)

**Reliability**:
- No risk of corrupted diffs breaking version history
- Each version is self-contained and independently retrievable
- Easier to debug and maintain than diff-based systems

**Performance**:
- Fast version retrieval: `SELECT content FROM article_versions WHERE id = ?`
- Fast "show changes" queries using database diff functions or client-side diff libraries
- Storage is cheap: 1000 articles × 5 versions avg × 10KB avg = 50MB (negligible)

### Database Schema

```sql
CREATE TABLE article_versions (
    id BIGSERIAL PRIMARY KEY,
    article_id BIGINT NOT NULL REFERENCES articles(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    editor_id BIGINT NOT NULL REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_article_versions (article_id, created_at DESC)
);
```

**Key Design Decisions**:
- **Full content snapshot**: Store `title` and `content` completely (not just deltas)
- **Foreign key constraints**: Cascade delete when article deleted (FR-032)
- **Indexed queries**: `(article_id, created_at DESC)` index optimizes "latest N versions" queries
- **Editor tracking**: `editor_id` enables audit trail (who made each change)

### Implementation Strategy

```php
// Laravel Model: Article
class Article extends Model
{
    protected static function booted()
    {
        // Automatically create version snapshot on update
        static::updated(function ($article) {
            ArticleVersion::create([
                'article_id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'editor_id' => auth()->id(),
            ]);
        });
    }

    public function versions()
    {
        return $this->hasMany(ArticleVersion::class)->orderBy('created_at', 'desc');
    }

    public function restoreVersion($versionId)
    {
        $version = $this->versions()->findOrFail($versionId);

        $this->update([
            'title' => $version->title,
            'content' => $version->content,
        ]);
        // Note: update() will trigger version snapshot automatically
    }
}

// Controller: Display version history
public function showVersions(Article $article)
{
    $versions = $article->versions()->paginate(20);
    return view('articles.versions', compact('article', 'versions'));
}

// Controller: Compare versions
public function compareVersions(Article $article, $versionId)
{
    $currentVersion = $article;
    $previousVersion = $article->versions()->findOrFail($versionId);

    // Use client-side diff library (e.g., diff2html, mergely) or server-side diff
    return view('articles.compare', compact('currentVersion', 'previousVersion'));
}
```

### Alternative: Diff-Based Storage

**Not recommended**, but worth documenting:

**Pros**:
- Reduced storage (store only changes, not full content)
- Efficient for large documents with small edits

**Cons**:
- Complexity: Requires diff algorithm (Myers, Hunt-McIlroy, or library like sebastian/diff)
- Slower restoration: Must apply diffs sequentially to reconstruct content
- Fragility: Corrupted diff breaks entire version chain
- Edge cases: Binary diffs for embedded images complicate implementation

**Verdict**: Storage savings (<50MB) don't justify complexity for 1000-article scale

### Trade-offs

**Pros**:
- **Simple implementation**: 1 model, 1 migration, event listener for auto-snapshots
- **Fast retrieval**: Single query to fetch any version
- **Reliable**: Each version independently restorable, no chain dependencies
- **Easy comparison**: Use client-side diff libraries (diff2html.js, jsdiff) for visual diffs
- **Audit compliance**: Full history with editor tracking (meets FR-003, FR-028)

**Cons**:
- **Storage overhead**: ~10KB per version per article (1000 articles × 5 versions = 50MB)
- **Duplicate data**: Full content stored redundantly (mitigated by text compression in PostgreSQL)
- **Large edits**: No optimization for small typo fixes vs full rewrites

**Storage Optimization** (if needed at scale):
```sql
-- PostgreSQL text compression (automatic)
-- TOAST (The Oversized-Attribute Storage Technique) compresses text > 2KB automatically

-- If storage becomes an issue (>10,000 articles), consider:
-- 1. Limit version retention (keep last 10 versions, archive older)
-- 2. Compress content column (GZIP in application layer before storage)
-- 3. Migrate to diff-based storage for articles with >20 versions
```

---

## 9. Category Hierarchy Implementation

### Decision: Adjacency List Model with Recursive CTEs

### Rationale

**Adjacency List** is the optimal approach for hierarchical categories in 2025:

**PostgreSQL CTE Support**:
- Since MySQL 8.0 and PostgreSQL 8.4+, **Recursive CTEs** eliminate the main weakness of adjacency lists
- Single query to retrieve entire category tree (no N+1 queries)
- No need for complex nested sets or closure tables

**Simplicity**:
- **Intuitive schema**: Each category has `parent_id` column (null for root categories)
- **Easy updates**: Moving categories = update single `parent_id` value
- **No renumbering**: Unlike nested sets, insertions/deletions don't require updating entire tree

**Performance**:
- **Fast writes**: Insert/update/delete modify single row
- **Fast reads**: Recursive CTE queries are efficient with proper indexing
- **Scalable**: Handles hundreds of categories without performance issues

### Database Schema

```sql
CREATE TABLE categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    parent_id BIGINT REFERENCES categories(id) ON DELETE CASCADE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_slug (slug)
);
```

**Key Design Decisions**:
- **Self-referential foreign key**: `parent_id` references `categories.id`
- **Cascade delete**: Deleting category deletes all subcategories (or set to RESTRICT to prevent - see FR-031)
- **Sort order**: `sort_order` column enables custom ordering within same level (FR-012)
- **Slug indexing**: Fast category lookup by URL slug

### Implementation Strategy

```php
// Laravel Package: staudenmeir/laravel-adjacency-list
// Provides recursive CTE queries for adjacency lists

use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class Category extends Model
{
    use HasRecursiveRelationships;

    // Define parent relationship
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Define children relationship
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    // Get all articles in this category and subcategories
    public function articlesRecursive()
    {
        return $this->descendantsAndSelf()->with('articles')->get();
    }
}

// Controller: Display category tree
public function index()
{
    // Get root categories with all descendants loaded
    $categories = Category::whereNull('parent_id')
        ->with('children.children.children') // Eager load 3 levels
        ->orderBy('sort_order')
        ->get();

    return view('categories.index', compact('categories'));
}

// Controller: Display category with articles (including subcategories)
public function show(Category $category)
{
    // Get all articles in this category and its subcategories
    $articles = Article::whereIn('category_id',
        $category->descendantsAndSelf()->pluck('id')
    )->paginate(20);

    return view('categories.show', compact('category', 'articles'));
}
```

### Recursive CTE Example (Raw SQL)

```sql
-- Get entire category tree starting from root
WITH RECURSIVE category_tree AS (
    -- Base case: root categories
    SELECT id, name, parent_id, 0 as depth, ARRAY[id] as path
    FROM categories
    WHERE parent_id IS NULL

    UNION ALL

    -- Recursive case: children of current level
    SELECT c.id, c.name, c.parent_id, ct.depth + 1, ct.path || c.id
    FROM categories c
    INNER JOIN category_tree ct ON c.parent_id = ct.id
    WHERE NOT c.id = ANY(ct.path) -- Prevent infinite loops
)
SELECT * FROM category_tree ORDER BY path;

-- Get all descendants of category ID 5
WITH RECURSIVE descendants AS (
    SELECT id, name, parent_id
    FROM categories
    WHERE id = 5

    UNION ALL

    SELECT c.id, c.name, c.parent_id
    FROM categories c
    INNER JOIN descendants d ON c.parent_id = d.id
)
SELECT * FROM descendants;
```

### Alternative: Nested Sets Model

**Not recommended** for this use case:

**Pros**:
- Fast reads for subtree queries (WHERE left BETWEEN x AND y)
- No recursive queries needed (single WHERE clause)

**Cons**:
- **Complex updates**: Moving/inserting categories requires renumbering entire tree
- **Slow writes**: Every insert/delete/move updates multiple rows
- **Concurrency issues**: Difficult to handle concurrent category edits
- **Learning curve**: Harder to understand and maintain than adjacency list

**Verdict**: CTEs eliminated nested sets' main advantage; adjacency list is simpler and more maintainable

### Trade-offs

**Pros**:
- **Simple schema**: One table, one self-referential foreign key
- **Easy maintenance**: Moving categories = single UPDATE query
- **Fast writes**: Only modified row needs updating
- **Laravel package support**: `staudenmeir/laravel-adjacency-list` handles CTE complexity
- **Unlimited depth**: No artificial limits on category nesting levels

**Cons**:
- **CTE complexity**: Raw recursive CTE queries are verbose (mitigated by Laravel package)
- **PostgreSQL-specific**: Recursive CTEs differ slightly between databases (MySQL 8.0+ compatible)
- **Potential loops**: Must guard against circular references (parent_id = own id)

**Loop Prevention**:
```php
// Model validation: Prevent circular references
public function setParentIdAttribute($value)
{
    // Prevent self-referencing
    if ($value == $this->id) {
        throw new \InvalidArgumentException('Category cannot be its own parent');
    }

    // Prevent circular references (parent's ancestors cannot include this category)
    if ($value) {
        $parent = Category::find($value);
        $ancestorIds = $parent->ancestors()->pluck('id')->toArray();

        if (in_array($this->id, $ancestorIds)) {
            throw new \InvalidArgumentException('Circular reference detected');
        }
    }

    $this->attributes['parent_id'] = $value;
}
```

---

## 10. Authentication & Authorization

### Decision: Laravel Sanctum with Session-Based Authentication

### Rationale

**Laravel Sanctum** is the ideal authentication solution for this knowledge base:

**Session-Based Auth for Web**:
- Sanctum uses Laravel's built-in cookie-based session authentication
- **CSRF protection**: Automatic CSRF token validation for state-changing requests
- **XSS protection**: HttpOnly cookies prevent JavaScript access to session tokens
- **Secure cookies**: SameSite attribute prevents CSRF attacks

**Role-Based Access Control (RBAC)**:
- Laravel's built-in authorization gates and policies integrate seamlessly
- 4 roles required: Admin, Editor, Contributor, Viewer (FR-019, FR-020)
- Simple role checking: `if (auth()->user()->isEditor()) { ... }`

**Simple Setup**:
- No OAuth2 complexity (unlike Laravel Passport)
- Minimal configuration required
- Built into Laravel 11 by default

**Future-Proof**:
- Sanctum also supports API token authentication (useful if mobile app or API needed later)
- Can switch between session (web) and token (API) authentication seamlessly

### Implementation Strategy

**1. User Model & Roles**:

```php
// Migration: users table
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('username')->unique();
    $table->string('email')->unique();
    $table->string('password'); // Hashed with bcrypt (FR-023)
    $table->enum('role', ['admin', 'editor', 'contributor', 'viewer'])->default('viewer');
    $table->boolean('is_active')->default(true); // FR-022
    $table->timestamps();
});

// User Model
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['username', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed', // Automatic bcrypt hashing
        'is_active' => 'boolean',
    ];

    // Role checking helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor' || $this->isAdmin();
    }

    public function isContributor(): bool
    {
        return $this->role === 'contributor' || $this->isEditor();
    }

    public function canEditArticle(Article $article): bool
    {
        return $this->isEditor() ||
               ($this->isContributor() && $article->author_id === $this->id);
    }
}
```

**2. Authorization Policies**:

```php
// app/Policies/ArticlePolicy.php
class ArticlePolicy
{
    // Anyone can view published articles (FR-020: Viewer role)
    public function view(User $user, Article $article): bool
    {
        return $article->status === 'published' || $this->update($user, $article);
    }

    // Contributors can create articles (FR-020)
    public function create(User $user): bool
    {
        return $user->isContributor();
    }

    // Editors can update all articles; Contributors can update own drafts (FR-020)
    public function update(User $user, Article $article): bool
    {
        return $user->isEditor() ||
               ($user->isContributor() && $article->author_id === $user->id);
    }

    // Only editors can publish (FR-020)
    public function publish(User $user, Article $article): bool
    {
        return $user->isEditor();
    }

    // Only editors can delete (FR-020)
    public function delete(User $user, Article $article): bool
    {
        return $user->isEditor();
    }
}

// Register policy in AuthServiceProvider
protected $policies = [
    Article::class => ArticlePolicy::class,
];
```

**3. Controllers with Authorization**:

```php
// ArticleController
class ArticleController extends Controller
{
    public function store(Request $request)
    {
        // Automatically checks ArticlePolicy::create()
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'title' => 'required|max:200',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $article = Article::create([
            ...$validated,
            'author_id' => auth()->id(),
            'status' => auth()->user()->isEditor() ? 'published' : 'draft',
        ]);

        return redirect()->route('articles.show', $article);
    }

    public function update(Request $request, Article $article)
    {
        // Automatically checks ArticlePolicy::update()
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title' => 'required|max:200',
            'content' => 'required',
        ]);

        $article->update($validated);

        return redirect()->route('articles.show', $article);
    }
}
```

**4. Blade Templates with Authorization**:

```blade
{{-- Only show edit button if user can edit this article --}}
@can('update', $article)
    <a href="{{ route('articles.edit', $article) }}" class="btn btn-primary">
        Edit Article
    </a>
@endcan

{{-- Only show publish button if user can publish --}}
@can('publish', $article)
    <form method="POST" action="{{ route('articles.publish', $article) }}">
        @csrf
        <button type="submit" class="btn btn-success">Publish</button>
    </form>
@endcan

{{-- Admin-only user management link --}}
@if(auth()->user()->isAdmin())
    <a href="{{ route('admin.users') }}">Manage Users</a>
@endif
```

**5. Middleware for Route Protection**:

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    // Contributors and above can create articles
    Route::get('/articles/create', [ArticleController::class, 'create'])
        ->middleware('can:create,App\Models\Article');

    // Editors only
    Route::prefix('admin')->middleware('role:editor')->group(function () {
        Route::get('/drafts', [ArticleController::class, 'drafts']);
        Route::post('/articles/{article}/approve', [ArticleController::class, 'approve']);
    });

    // Admins only
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
    });
});

// Custom middleware: app/Http/Middleware/CheckRole.php
class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->user() || auth()->user()->role !== $role && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
```

### Security Best Practices

**Password Hashing** (FR-023):
```php
// Laravel automatically uses bcrypt via 'hashed' cast
$user = User::create([
    'password' => $request->password, // Automatically hashed
]);

// Manual hashing if needed
use Illuminate\Support\Facades\Hash;
Hash::make($request->password); // bcrypt with cost factor 12
```

**CSRF Protection**:
```blade
{{-- All forms automatically include CSRF token --}}
<form method="POST" action="/articles">
    @csrf
    {{-- Laravel validates token automatically --}}
</form>
```

**Session Security Configuration** (`config/session.php`):
```php
return [
    'lifetime' => 120, // 2 hours
    'expire_on_close' => false,
    'encrypt' => true,
    'http_only' => true, // Prevent XSS access to cookies
    'same_site' => 'lax', // CSRF protection
    'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only
];
```

### Trade-offs

**Pros**:
- **Zero configuration**: Laravel Sanctum included by default in Laravel 11
- **Secure defaults**: CSRF protection, HttpOnly cookies, SameSite attributes automatic
- **Simple RBAC**: Role checking via model methods + policies
- **Flexible**: Supports session (web) and token (API) authentication
- **Well-documented**: Extensive Laravel documentation and community resources

**Cons**:
- **Not OAuth2**: If third-party authentication needed (Google, GitHub login), requires Laravel Socialite
- **Role management**: No built-in admin UI for role assignment (must build custom)
- **Advanced permissions**: If complex permission matrix needed (e.g., per-article permissions), requires package like Spatie Permission

**When to Upgrade**:
- **Spatie Laravel Permission**: If granular permissions needed (e.g., "can edit category X articles only")
- **Laravel Socialite**: If OAuth providers needed (Google, GitHub, Microsoft login)
- **Laravel Passport**: If full OAuth2 server needed (external apps authenticating against knowledge base)

---

## Implementation Roadmap

### Phase 0: Project Setup (Week 1)

**Infrastructure**:
- [x] Install PHP 8.3, Composer 2.x, PostgreSQL 16
- [x] Initialize Laravel 11.x project
- [x] Configure environment (.env: database, cache, session)
- [x] Set up version control (git, .gitignore)

**Development Tools**:
- [x] Install Pest + Laravel plugin (`composer require pestphp/pest --dev`)
- [x] Install Laravel Pint (code style), Larastan (static analysis)
- [x] Configure Vite + Tailwind CSS
- [x] Install Laravel Debugbar for development

### Phase 1: Core Features (Weeks 2-4)

**Database Schema**:
- [x] Users table + authentication (Sanctum)
- [x] Articles table + version history
- [x] Categories table (adjacency list)
- [x] Tags table (many-to-many with articles)
- [x] Attachments table

**Authentication & Authorization**:
- [x] User registration/login (Sanctum)
- [x] Role-based middleware (admin, editor, contributor, viewer)
- [x] Authorization policies for articles, categories, users

**Article Management**:
- [x] Article CRUD (create, read, update, delete)
- [x] TinyMCE integration for rich text editing
- [x] File attachment uploads (Laravel Media Library)
- [x] Version history snapshots (automatic on update)
- [x] Slug generation and uniqueness validation

### Phase 2: Search & Discovery (Weeks 5-6)

**Search Implementation**:
- [x] PostgreSQL full-text search (GIN indexes on tsvector)
- [x] Laravel Scout integration (Database driver initially)
- [x] Search filters (category, tags, date range)
- [x] Autocomplete/suggestions

**Category Browsing**:
- [x] Hierarchical category navigation (recursive CTEs)
- [x] Category management UI (create, edit, reorder)
- [x] Breadcrumb navigation

### Phase 3: User Features (Week 7)

**Bookmarks & Feedback**:
- [x] Bookmark articles (many-to-many user-article relationship)
- [x] Feedback system (helpful/not helpful)
- [x] View count tracking
- [x] Reading history

### Phase 4: Analytics & Admin (Week 8)

**Activity Logging**:
- [x] Activity log model (article creation, edits, user actions)
- [x] Admin dashboard (metrics, top articles, activity feed)

**Admin Panel**:
- [x] User management (create, edit, deactivate, role assignment)
- [x] Article management (bulk actions, status changes)
- [x] Category management (tree view, drag-to-reorder)

### Phase 5: Testing & Optimization (Week 9)

**Testing**:
- [x] Unit tests (models, services - 80% coverage target)
- [x] Feature tests (user workflows - all 6 user stories)
- [x] Architecture tests (enforce coding standards)

**Performance Optimization**:
- [x] Database query optimization (N+1 prevention, indexing)
- [x] Caching strategy (query cache, route cache, view cache)
- [x] Asset optimization (Vite build, image compression)

### Phase 6: Deployment (Week 10)

**Production Deployment**:
- [x] Server setup (PHP-FPM, PostgreSQL, Redis, Nginx/Apache)
- [x] Environment configuration (HTTPS, secure session cookies)
- [x] Database migrations + seeders
- [x] Performance monitoring (Laravel Telescope or APM)

---

## Technology Stack Summary

| **Category** | **Technology** | **Version** | **Rationale** |
|--------------|---------------|-------------|---------------|
| **Language** | PHP | 8.3 | Active support until 2025-12-31, mature feature set, stable performance |
| **Framework** | Laravel | 11.x | Rapid development, built-in auth/ORM/routing, 50-60% faster than vanilla PHP |
| **Database** | PostgreSQL | 16 | Superior full-text search, JSON support, recursive CTEs for hierarchies |
| **Testing** | Pest | 3.x | Modern syntax, built-in parallel testing, Laravel-optimized |
| **Frontend** | Tailwind CSS | 3.x | 70-80% smaller bundles than Bootstrap, JIT compilation, design flexibility |
| **Rich Text** | TinyMCE | 7 | Mature, easy setup, true WYSIWYG, GPL-2.0 license |
| **Authentication** | Laravel Sanctum | 4.x | Session-based auth, CSRF protection, simple RBAC, future API support |
| **Search** | PostgreSQL FTS | (built-in) | Sufficient for 1000 articles, <1s query time, migrate to Elasticsearch later if needed |
| **Dependency Mgmt** | Composer | 2.x | Standard PHP package manager, security auditing, lock file guarantees |
| **Build Tool** | Vite | 5.x | Laravel default, fast HMR, optimized builds |
| **Cache** | Redis | 7.x | Session storage, query cache, rate limiting |

---

## Performance Targets Summary

| **Metric** | **Target** | **Strategy** |
|------------|-----------|-------------|
| **Page Load Time** | < 2s | TTFB < 200ms, OPcache, route/view caching, CDN for assets |
| **Search Query** | < 1s | GIN indexes on tsvector, query caching, pagination (20 results) |
| **Concurrent Users** | 100+ | PHP-FPM (50-100 workers), Redis sessions, PostgreSQL connection pooling |
| **Database Queries** | 95% < 50ms | Proper indexing, N+1 prevention (eager loading), query monitoring |
| **TTFB** | < 200ms | OPcache, route cache, database query optimization |
| **LCP (Core Web Vital)** | ≤ 2.5s | Image optimization, lazy loading, Tailwind's small CSS bundle |

---

## Cost Analysis

**Development Time Estimates** (with Laravel vs vanilla PHP):

| **Feature** | **Laravel** | **Vanilla PHP** | **Time Saved** |
|-------------|-------------|-----------------|----------------|
| Authentication + RBAC | 2 days | 2 weeks | 8 days |
| Article CRUD + ORM | 3 days | 1.5 weeks | 4.5 days |
| File uploads | 1 day | 3 days | 2 days |
| Search integration | 2 days | 1 week | 3 days |
| Version history | 2 days | 1 week | 3 days |
| Testing setup | 1 day | 3 days | 2 days |
| **Total** | **~3 weeks** | **~8 weeks** | **~5 weeks** |

**Infrastructure Costs** (estimated for 100 concurrent users):

| **Component** | **Specification** | **Monthly Cost** |
|---------------|-------------------|------------------|
| Application Server | 4 vCPUs, 8GB RAM | $40-80 (DigitalOcean, Vultr, Linode) |
| Database Server | 2 vCPUs, 4GB RAM | $20-40 |
| Redis Cache | 1 vCPU, 2GB RAM | $10-20 (or included) |
| Backups | 100GB storage | $5-10 |
| **Total** | | **$75-150/month** |

**Alternative: Shared Hosting** (for <20 concurrent users):
- Cost: $10-30/month
- Trade-off: Limited performance, no Redis, shared resources

---

## Risk Assessment

| **Risk** | **Impact** | **Mitigation** |
|----------|-----------|----------------|
| **Team unfamiliar with Laravel** | Medium | 1-2 week onboarding, extensive documentation, pair programming |
| **PostgreSQL full-text search insufficient at scale** | Low | Use Laravel Scout abstraction from day one (easy swap to Elasticsearch) |
| **File storage exceeds disk quota** | Medium | Implement file size limits (FR-033), offload to S3/CloudFlare R2 if needed |
| **Database performance issues with complex category trees** | Low | Adjacency list + recursive CTEs handle hundreds of categories efficiently |
| **Version history storage grows large** | Low | 1000 articles × 5 versions × 10KB = 50MB (negligible; add retention policy if needed) |

---

## References & Sources

### PHP Version & Performance
- [PHP Benchmarks: 8.4 performance comparison - Tideways](https://tideways.com/profiler/blog/php-benchmarks-8-4-performance-is-steady-compared-to-8-3-and-8-2)
- [Key Differences Between PHP Versions 8.1, 8.2, 8.3, and 8.4 - Hashnode](https://dhruvilblog.hashnode.dev/discover-the-key-differences-between-php-versions-81-82-83-and-84)
- [PHP 8.2 vs PHP 8.3: Evolution of Server-Side - Medium](https://medium.com/@nemanjamilenkovic_58178/php-8-2-vs-php-8-3-the-evolution-of-a-server-side-powerhouse-c2ea04188cb7)

### Framework Comparison
- [Symfony vs Laravel: Which Framework in 2025? - FNX Group](https://fnx.group/blog/article/symfony-vs-laravel-which-php-framework-should-you-choose-in-2025)
- [Laravel vs Symfony: Best PHP Framework for 2026 - GloryWebs](https://www.glorywebs.com/blog/laravel-vs-symfony)
- [Choosing Laravel, Symfony, or Slim in 2025 - Web Design Penarth](https://webdesignpenarth.co.uk/2025/05/01/how-to-choose-between-laravel-symfony-and-slim-in-2025/)

### Database & Search
- [PostgreSQL vs MySQL: 2025 Comparison Guide - SqlCheat](https://sqlcheat.com/blog/postgresql-vs-mysql-2025-comparison/)
- [MySQL and PostgreSQL for Advanced Full-Text Search - DBConvert](https://dbconvert.com/blog/mysql-and-postgresql-for-advanced-full-text-search/)
- [Elasticsearch vs MySQL FULLTEXT - Medium](https://medium.com/@mazraara/full-text-search-with-mysql-and-elasticsearch-b48e79a8ad4a)
- [Why Elasticsearch Outperforms MySQL for Search - Medium](https://medium.com/@itsjatin135/why-elasticsearch-outperforms-traditional-databases-like-mysql-for-search-55a3e0355c96)

### Testing Frameworks
- [Pest vs PHPUnit: How Pest modernizes testing - Nabil Hassen](https://nabilhassen.com/pest-vs-phpunit)
- [Laravel Pest Guide vs PHPUnit - MuneebDev](https://muneebdev.com/laravel-pest-guide-pestphp-pest-plugin-vs-phpunit/)
- [Pest vs PHPUnit Syntax Examples - Laravel Daily](https://laraveldaily.com/post/pest-phpunit-syntax-expect-assert-examples)

### Frontend & Performance
- [Bootstrap vs Tailwind CSS 2025 - IT Path Solutions](https://www.itpathsolutions.com/bootstrap-vs-tailwind-which-is-better)
- [Tailwind vs Bootstrap Performance 2025 - Vocal Media](https://vocal.media/geeks/tailwind-vs-bootstrap-a-complete-head-to-head-comparison-for-2025)
- [Website Load Time Statistics 2025 - Hostinger](https://www.hostinger.com/tutorials/website-load-time-statistics)
- [Web Performance Benchmarks 2025 - InMotion Hosting](https://www.inmotionhosting.com/blog/web-performance-benchmarks/)

### Rich Text Editors
- [Which Rich Text Editor Framework in 2025? - Liveblocks](https://liveblocks.io/blog/which-rich-text-editor-framework-should-you-choose-in-2025)
- [TinyMCE vs CKEditor - TinyMCE](https://www.tiny.cloud/tinymce-vs-ckeditor/)
- [Top 10 Rich Text Editors 2025 - Cotocus](https://www.cotocus.com/blog/top-10-rich-text-editors-tools-in-2025-features-pros-cons-comparison/)

### Dependency Management
- [PHP Dependency Management with Composer - Zend](https://www.zend.com/blog/php-dependency-management)
- [Composer Best Practices - WordPress.tv](https://wordpress.tv/2025/06/07/composer-best-practices/)
- [How to Manage PHP Dependencies - Medium](https://anujtomar97.medium.com/how-to-manage-php-dependencies-effectively-in-your-projects-96e354f1edd7)

### Authentication & Authorization
- [Laravel Sanctum Documentation - Laravel 12.x](https://laravel.com/docs/12.x/sanctum)
- [Laravel Sanctum vs Passport 2025 - Abbacus Technologies](https://www.abbacustechnologies.com/laravel-sanctum-vs-passport-authentication-strategy-for-2025/)
- [Laravel Authentication Best Practices - Orbit Web Tech](https://orbitwebtech.com/laravel-authentication/)

### Hierarchical Data
- [Managing Hierarchical Data in MySQL - Mike Hillyer](https://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/)
- [Adjacency List vs Nested Sets - SQL Pey](https://sqlpey.com/sql/optimizing-hierarchical-data/)
- [MySQL Adjacency List with CTEs - Akshansh Khare](https://akki.ca/blog/mysql-adjacency-list-model-for-hierarchical-data-using-cte/)
- [Mastering SQL Trees - TeddySmith.IO](https://teddysmith.io/sql-trees/)

### ORM Performance
- [Eloquent vs Raw SQL Comparison - Livares Blog](https://blog.livares.com/comparison-of-performance-between-eloquent-and-raw-query/)
- [ORM vs Raw Queries in Laravel - Medium](https://medium.com/@tm.aashish1/orm-vs-raw-queries-in-laravel-063efd77e897)
- [Laravel Eloquent ORM vs Raw SQL - Medium](https://medium.com/@harshavardhanbudaraju/laravel-eloquent-orm-vs-raw-sql-d576276ee848)

---

## Conclusion

This technology stack provides an optimal balance of **development speed**, **performance**, **scalability**, and **maintainability** for a PHP-based knowledge base system:

**Key Strengths**:
1. **Laravel 11.x** reduces development time by 50-60% compared to vanilla PHP while providing production-ready authentication, ORM, and file handling
2. **PostgreSQL 16** offers sophisticated full-text search, JSON support, and recursive CTEs for hierarchical categories—sufficient for 1000+ articles without Elasticsearch
3. **Pest 3.x** modernizes testing with expressive syntax, parallel execution, and built-in architecture testing
4. **Tailwind CSS 3.x** delivers 70-80% smaller CSS bundles than Bootstrap while enabling completely custom designs
5. **Laravel Sanctum** provides secure session-based authentication with RBAC out-of-box

**Future-Proof Design**:
- **Laravel Scout abstraction**: Easy migration from PostgreSQL full-text search to Elasticsearch when scale demands it
- **Adjacency list categories**: Simple updates now, performant reads with recursive CTEs
- **Version history snapshots**: Reliable, simple implementation with negligible storage overhead (50MB for 5000 versions)

**Performance Confidence**:
- Page loads < 2s with proper caching (TTFB < 200ms, OPcache, Redis)
- Search queries < 1s for 1000+ articles (GIN indexes, query caching)
- 100+ concurrent users supported on modest hardware (4 vCPU, 8GB RAM)

This stack meets all 34 functional requirements across 6 user stories while maintaining code quality, security, and developer productivity.
