# Changelog

All notable changes to `laravel-invoice` will be documented in this file.

## 1.0.0 - 2026-08-16
### Added
- Initial release.
- Decimal-safe calculation engine for taxes, discounts, and fees.
- Concurrent-safe invoice sequence generation (transaction locks).
- Fluent `InvoiceBuilder` API.
- Payment tracker with partial payment support and automatic status resolution.
- PDF generation interface via `PdfGeneratorInterface` with Dompdf fallback.
- Customizable Blade templates with multi-language (Localization) support.
- REST API controllers with public verification endpoints.
- Webhook notification system via async Jobs (with HMAC SHA-256).
- Full testing suite built on Pest/PHPUnit.
