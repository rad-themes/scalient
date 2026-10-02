# Fleet for Statamic

Fleet is a logistics and freight starter kit by [Rad Themes](https://statamic.com/creators/rad-themes). It uses native Statamic collections, Antlers, and Tailwind CSS 4. All imagery is bundled locally. No Webflow runtime or paid addon is required.

This is a development preview. A Statamic Marketplace release and hosted demonstration are not available yet.

## Included

- Seven main pages: home, about, services, platform, newsroom, careers, and contact.
- Six service entries, three newsroom articles, and three sample career listings, each with a detail page.
- Nine editable page sections: introduction, features, image/text, services, news, careers, locations, FAQ, and quote form.
- Native navigation, brand/contact globals, managed images, SEO fields, mobile navigation, and a custom 404 page.
- A quote form with server validation, CSRF protection, a honeypot, and submissions stored in the Control Panel.

## Requirements

- Statamic 6.34 or later in the 6.x series, with its required PHP extensions.
- PHP 8.3 or newer within 8.x and Composer 2.
- Node.js 22.12 or newer and npm to rebuild frontend assets.

Compiled frontend assets are included. Statamic licensing is separate from this kit; see [Statamic pricing](https://statamic.com/pricing) for Core and Pro features.

## Installation for repository collaborators

Start with a **new** Statamic project. Installing a starter kit copies content and configuration into that project; do not install it into an established site without a backup and a merge plan.

Clone this private repository somewhere outside the new project, then register its path in the new project's Composer configuration:

```sh
composer config repositories.fleet path /absolute/path/to/statamic-fleet
php please starter-kit:install rad-themes/statamic-fleet --local --without-user --clear-site
php please make:user
php please stache:refresh
npm ci
npm run build
php artisan serve
```

Use your own local checkout path. Composer access to the private repository is required for collaborators. Marketplace installation instructions will be added when the paid product is released.

The kit installs its asset disk configuration in `config/filesystems.php`, a Vite configuration, npm dependencies, and sample content. It includes no accounts, passwords, `.env` file, application key, or form submissions.

## Editing content

Open `/cp` and sign in with the user you created. In **Collections → Pages**, select a page to change its hero and reorder, add, disable, or remove its sections. Services, Newsroom, and Careers are separate collections, with their routes mounted beneath the appropriate listing page.

Use **Globals → Brand and contact** for the site name, contact information, footer text, and calls to action. Use **Navigation → Main navigation** to manage menu links. Use **Assets** to replace images. Each content blueprint includes SEO title, description, and social-image fields.

## Quote requests

The form stores submissions under **Forms → Quote requests** and `storage/forms/quote`. Email delivery is not configured. Configure your mail transport in `.env` and add recipients in Statamic's form email settings if email notifications are needed. Test delivery on your own server before launch.

The platform page is an editable marketing page. It does not connect to shipment tracking software. Career applications use email links; there is no applicant tracking integration.

## Customization and launch

Edit design tokens in `resources/css/site.css`, interactions in `resources/js/site.js`, and templates in `resources/views`. Run `npm run dev` while editing or `npm run build` for a production build. The kit installs as editable project files; future releases do not automatically overwrite your customizations.

Before launching, replace all sample contact details, locations, roles, article copy, and service claims; configure `APP_URL`, mail, backups, HTTPS, and your own privacy information. Review the supplied imagery and replace it as required by your project. Keep `APP_DEBUG=false` in production.

## Checks

```sh
php artisan test --compact tests/Feature/FleetKitTest.php
npm run build
```

The tests cover the supplied routes, native form fields, invalid input, stored requests, the honeypot, brand globals, and the 404 page.

## Credits and support

Fleet adapts the authorized Fleet source design by **Flowit Supply** into native Statamic content models and new Antlers/Tailwind templates. See [CREDITS.md](CREDITS.md) for source provenance. Demo articles and role descriptions are sample content.

For repository development issues, use [GitHub Issues](https://github.com/rad-themes/statamic-fleet/issues). Marketplace buyer support information will be provided with the release.
