# Tasks: Knowledge Base System

**Input**: Design documents from `/specs/001-knowledge-base-system/`
**Prerequisites**: plan.md (Laravel 11 + PostgreSQL 16), spec.md (6 user stories), research.md (technology decisions)

**Tests**: NOT REQUESTED - Tests are optional in spec.md, so test tasks are NOT included in this breakdown. Focus is on implementation tasks only.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

Laravel single-project structure (from plan.md):
- Models: `app/Models/`
- Controllers: `app/Http/Controllers/`
- Middleware: `app/Http/Middleware/`
- Views: `resources/views/`
- Migrations: `database/migrations/`
- Frontend: `resources/css/` and `resources/js/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Initialize Laravel project with required dependencies

- [X] T001 Create Laravel 11.x project: `composer create-project laravel/laravel kmsystem`
- [X] T002 [P] Configure environment file (.env) with database credentials (PostgreSQL 16)
- [X] T003 [P] Install Pest testing framework: `composer require pestphp/pest pestphp/pest-plugin-laravel --dev` (SKIPPED - PHP 8.5.1 compatibility issue)
- [X] T004 [P] Install Laravel Sanctum: `composer require laravel/sanctum`
- [X] T005 [P] Install Laravel Scout: `composer require laravel/scout`
- [X] T006 [P] Install HTML Purifier: `composer require mews/purifier`
- [X] T007 [P] Install Laravel Media Library: `composer require spatie/laravel-medialibrary`
- [X] T008 [P] Install Adjacency List package: `composer require staudenmeir/laravel-adjacency-list`
- [X] T009 [P] Install Tailwind CSS: `npm install -D tailwindcss postcss autoprefixer && npx tailwindcss init`
- [X] T010 [P] Install Alpine.js and HTMX: `npm install alpinejs htmx.org`
- [X] T011 [P] Configure Tailwind in vite.config.js and resources/css/app.css
- [X] T012 [P] Install Laravel Pint (code style): `composer require laravel/pint --dev`
- [X] T013 [P] Install Larastan (static analysis): `composer require larastan/larastan --dev`
- [X] T014 Create .gitignore with PHP/Laravel patterns (vendor/, node_modules/, .env, storage/*, public/storage)
- [X] T015 Run database migrations to verify PostgreSQL connection: `php artisan migrate`

**Checkpoint**: Laravel project initialized with all dependencies, database connected

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Database Schema & Models

- [ ] T016 Create users table migration in database/migrations/YYYY_MM_DD_create_users_table.php
- [ ] T017 Create categories table migration in database/migrations/YYYY_MM_DD_create_categories_table.php
- [ ] T018 Create articles table migration in database/migrations/YYYY_MM_DD_create_articles_table.php
- [ ] T019 Create article_versions table migration in database/migrations/YYYY_MM_DD_create_article_versions_table.php
- [ ] T020 Create tags table migration in database/migrations/YYYY_MM_DD_create_tags_table.php
- [ ] T021 Create article_tags pivot table migration in database/migrations/YYYY_MM_DD_create_article_tags_table.php
- [ ] T022 Create attachments table migration in database/migrations/YYYY_MM_DD_create_attachments_table.php
- [ ] T023 Create bookmarks table migration in database/migrations/YYYY_MM_DD_create_bookmarks_table.php
- [ ] T024 Create feedback table migration in database/migrations/YYYY_MM_DD_create_feedback_table.php
- [ ] T025 Create activity_logs table migration in database/migrations/YYYY_MM_DD_create_activity_logs_table.php
- [ ] T026 Run migrations to create all database tables: `php artisan migrate`

### Base Models

- [ ] T027 [P] Create User model in app/Models/User.php with role enum (admin, editor, contributor, viewer)
- [ ] T028 [P] Create Category model in app/Models/Category.php with HasRecursiveRelationships trait
- [ ] T029 [P] Create Article model in app/Models/Article.php with status enum (draft, published)
- [ ] T030 [P] Create ArticleVersion model in app/Models/ArticleVersion.php
- [ ] T031 [P] Create Tag model in app/Models/Tag.php
- [ ] T032 [P] Create Attachment model in app/Models/Attachment.php
- [ ] T033 [P] Create Bookmark model in app/Models/Bookmark.php
- [ ] T034 [P] Create Feedback model in app/Models/Feedback.php
- [ ] T035 [P] Create ActivityLog model in app/Models/ActivityLog.php

### Authentication & Authorization

- [ ] T036 Publish Sanctum configuration: `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
- [ ] T037 Create ArticlePolicy in app/Policies/ArticlePolicy.php (view, create, update, publish, delete methods)
- [ ] T038 [P] Create CategoryPolicy in app/Policies/CategoryPolicy.php (viewAny, create, update, delete methods)
- [ ] T039 [P] Create UserPolicy in app/Policies/UserPolicy.php (viewAny, create, update, delete methods)
- [ ] T040 Register policies in app/Providers/AuthServiceProvider.php
- [ ] T041 Create CheckRole middleware in app/Http/Middleware/CheckRole.php for role-based access control
- [ ] T042 Create LogActivity middleware in app/Http/Middleware/LogActivity.php for audit logging
- [ ] T043 Register middlewares in app/Http/Kernel.php

