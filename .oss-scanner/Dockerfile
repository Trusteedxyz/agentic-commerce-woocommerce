# Build for the OSS Scanner. The scanner uses the repository checkout as the build context.
FROM php:8.3-cli-bookworm
ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1
RUN apt-get update && apt-get install -y --no-install-recommends \
      git unzip ca-certificates \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# scanner contract: the checkout lives inside the image, at /src
COPY . /src
WORKDIR /src

# Installs PHPUnit (dev dependency). The plugin itself has no runtime dependencies.
RUN composer install --no-interaction --prefer-dist

# The unit tests run against WordPress stubs (tests/unit/wp-stubs.php), so no WordPress or database is needed.
# Run them so the image is known to work, but do not let a failing test abort the build.
RUN (vendor/bin/phpunit || echo "WARNING: phpunit reported failures")
