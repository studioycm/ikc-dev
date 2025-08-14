Framework targets: All code must be Laravel 12.21+ and Filament v3.x. Avoid references to Laravel 11/10 or Filament v2. Filament v4 is available but unsuitable for now due to missing plugins.

Environment: Development runs on Windows via Laravel Herd, PHP 8.4 and MySQL. Tools include PhpStorm, TinkerWell and Spatie Ray; assume they’re available.

Best practices: Follow the “Laravel way” (Taylor Otwell, Nuno Maduro) and “Filament way” (Dan Harrin, Povilas Korop). Use modern Eloquent patterns, proper casts, relationship helpers and avoid deprecated syntax.

Version‑aware docs: When checking documentation or samples, confirm they match Laravel 12 and Filament v3. Do not suggest features only present in earlier or later versions.

Resources: Use the private GitHub repos—especially FilamentExamples-Projects by Povilas Korop—for reference patterns and sample code.

Avoid assumptions: Don’t propose code requiring older frameworks, unavailable plugins, or unstable Filament v4 features. Stick to the supported stack described above.