### Factories & Seeders

- [ ] T044 [P] Create UserFactory in database/factories/UserFactory.php with all 4 roles
- [ ] T045 [P] Create CategoryFactory in database/factories/CategoryFactory.php
- [ ] T046 [P] Create ArticleFactory in database/factories/ArticleFactory.php
- [ ] T047 [P] Create TagFactory in database/factories/TagFactory.php
- [ ] T048 Create UserSeeder in database/seeders/UserSeeder.php (create admin, editor, contributor, viewer users)
- [ ] T049 [P] Create CategorySeeder in database/seeders/CategorySeeder.php (sample hierarchical categories)
- [ ] T050 Update DatabaseSeeder in database/seeders/DatabaseSeeder.php to call UserSeeder and CategorySeeder
- [ ] T051 Run seeders to populate test data: `php artisan db:seed`

### Layout & Base Views

- [ ] T052 Create main layout in resources/views/layouts/app.blade.php with Tailwind CSS
- [ ] T053 [P] Create admin layout in resources/views/layouts/admin.blade.php
- [ ] T054 [P] Create homepage view in resources/views/home.blade.php with search bar and recent articles
- [ ] T055 Configure routes in routes/web.php (auth routes, article routes, search routes)

**Checkpoint**: Foundation ready - database tables created, models defined, auth configured, user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Article Discovery & Reading (Priority: P1) 🎯 MVP

**Goal**: Enable users to search for articles, browse by category, read article content with attachments, and provide feedback

**Independent Test**: Create sample articles via seeder, visit homepage, search using keywords, filter by category/tags, click on article to read content, verify related articles appear, click feedback button

### Implementation for User Story 1

- [ ] T056 [P] [US1] Create SearchService in app/Services/SearchService.php (PostgreSQL full-text search implementation)
- [ ] T057 [P] [US1] Add search_vector column to articles table via migration for tsvector indexing
- [ ] T058 [P] [US1] Create GIN index on search_vector column in articles table migration
- [ ] T059 [US1] Update Article model boot method to auto-update search_vector on create/update
- [ ] T060 [US1] Create SearchController in app/Http/Controllers/SearchController.php (index, search methods)
- [ ] T061 [P] [US1] Create search results view in resources/views/search/results.blade.php with filters
- [ ] T062 [P] [US1] Create ArticleController@index in app/Http/Controllers/ArticleController.php (list articles)
- [ ] T063 [US1] Create ArticleController@show method for displaying single article with related articles
- [ ] T064 [P] [US1] Create article index view in resources/views/articles/index.blade.php (article list)
- [ ] T065 [US1] Create article show view in resources/views/articles/show.blade.php (rich text content, attachments, related articles, feedback)
- [ ] T066 [P] [US1] Create CategoryController@index in app/Http/Controllers/CategoryController.php (list categories)
- [ ] T067 [US1] Create CategoryController@show method to display articles in category and subcategories (recursive CTE)
- [ ] T068 [P] [US1] Create category index view in resources/views/categories/index.blade.php (category tree)
- [ ] T069 [P] [US1] Create category show view in resources/views/categories/show.blade.php (articles in category)
- [ ] T070 [US1] Implement related articles logic in Article model (shared categories/tags)
- [ ] T071 [US1] Implement feedback storage in FeedbackController@store (helpful/not helpful)
- [ ] T072 [US1] Add view count increment logic to ArticleController@show method
- [ ] T073 [US1] Add routes for search, articles, and categories in routes/web.php
- [ ] T074 [US1] Style homepage, search results, and article pages with Tailwind CSS
- [ ] T075 [US1] Test complete user journey: search → filter → read article → related articles → feedback

