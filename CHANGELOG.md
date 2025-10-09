# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [1.7.0](https://github.com/squarecandy/squarecandy-common/compare/v1.6.3...v1.7.0) (2025-10-09)


### Features

* use the branch slug in grunt bump ([b77a589](https://github.com/squarecandy/squarecandy-common/commit/b77a589cbc25ccbb4b86c3e9fe685a78b3147cd4))

### [1.6.3](https://github.com/squarecandy/squarecandy-common/compare/v1.6.2...v1.6.3) (2025-07-10)


### Bug Fixes

* undisable button even if form is closed then reopened ([122ac32](https://github.com/squarecandy/squarecandy-common/commit/122ac32e551d73b1b5acb9de6f415cd28fa99b03))

### [1.6.2](https://github.com/squarecandy/squarecandy-common/compare/v1.6.1...v1.6.2) (2025-07-10)


### Bug Fixes

* add sqcdy-views2 body class ([c5cb890](https://github.com/squarecandy/squarecandy-common/commit/c5cb8900a37f2866fd0f2c07c426a4e6ec0e5b00))
* GiveWP workaround to allow zero campaign goals ([7e5b735](https://github.com/squarecandy/squarecandy-common/commit/7e5b735a13d500f0d122b37162a2e238524e328e))

### [1.6.1](https://github.com/squarecandy/squarecandy-common/compare/v1.6.0...v1.6.1) (2025-06-20)


### Bug Fixes

* disable speculative loading rules introduced in WP 6.5 ([dc87d5c](https://github.com/squarecandy/squarecandy-common/commit/dc87d5c7904948476f12e7357085e7eaa76107ef))
* remove contain-intrinsic-size output from WP Core ([8530ceb](https://github.com/squarecandy/squarecandy-common/commit/8530cebae2ae16d071c4f736a015a1a1d70f1fa7))

### [1.5.7](https://github.com/squarecandy/squarecandy-common/compare/v1.5.6...v1.5.7) (2025-05-29)


### Bug Fixes

* wrap prev/next text in span, add filter ([2a14ace](https://github.com/squarecandy/squarecandy-common/commit/2a14ace9a0aff020124629b8d2d34605cd579ed6))

## [1.6.0](https://github.com/squarecandy/squarecandy-common/compare/v1.5.6...v1.6.0) (2025-06-10)


### Features

* thwart unnecessary calls to api.wordpress.org/core ([a82b8b2](https://github.com/squarecandy/squarecandy-common/commit/a82b8b20644f696db030844628a8a9391f8dfd19))

### [1.5.7](https://github.com/squarecandy/squarecandy-common/compare/v1.5.4...v1.5.7) (2025-05-29)


### Bug Fixes

* better comments and logging ([1fdd30b](https://github.com/squarecandy/squarecandy-common/commit/1fdd30b38e162cdc41a61876f95a6e53e0cdfc9b))
* simplify the GiveWP js check (date instead of nonce) ([1b64d13](https://github.com/squarecandy/squarecandy-common/commit/1b64d13ed150a12ea6781f11f8ac71b62956100d))
* wrap prev/next text in span, add filter ([2a14ace](https://github.com/squarecandy/squarecandy-common/commit/2a14ace9a0aff020124629b8d2d34605cd579ed6))

### [1.5.6](https://github.com/squarecandy/squarecandy-common/compare/v1.5.5...v1.5.6) (2025-05-06)


### Bug Fixes

* better comments and logging ([1fdd30b](https://github.com/squarecandy/squarecandy-common/commit/1fdd30b38e162cdc41a61876f95a6e53e0cdfc9b))

### [1.5.5](https://github.com/squarecandy/squarecandy-common/compare/v1.5.4...v1.5.5) (2025-05-06)


### Bug Fixes

* simplify the GiveWP js check (date instead of nonce) ([1b64d13](https://github.com/squarecandy/squarecandy-common/commit/1b64d13ed150a12ea6781f11f8ac71b62956100d))

### [1.5.4](https://github.com/squarecandy/squarecandy-common/compare/v1.5.3...v1.5.4) (2025-04-30)

### [1.5.3](https://github.com/squarecandy/squarecandy-common/compare/v1.5.2...v1.5.3) (2025-04-29)


### Bug Fixes

* add squarecandy_add_options_page function ([1b86d89](https://github.com/squarecandy/squarecandy-common/commit/1b86d893b754a953b9653c72df93ee2ed97f330d))
* add subpage option for squarecandy_add_options_page ([51cbaef](https://github.com/squarecandy/squarecandy-common/commit/51cbaef82b6a223be0a1bcca2dc326e302fd1998))
* back out image sizes fix ([a0f6db8](https://github.com/squarecandy/squarecandy-common/commit/a0f6db8f4cf48695b1c3624155e16f366735f369))

### [1.5.2] (2025-03-26)

### Bug Fixes

* fix WP 6.7.1 image sizes auto bug

### [1.5.1] (2025-02-14)

### Bug Fixes

* GiveWP log any nonce errors in addition to showing the user the error;
* GiveWP add additional nonce fill attempt after 5 seconds;
* GiveWP nonce - remove jquery dependency

### [1.5.0] (2025-01-25)

### Features

* Pagination Function squarecandy_pagination to output consistent pagers across plugins and themes

### [1.4.1] (2025-01-06)

### Bug Fixes

* fix count warning

### [1.4.0] (2024-12-19)

### Features

* GiveWP change the donation status to complete if the WC Order status is "Processing"
* GiveWP add WP nonce requirement to confirm javascript is in use.
* Add squarecandy_slide_header_images function

### Bug Fixes

* update stylelint rules
