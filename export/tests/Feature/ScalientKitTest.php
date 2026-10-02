<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Facades\FormSubmission;
use Statamic\Facades\Stache;
use Tests\TestCase;

class ScalientKitTest extends TestCase
{
    public function test_homepage_renders_scalient_content_and_native_navigation(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertOk()->assertSeeText('Fueling the next generation of brands.');
        $response->assertSee('href="/services"', false);
        $response->assertSee('mailto:hello@example.com', false);
        $response->assertDontSee('webflow.js');
    }

    public function test_service_detail_renders_from_a_collection_entry(): void
    {
        $this->withoutVite();

        $response = $this->get('/services/content-marketing');

        $response->assertOk()->assertSeeText('Content Marketing');
        $response->assertSeeText('Content that earns attention and keeps it.');
    }

    public function test_contact_page_uses_a_real_form_with_validation_fields(): void
    {
        $this->withoutVite();

        $response = $this->get('/contact');

        $response->assertOk()->assertSee('method="POST"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="_token"', false);
    }

    #[DataProvider('pageRoutes')]
    public function test_each_content_route_renders_with_a_title(string $route, string $title): void
    {
        $this->withoutVite();

        $this->get($route)->assertOk()->assertSeeText($title);
    }

    /** @return array<string, array{string, string}> */
    public static function pageRoutes(): array
    {
        return [
            'about' => ['/about', 'Company'],
            'services' => ['/services', 'Services'],
            'work' => ['/work', 'Work'],
            'pricing' => ['/pricing', 'Pricing'],
            'blog' => ['/blog', 'Insights'],
            'career' => ['/career', 'Careers'],
            'contact' => ['/contact', 'Contact'],
            'content marketing' => ['/services/content-marketing', 'Content Marketing'],
            'marketing automation' => ['/services/marketing-automation', 'Marketing Automation'],
            'market magnet' => ['/work/market-magnet', 'Market Magnet'],
            'growth engine' => ['/work/growth-engine', 'Growth Engine'],
            'metrics article' => ['/blog/metrics-that-matter', 'The metrics that actually matter'],
            'audience article' => ['/blog/audiences-that-convert', 'Build audiences that convert'],
        ];
    }

    public function test_invalid_quote_is_rejected_by_server_validation(): void
    {
        $this->postJson('/!/forms/quote', [
            'email' => 'invalid-email',
            'message' => 'short',
        ])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_valid_quote_is_stored_and_honeypot_submission_is_discarded(): void
    {
        Mail::fake();
        $submissionDirectory = sys_get_temp_dir().'/scalient-forms-'.bin2hex(random_bytes(8));
        Stache::store('form-submissions')->directory($submissionDirectory.'/');

        try {
            $details = [
                'name' => 'Scalient Lead',
                'email' => 'lead@example.com',
                'message' => 'We are interested in a growth strategy workshop.',
            ];

            $this->postJson('/!/forms/quote', $details)
                ->assertOk()->assertJson(['success' => true, 'submission_created' => true]);

            $submissions = FormSubmission::whereForm('quote');
            $this->assertCount(1, $submissions);
            $this->assertSame($details['email'], $submissions->first()->get('email'));
            $this->assertFileExists($submissions->first()->path());

            $this->postJson('/!/forms/quote', [...$details, 'website' => 'spam.example.com'])
                ->assertOk()->assertJson(['success' => true, 'submission_created' => false]);

            $this->assertCount(1, FormSubmission::whereForm('quote'));
            Mail::assertNothingSent();
        } finally {
            File::deleteDirectory($submissionDirectory);
        }
    }

    public function test_missing_page_shows_the_custom_not_found_page(): void
    {
        $this->withoutVite();

        $this->get('/missing-scalient-page')->assertNotFound()->assertSeeText('404');
    }
}