**Checkpoint**: User Story 1 complete - Users can search, browse, read articles with full functionality

---

## Phase 4: User Story 2 - Article Creation & Publishing (Priority: P1) 🎯 MVP

**Goal**: Enable content contributors to create articles with rich text, upload attachments, assign categories/tags, save drafts, publish (editors), and submit for approval (contributors)

**Independent Test**: Login as editor, click "Create Article", fill title, use TinyMCE to add formatted content, upload file attachment, select category, add tags, click "Publish", verify article appears in search

### Implementation for User Story 2

- [ ] T076 [P] [US2] Create StoreArticleRequest in app/Http/Requests/StoreArticleRequest.php (validation rules)
- [ ] T077 [P] [US2] Create UpdateArticleRequest in app/Http/Requests/UpdateArticleRequest.php
- [ ] T078 [US2] Create ArticleController@create method (show create form)
- [ ] T079 [US2] Create ArticleController@store method (save article, sanitize HTML with HTMLPurifier, create version snapshot)
- [ ] T080 [US2] Create ArticleController@edit method (show edit form with existing data)
- [ ] T081 [US2] Create ArticleController@update method (update article, create version snapshot, handle status change)
- [ ] T082 [P] [US2] Create article create view in resources/views/articles/create.blade.php with TinyMCE integration
- [ ] T083 [P] [US2] Create article edit view in resources/views/articles/edit.blade.php
- [ ] T084 [US2] Add TinyMCE CDN script to article create/edit views with image upload endpoint
- [ ] T085 [US2] Implement file upload endpoint in ArticleController@uploadImage for TinyMCE
- [ ] T086 [P] [US2] Create AttachmentController in app/Http/Controllers/AttachmentController.php (store, destroy methods)
- [ ] T087 [US2] Implement attachment upload using Laravel Media Library in AttachmentController@store
- [ ] T088 [US2] Add attachment list display to article create/edit forms
- [ ] T089 [US2] Implement slug auto-generation in Article model (from title, ensure uniqueness)
- [ ] T090 [US2] Implement version history auto-save in Article model boot method (create ArticleVersion on update)
- [ ] T091 [P] [US2] Create ArticleController@versions method to display version history
- [ ] T092 [P] [US2] Create article versions view in resources/views/articles/versions.blade.php
- [ ] T093 [US2] Implement draft vs published logic in ArticleController@store (editors publish directly, contributors save as draft)
- [ ] T094 [US2] Add publish button to article edit view (visible only if user can publish via policy)
- [ ] T095 [US2] Implement ArticleController@publish method for approving contributor drafts
- [ ] T096 [US2] Add routes for article CRUD, attachments, versions in routes/web.php
- [ ] T097 [US2] Apply ArticlePolicy authorization checks to all article controller methods
- [ ] T098 [US2] Style article create/edit forms with Tailwind CSS (responsive, user-friendly)
- [ ] T099 [US2] Test complete user journey: create article → add content → upload attachment → assign category/tags → publish → verify in search

**Checkpoint**: User Story 2 complete - Articles can be created, edited, versioned, and published with full workflow

---

