<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $users = User::whereIn('role', ['admin', 'editor', 'contributor'])->get();

        // Create tags
        $tags = [];
        $tagNames = ['Laravel', 'PHP', 'Database', 'API', 'Security', 'Performance', 'Testing', 'Deployment', 'Frontend', 'Backend'];
        foreach ($tagNames as $name) {
            $tags[] = Tag::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
            ]);
        }

        // Create sample articles
        $articles = [
            [
                'title' => 'Getting Started with the Knowledge Base',
                'content' => "Welcome to our comprehensive knowledge base system!\n\nThis guide will help you navigate through our documentation and find the information you need quickly.\n\n## Key Features\n\n- Full-text search capabilities\n- Hierarchical category organization\n- Tag-based filtering\n- User feedback system\n- Related articles suggestions\n\n## How to Use\n\n1. Use the search bar to find specific topics\n2. Browse by categories for organized content\n3. Filter by tags to narrow down results\n4. Provide feedback to help us improve\n\nIf you have any questions, feel free to explore our other articles or contact support.",
                'excerpt' => 'Learn how to effectively use our knowledge base system to find the information you need.',
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Understanding Database Migrations',
                'content' => "Database migrations are a crucial part of Laravel development.\n\n## What are Migrations?\n\nMigrations are like version control for your database, allowing your team to define and share the application's database schema.\n\n## Creating Migrations\n\nUse the Artisan command:\n```\nphp artisan make:migration create_users_table\n```\n\n## Running Migrations\n\nExecute migrations with:\n```\nphp artisan migrate\n```\n\n## Best Practices\n\n- Always review migration files before running\n- Use descriptive names\n- Test migrations in development first\n- Keep migrations simple and focused",
                'excerpt' => 'A comprehensive guide to understanding and working with Laravel database migrations.',
                'status' => 'published',
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'API Authentication Best Practices',
                'content' => "Securing your API is critical for protecting user data and system integrity.\n\n## Authentication Methods\n\n### Token-Based Authentication\nUse Laravel Sanctum for SPA and mobile authentication.\n\n### OAuth 2.0\nImplement for third-party integrations.\n\n## Security Recommendations\n\n1. Always use HTTPS in production\n2. Implement rate limiting\n3. Validate all input data\n4. Use strong password hashing\n5. Keep dependencies updated\n\n## Common Pitfalls\n\n- Exposing sensitive data in responses\n- Not implementing proper CORS policies\n- Weak token generation\n- Missing input validation\n\nFollow these practices to build secure APIs.",
                'excerpt' => 'Learn the best practices for implementing secure API authentication in your applications.',
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Optimizing Database Queries',
                'content' => "Database performance is crucial for application speed.\n\n## Common Issues\n\n### N+1 Query Problem\nUse eager loading to avoid:\n```php\n// Bad\n\$articles = Article::all();\nforeach(\$articles as \$article) {\n    echo \$article->author->name;\n}\n\n// Good\n\$articles = Article::with('author')->get();\n```\n\n### Missing Indexes\nAdd indexes to frequently queried columns.\n\n### Inefficient Queries\nUse query builder efficiently and avoid SELECT *.\n\n## Tools\n\n- Laravel Debugbar\n- Laravel Telescope\n- Database query logs\n\n## Monitoring\n\nRegularly monitor slow queries and optimize as needed.",
                'excerpt' => 'Tips and techniques for optimizing database queries in Laravel applications.',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Introduction to Full-Text Search',
                'content' => "Full-text search provides powerful search capabilities.\n\n## PostgreSQL Full-Text Search\n\nOur system uses PostgreSQL's native full-text search with tsvector.\n\n## How It Works\n\n1. Text is converted to tsvector format\n2. Search queries use tsquery\n3. Results are ranked by relevance\n\n## Advantages\n\n- Fast search performance\n- Weighted ranking (title > excerpt > content)\n- Language support\n- No external dependencies\n\n## Usage Tips\n\n- Use specific keywords\n- Combine with filters\n- Check related articles\n\nTry searching now to see it in action!",
                'excerpt' => 'Understand how our PostgreSQL-based full-text search system works and how to use it effectively.',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Testing Laravel Applications',
                'content' => "Testing ensures your application works as expected.\n\n## Types of Tests\n\n### Unit Tests\nTest individual components in isolation.\n\n### Feature Tests\nTest complete features and user flows.\n\n### Browser Tests\nUse Laravel Dusk for end-to-end testing.\n\n## Writing Tests\n\n```php\npublic function test_user_can_view_articles()\n{\n    \$article = Article::factory()->create();\n    \n    \$response = \$this->get(route('articles.show', \$article));\n    \n    \$response->assertStatus(200);\n    \$response->assertSee(\$article->title);\n}\n```\n\n## Best Practices\n\n- Write tests first (TDD)\n- Keep tests focused\n- Use factories for test data\n- Test edge cases",
                'excerpt' => 'Learn how to write effective tests for your Laravel applications using PHPUnit and Pest.',
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Deployment Guide',
                'content' => "Deploy your Laravel application to production safely.\n\n## Pre-Deployment Checklist\n\n- [ ] All tests passing\n- [ ] Environment variables configured\n- [ ] Database migrations ready\n- [ ] Assets compiled\n- [ ] Cache cleared\n\n## Deployment Steps\n\n1. Pull latest code\n2. Install dependencies\n3. Run migrations\n4. Clear and rebuild cache\n5. Restart services\n\n## Zero-Downtime Deployment\n\nUse tools like:\n- Laravel Forge\n- Envoyer\n- GitHub Actions\n\n## Post-Deployment\n\n- Monitor error logs\n- Check application health\n- Verify critical features\n\nAlways have a rollback plan!",
                'excerpt' => 'A step-by-step guide to deploying Laravel applications to production environments.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Draft: Upcoming Features',
                'content' => "This is a draft article about features we're planning to add.\n\n## Planned Features\n\n- Advanced search filters\n- Article versioning UI\n- Rich text editor\n- File attachments\n- Email notifications\n\nStay tuned for updates!",
                'excerpt' => 'Preview of exciting features coming soon to the knowledge base.',
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($articles as $articleData) {
            $category = $categories->random();
            $author = $users->random();

            $article = Article::create([
                'title' => $articleData['title'],
                'slug' => \Illuminate\Support\Str::slug($articleData['title']),
                'content' => $articleData['content'],
                'excerpt' => $articleData['excerpt'],
                'category_id' => $category->id,
                'author_id' => $author->id,
                'status' => $articleData['status'],
                'published_at' => $articleData['published_at'],
                'view_count' => rand(10, 500),
            ]);

            // Attach 2-4 random tags
            $article->tags()->attach(
                collect($tags)->random(rand(2, 4))->pluck('id')
            );
        }
    }
}
