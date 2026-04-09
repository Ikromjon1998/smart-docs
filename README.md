# Smart Docs

A real-world NativePHP mobile application for scanning, organizing, and managing documents on Android and iOS.

Built with [NativePHP Mobile](https://nativephp.com), [Laravel 13](https://laravel.com), and [Livewire 4](https://livewire.laravel.com).

## Features

- **Document Scanning** — Scan documents using your device camera with automatic edge detection, perspective correction, and cropping
- **Multi-Page Support** — Scan multiple pages in a single session
- **PDF Conversion** — Convert scanned JPEG images into a single PDF document
- **Native Sharing** — Share documents via the native share sheet (email, messaging, cloud storage, etc.)
- **Document Management** — Browse, search, filter by category, edit details, and delete documents
- **Configurable Scan Options** — Output format (JPEG/PDF), quality, page limits, gallery import, scanner mode
- **Mobile-First UI** — Tailwind CSS interface with safe area support, bottom navigation, and native-feel interactions

## Plugins Used

| Plugin | Description |
|--------|-------------|
| [nativephp-mobile-document-scanner](https://github.com/Ikromjon1998/nativephp-mobile-document-scanner) | Document scanning with VisionKit (iOS) and ML Kit (Android) |
| [nativephp/mobile-share](https://github.com/nicoverbruggen/nativephp-mobile-share) | Native share sheet for sharing files and text |

## Tech Stack

- **Framework:** Laravel 13
- **Mobile Runtime:** NativePHP Mobile v3
- **Frontend:** Livewire 4 + Tailwind CSS v4
- **PDF Generation:** FPDF
- **Database:** SQLite
- **Platforms:** Android, iOS

## Requirements

- PHP 8.3+
- Composer
- Node.js & npm
- Android SDK (for Android builds)
- Xcode (for iOS builds, macOS only)

## Installation

```bash
# Clone the repository
git clone https://github.com/Ikromjon1998/smart-docs.git
cd smart-docs

# Install dependencies
composer install
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build frontend assets
npm run build
```

## Running on a Device

```bash
# Android
php artisan native:run android

# iOS (macOS only)
php artisan native:run ios
```

> **Note:** This app requires a real device or emulator — it will not work with `php artisan serve`.

## Project Structure

```
app/
  Livewire/
    Scanner.php         # Scan documents with configurable options
    DocumentList.php    # Browse and search saved documents
    DocumentDetail.php  # View, edit, share, convert, delete documents
    Settings.php        # App settings and scanner status
  Models/
    Document.php        # Document model (title, category, summary, file_paths, etc.)
  Services/
    PdfConverter.php    # Convert JPEG images to PDF using FPDF
resources/
  views/
    layouts/app.blade.php           # Mobile app layout with bottom navigation
    livewire/scanner.blade.php      # Scanner UI with options panel
    livewire/document-list.blade.php    # Document list with search/filter
    livewire/document-detail.blade.php  # Document detail with actions
    livewire/settings.blade.php     # Settings page
```

## How It Works

1. **Scan** — Tap the Scan button to open the native document scanner. Configure output format, quality, page limits, and gallery import before scanning.
2. **Save** — Scanned documents are automatically saved to the app's SQLite database with file paths, page count, format, and file size.
3. **Organize** — Edit document titles, categories, and add summaries. Search and filter your document library.
4. **Convert** — Convert JPEG scans to PDF with a single tap (with confirmation).
5. **Share** — Use the native share sheet to send documents via email, messaging apps, or save to cloud storage like Google Drive or Files.

## Contributing

Contributions are welcome! Please feel free to submit a pull request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add my feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