## Phase 5: User Story 3 - Content Organization & Management (Priority: P2)

**Goal**: Enable admins/editors to manage categories (create, edit, hierarchical structure), manage articles (bulk operations, filtering), and manage tags

**Independent Test**: Login as admin, access admin panel, create root category, create subcategory, assign articles to categories, edit article metadata, view article list with filters

### Implementation for User Story 3

- [ ] T100 [P] [US3] Create admin dashboard view in resources/views/admin/dashboard.blade.php with metrics
- [ ] T101 [P] [US3] Create CategoryController@create method (show create category form)
- [ ] T102 [US3] Create CategoryController@store method (save category with parent_id, sort_order)
- [ ] T103 [US3] Create CategoryController@edit method (show edit form)
- [ ] T104 [US3] Create CategoryController@update method (update category, prevent circular references)
- [ ] T105 [US3] Create CategoryController@destroy method (delete category with cascade or prevent if articles exist)
- [ ] T106 [P] [US3] Create category create view in resources/views/admin/categories/create.blade.php
- [ ] T107 [P] [US3] Create category edit view in resources/views/admin/categories/edit.blade.php
- [ ] T108 [P] [US3] Create category management view in resources/views/admin/categories/index.blade.php (tree view with jsTree or drag-drop)
- [ ] T109 [US3] Add circular reference prevention logic to Category model setParentIdAttribute mutator
- [ ] T110 [P] [US3] Create admin articles list view in resources/views/admin/articles/index.blade.php with filters (status, category, author, date)
- [ ] T111 [US3] Create ArticleController@bulkUpdate method for bulk status changes (publish multiple drafts)
- [ ] T112 [P] [US3] Create TagController in app/Http/Controllers/TagController.php (index, store, update, destroy)
- [ ] T113 [P] [US3] Create tag management view in resources/views/admin/tags/index.blade.php
- [ ] T114 [US3] Implement tag normalization (lowercase slugs) in Tag model boot method
- [ ] T115 [US3] Add admin routes in routes/web.php with role:admin middleware
- [ ] T116 [US3] Apply CategoryPolicy and ArticlePolicy authorization to admin actions
- [ ] T117 [US3] Style admin panel with Tailwind CSS or AdminLTE/Tabler template
- [ ] T118 [US3] Test complete admin workflow: create categories → organize articles → manage tags

**Checkpoint**: User Story 3 complete - Full content organization and management capabilities for admins

---

## Phase 6: User Story 5 - Access Control & User Management (Priority: P2)

**Goal**: Enable admins to manage user accounts (create, edit, assign roles, activate/deactivate) and enforce role-based permissions across the system

**Independent Test**: Login as admin, access user management, create users with different roles, login as each role, verify permission enforcement (viewer can't create, contributor needs approval, editor can publish)

### Implementation for User Story 5

- [ ] T119 [P] [US5] Create UserController in app/Http/Controllers/UserController.php (index, create, store, edit, update, destroy)
- [ ] T120 [P] [US5] Create user list view in resources/views/admin/users/index.blade.php
- [ ] T121 [P] [US5] Create user create view in resources/views/admin/users/create.blade.php
- [ ] T122 [P] [US5] Create user edit view in resources/views/admin/users/edit.blade.php
- [ ] T123 [US5] Implement user creation with password hashing (bcrypt) in UserController@store
- [ ] T124 [US5] Implement role assignment logic with validation (admin, editor, contributor, viewer)
- [ ] T125 [US5] Implement user activation/deactivation in UserController@update (is_active toggle)
- [ ] T126 [US5] Add role helper methods to User model (isAdmin, isEditor, isContributor, canEditArticle)
- [ ] T127 [US5] Create authentication views (login, register) in resources/views/auth/
- [ ] T128 [US5] Configure Laravel Breeze or manual auth routes in routes/web.php
- [ ] T129 [US5] Implement login/logout functionality using Laravel Sanctum session authentication
- [ ] T130 [US5] Add permission denied views in resources/views/errors/403.blade.php
- [ ] T131 [US5] Verify ArticlePolicy enforcement (viewers can't create, contributors can't edit others' articles)
- [ ] T132 [US5] Create drafts approval queue view in resources/views/admin/drafts/index.blade.php (for editors)
- [ ] T133 [US5] Implement ArticleController@approveDraft method for editors to approve contributor submissions
- [ ] T134 [US5] Add email notification on draft approval/rejection (optional, use Laravel Mail)
- [ ] T135 [US5] Style user management and auth pages with Tailwind CSS
- [ ] T136 [US5] Test role-based access: viewer (read-only) → contributor (create, needs approval) → editor (publish) → admin (all access)

**Checkpoint**: User Story 5 complete - Full user management and role-based access control implemented

---

## Phase 7: User Story 4 - Personal Knowledge Management (Priority: P2)

**Goal**: Enable users to bookmark articles, view bookmarked articles, and track reading history

**Independent Test**: Login as any user, view article, click bookmark icon, go to profile, see bookmarked articles, verify view count increments on article reads

### Implementation for User Story 4

- [ ] T137 [P] [US4] Create BookmarkController in app/Http/Controllers/BookmarkController.php (store, destroy, index)
- [ ] T138 [US4] Implement bookmark toggle in BookmarkController@store (add if not exists, remove if exists)
- [ ] T139 [P] [US4] Create user profile view in resources/views/profile/index.blade.php
- [ ] T140 [P] [US4] Create bookmarks list view in resources/views/profile/bookmarks.blade.php
- [ ] T141 [US4] Add bookmark icon to article show view with HTMX or Alpine.js toggle functionality
- [ ] T142 [US4] Implement bookmark relationship in User model (hasMany Bookmark)
- [ ] T143 [US4] Display reading history in profile (articles with view count > 0 by this user)
- [ ] T144 [US4] Add bookmark routes in routes/web.php (require authentication)
- [ ] T145 [US4] Style profile and bookmarks pages with Tailwind CSS
- [ ] T146 [US4] Test bookmark workflow: view article → bookmark → view bookmarks → unbookmark

**Checkpoint**: User Story 4 complete - Personal knowledge management features for all users

---

## Phase 8: User Story 6 - Analytics & Insights (Priority: P3)

**Goal**: Enable admins/editors to view analytics dashboard with metrics (total articles, views, user activity), top articles by views, feedback ratios, and activity logs

**Independent Test**: Login as admin, access analytics dashboard, see metrics widgets, view top articles list sorted by views, see feedback helpful/not helpful ratios, view activity logs with filtering

### Implementation for User Story 6

- [ ] T147 [P] [US6] Create AnalyticsController in app/Http/Controllers/AnalyticsController.php (dashboard, topArticles, activityLogs)
- [ ] T148 [P] [US6] Create AnalyticsService in app/Services/AnalyticsService.php (calculate metrics, query aggregations)
- [ ] T149 [US6] Implement AnalyticsService@getMetrics method (total articles, total views, active users, articles this month)
- [ ] T150 [US6] Implement AnalyticsService@getTopArticles method (order by view_count, include percentage change)
- [ ] T151 [US6] Implement AnalyticsService@getFeedbackStats method (helpful ratio per article)
- [ ] T152 [US6] Implement AnalyticsService@getActivityLogs method with filtering (user, action type, date range)
- [ ] T153 [P] [US6] Create analytics dashboard view in resources/views/admin/analytics/dashboard.blade.php with ApexCharts
- [ ] T154 [P] [US6] Create top articles view in resources/views/admin/analytics/top-articles.blade.php
- [ ] T155 [P] [US6] Create activity logs view in resources/views/admin/analytics/activity-logs.blade.php with filters
- [ ] T156 [US6] Add ApexCharts CDN to analytics views for data visualization
- [ ] T157 [US6] Implement activity logging in LogActivity middleware (log create, update, delete, publish actions)
- [ ] T158 [US6] Add analytics routes in routes/web.php (require editor or admin role)
- [ ] T159 [US6] Style analytics dashboard with charts, cards, and responsive layout using Tailwind CSS
- [ ] T160 [US6] Test analytics workflow: generate activity → view dashboard → see metrics → filter logs

**Checkpoint**: User Story 6 complete - Full analytics and insights for admins and editors

---

## Phase 9: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories, performance optimization, security hardening, and final validation

- [ ] T161 [P] Add breadcrumb navigation component to all pages in resources/views/components/breadcrumbs.blade.php
- [ ] T162 [P] Implement pagination for article lists, search results, and admin tables (Tailwind pagination)
- [ ] T163 [P] Add flash messages component for success/error notifications in resources/views/components/flash-messages.blade.php
- [ ] T164 [P] Optimize database queries: add eager loading to prevent N+1 queries (Article::with('category', 'author', 'tags'))
- [ ] T165 [P] Add database query caching for expensive operations (category tree, popular articles) using Redis
- [ ] T166 [P] Implement route caching: `php artisan route:cache`
- [ ] T167 [P] Implement view caching: `php artisan view:cache`
- [ ] T168 [P] Configure OPcache in php.ini for production performance
- [ ] T169 [P] Add HTTPS redirect middleware in app/Http/Middleware/ForceHttps.php for production
- [ ] T170 [P] Configure secure session cookies in config/session.php (http_only, same_site, secure)
- [ ] T171 [P] Add rate limiting to search endpoint in routes/web.php (throttle:60,1)
- [ ] T172 [P] Validate and sanitize all file uploads (size limits, allowed mime types)
- [ ] T173 [P] Add CSRF token validation verification to all forms (already included via @csrf directive)
- [ ] T174 [P] Run Laravel Pint for code style consistency: `vendor/bin/pint`
- [ ] T175 [P] Run Larastan static analysis: `vendor/bin/phpstan analyse`
- [ ] T176 [P] Create README.md with installation instructions, requirements, and usage guide
- [ ] T177 [P] Create .env.example file with all required environment variables
- [ ] T178 [P] Test responsive design on mobile devices (homepage, search, article view, create article)
- [ ] T179 [P] Verify accessibility (WCAG 2.1): semantic HTML, ARIA labels, keyboard navigation
- [ ] T180 [P] Run `composer audit` to check for vulnerable dependencies
- [ ] T181 Perform end-to-end testing of all 6 user stories in sequence
- [ ] T182 Verify performance targets: page load <2s, search <1s, 100 concurrent users (load testing)
- [ ] T183 Create deployment guide in docs/deployment.md with server setup instructions
- [ ] T184 Final review and sign-off on all user stories

**Checkpoint**: Application ready for production deployment

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phases 3-8)**: All depend on Foundational phase completion
  - User Stories 1 & 2 (P1): MVP - implement first
  - User Stories 3, 5, 4 (P2): Enhance MVP - implement next
  - User Story 6 (P3): Analytics - implement last
  - Stories CAN proceed in parallel if team capacity allows
- **Polish (Phase 9)**: Depends on all desired user stories being complete

### User Story Dependencies

- **US1 - Article Discovery** (P1): Can start after Foundational (Phase 2) - No dependencies on other stories
- **US2 - Article Creation** (P1): Can start after Foundational (Phase 2) - Integrates with US1 (articles must be searchable) but independently testable
- **US3 - Content Organization** (P2): Can start after Foundational (Phase 2) - Uses categories/tags from US1/US2
- **US5 - Access Control** (P2): Can start after Foundational (Phase 2) - Required for US2 approval workflow but can be tested independently
- **US4 - Personal Features** (P2): Can start after US1 complete (bookmarks require articles to exist)
- **US6 - Analytics** (P3): Can start after US1/US2 complete (requires view counts, feedback data)

### Within Each User Story

- Models before services
- Services before controllers
- Controllers before views
- Core implementation before integration
- Story complete before moving to next priority

### Parallel Opportunities

- **Setup Phase**: Tasks T002-T014 (all marked [P]) can run in parallel
- **Foundational Phase**:
  - Models T027-T035 (all marked [P]) can run in parallel
  - Policies T038-T039 (marked [P]) can run in parallel
  - Factories T044-T047 (marked [P]) can run in parallel
- **User Stories**: Once Foundational completes, all user stories can start in parallel (if team capacity allows)
- **Within US1**: SearchService (T056), search_vector column (T057), GIN index (T058) can run in parallel
- **Within US2**: Request classes (T076-T077), views (T082-T083), AttachmentController (T086) can run in parallel
- **Polish Phase**: All tasks marked [P] (T161-T180) can run in parallel

---

## Parallel Example: Foundational Phase Models

```bash
# Launch all model creation tasks together after migrations complete:
Task: "Create User model in app/Models/User.php"
Task: "Create Category model in app/Models/Category.php"
Task: "Create Article model in app/Models/Article.php"
Task: "Create ArticleVersion model in app/Models/ArticleVersion.php"
Task: "Create Tag model in app/Models/Tag.php"
Task: "Create Attachment model in app/Models/Attachment.php"
Task: "Create Bookmark model in app/Models/Bookmark.php"
Task: "Create Feedback model in app/Models/Feedback.php"
Task: "Create ActivityLog model in app/Models/ActivityLog.php"
```

---

## Implementation Strategy

### MVP First (User Stories 1 & 2 Only - Priority P1)

1. Complete Phase 1: Setup (T001-T015)
2. Complete Phase 2: Foundational (T016-T055) - CRITICAL - blocks all stories
3. Complete Phase 3: User Story 1 - Article Discovery (T056-T075)
4. Complete Phase 4: User Story 2 - Article Creation (T076-T099)
5. **STOP and VALIDATE**: Test MVP independently (search, create, read articles)
6. Deploy/demo MVP if ready

**MVP Scope**: 99 tasks total (T001-T099)
**MVP Deliverable**: Functional knowledge base where editors create/publish articles and all users search/read them

### Incremental Delivery (Add P2 & P3 Stories)

1. MVP (US1 + US2) → Test independently → Deploy/Demo
2. Add US3 - Content Organization (T100-T118) → Test → Deploy/Demo
3. Add US5 - Access Control (T119-T136) → Test → Deploy/Demo
4. Add US4 - Personal Features (T137-T146) → Test → Deploy/Demo
5. Add US6 - Analytics (T147-T160) → Test → Deploy/Demo
6. Polish & Cross-Cutting (T161-T184) → Final validation → Production deployment

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup (Phase 1) + Foundational (Phase 2) together
2. Once Foundational is done:
   - Developer A: User Story 1 (Article Discovery)
   - Developer B: User Story 2 (Article Creation)
   - Developer C: User Story 3 (Content Organization)
3. Stories complete and integrate independently
4. Team reconvenes for Polish phase

---

## Task Summary

**Total Tasks**: 184
**Setup Tasks**: 15 (T001-T015)
**Foundational Tasks**: 40 (T016-T055)
**User Story 1 Tasks**: 20 (T056-T075)
**User Story 2 Tasks**: 24 (T076-T099)
**User Story 3 Tasks**: 19 (T100-T118)
**User Story 5 Tasks**: 18 (T119-T136)
**User Story 4 Tasks**: 10 (T137-T146)
**User Story 6 Tasks**: 14 (T147-T160)
**Polish Tasks**: 24 (T161-T184)

**Parallel Tasks**: 62 tasks marked [P] can run in parallel with other tasks in same phase
**MVP Scope**: 99 tasks (Phases 1-4: US1 + US2)
**Full Feature Set**: 184 tasks (all 6 user stories + polish)

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Tests NOT included (not requested in spec.md)
- Verify checkpoints after each phase
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- All file paths use Laravel 11.x conventions (app/, resources/, database/)
- PostgreSQL full-text search implementation (not Elasticsearch initially)
- TinyMCE integration via CDN (not npm package)
- Tailwind CSS for styling (built with Vite)
