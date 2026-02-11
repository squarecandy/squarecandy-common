# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [2.0.3](https://github.com/squarecandy/squarecandy-common/compare/v2.0.2...v2.0.3) (2026-02-11)


### Bug Fixes

* additional cssnano overrides for background-color:initial ([095f60d](https://github.com/squarecandy/squarecandy-common/commit/095f60d0c6660296b810d0bb6c8a23568eb8cd46))
* hide the depreciation notices about [@import](https://github.com/import) in sass for now. ([1303ba8](https://github.com/squarecandy/squarecandy-common/commit/1303ba8c0bf03959d37cd85f01eab3d688b8df7e))
* remove no longer used package & debug logging ([3f88fe8](https://github.com/squarecandy/squarecandy-common/commit/3f88fe86ea7b4bf39d6cf959b8b003ba0f16ec6d))

### [2.0.2](https://github.com/squarecandy/squarecandy-common/compare/v2.0.1...v2.0.2) (2026-02-08)


### Bug Fixes

* add mixitup ([5744fd3](https://github.com/squarecandy/squarecandy-common/commit/5744fd3d7b4911e2900d898e299e913b8a7e32f1))

### [2.0.1](https://github.com/squarecandy/squarecandy-common/compare/v2.0.0...v2.0.1) (2026-02-08)

## [2.0.0](https://github.com/squarecandy/squarecandy-common/compare/v1.9.0...v2.0.0) (2026-02-08)


### Features

* Major updates to CI / build tools ([7ae9942](https://github.com/squarecandy/squarecandy-common/commit/7ae99427bd8ffa3b7d953ca5256f499cdf58b565))
* new php linting and fixing approach ([7264de5](https://github.com/squarecandy/squarecandy-common/commit/7264de546c3aaeb8136d951ff26fd82eadafe05e))


### Bug Fixes

* add check so we don't copy removed files if they've been deleted ([b3be445](https://github.com/squarecandy/squarecandy-common/commit/b3be445e379b9c0e36e09a5060c93842c3a6e43b))
* add comments ([34581cc](https://github.com/squarecandy/squarecandy-common/commit/34581cc7ec25067e9f40495c1c8132438cf843b2))
* add Gruntfile copy, add template package.json, composer.json & Gruntfile.js to common directory ([e687014](https://github.com/squarecandy/squarecandy-common/commit/e68701401d2be01d531670943acb232031958511))
* add our fork of phprtflite ([b0d4314](https://github.com/squarecandy/squarecandy-common/commit/b0d431458a9e8b2cc3f67bed5df6934adec6e621))
* add step to check for new files before bump ([504af07](https://github.com/squarecandy/squarecandy-common/commit/504af079e62803a25f798003480fa31c0e9ace14))
* add svgstore, add conditional copy for cycle2, magnific, package.json & composer.json (with version replace for package.json) ([27f6c73](https://github.com/squarecandy/squarecandy-common/commit/27f6c7392853e344ff32307d55d50f7154f650ce))
* back out cssnano calc setting ([81d6a90](https://github.com/squarecandy/squarecandy-common/commit/81d6a90083cbbd24b87bb1f86717a782e3c50603))
* check css subdirectories ([cec29d0](https://github.com/squarecandy/squarecandy-common/commit/cec29d0754fca138d4c6d33f8345f68394691d18))
* cssnano, don't mess with calc ([9765ec4](https://github.com/squarecandy/squarecandy-common/commit/9765ec44cceffa0c1389ec1e26ae049a68fa60ae))
* disable cssnano colormin ([23e879f](https://github.com/squarecandy/squarecandy-common/commit/23e879f7949d8462a36e8404fbdb1117a99abab1))
* disable SEOPress per-post redirections ([d4a77ed](https://github.com/squarecandy/squarecandy-common/commit/d4a77ed564a000ac0effa9d6bb8b9a5c868f1114))
* existing code comments not passing new rules. ([50addb9](https://github.com/squarecandy/squarecandy-common/commit/50addb9c9a7057b096553474817fc32950260814))
* format long line ([cbe04ee](https://github.com/squarecandy/squarecandy-common/commit/cbe04ee9b66d4f664a6dc37325929566c6fc1bd5))
* ignore WordPress capital P rule in URL - wider ignore rule matches old and new rulesets ([3d69e32](https://github.com/squarecandy/squarecandy-common/commit/3d69e3266dd1f059f2881561284894940194d9b3))
* improve whitespace enforcement ([50e4a77](https://github.com/squarecandy/squarecandy-common/commit/50e4a77eae651a026779bfafe70f2a050a629cbb))
* make check for new files include subdirectories, fix gitnewer ([ef58397](https://github.com/squarecandy/squarecandy-common/commit/ef583974d2664751702699da6f64eaf105bf7dd7))
* max-line-length does not work anymore ([d74affd](https://github.com/squarecandy/squarecandy-common/commit/d74affdbaf8b696adf6b39c1e046d79f859b6f8c))
* more phpcs rule tweaks for less legacy changes ([6308329](https://github.com/squarecandy/squarecandy-common/commit/6308329181880ac540eef0da3f5b0c536aaaf023))
* move copy out of the compile process, create `grunt update` ([28a000f](https://github.com/squarecandy/squarecandy-common/commit/28a000fa78ab51870dcae53f3bc335fa1a26a66f))
* proof of concept for grunt settings file ([a5487a7](https://github.com/squarecandy/squarecandy-common/commit/a5487a7f562fe8bc2af63760c223e17931f03f78))
* Redirect GiveWP Donor Single Overview to Legacy Overview ([795f57f](https://github.com/squarecandy/squarecandy-common/commit/795f57fe94f17c333f3d10ac7d346433552a991b))
* remove "starter" dir now that all files are duplicated in "common" ([a38e34d](https://github.com/squarecandy/squarecandy-common/commit/a38e34d266f4b27b312fddaafc50ef49a6db4ca0))
* remove extra logging ([11d9bf2](https://github.com/squarecandy/squarecandy-common/commit/11d9bf23a1d41a7b301e2bb285da7c860ca241f4))
* set some stylelint issues to warnings so they don't stop compiling but we can improve things as time allows ([fd9a368](https://github.com/squarecandy/squarecandy-common/commit/fd9a3681b95225f7794d825b2e7e5681955b257a))
* tweak eslint rules for better legacy matching ([08ebbec](https://github.com/squarecandy/squarecandy-common/commit/08ebbec440db5d1b238d67c4c3458405bb2b7304))
* update github workflow settings ([71b0742](https://github.com/squarecandy/squarecandy-common/commit/71b0742907bfb34cef5558d417048148852ec269))
* update grunt-sass & sass and remove no longer necessary string-replace ([549e4ac](https://github.com/squarecandy/squarecandy-common/commit/549e4aceedc52737478cda95c46e1142d2f479d6))

## [1.9.0](https://github.com/squarecandy/squarecandy-common/compare/v1.8.0...v1.9.0) (2026-01-22)


### Features

* add automated creation of releases on github ([b98121e](https://github.com/squarecandy/squarecandy-common/commit/b98121efb9ad4b8bc1768e042b4b61b6f657a5dd))

## [1.8.0](https://github.com/squarecandy/squarecandy-common/compare/v1.7.1...v1.8.0) (2026-01-22)


### Features

* Update WP Coding Standards to 3.x ([621092b](https://github.com/squarecandy/squarecandy-common/commit/621092bde2625c742aecd01edd6f57736c9d493d))


### Bug Fixes

* depreciated rules ([4a77042](https://github.com/squarecandy/squarecandy-common/commit/4a770420ae91b5f55bd7367adb131473c04bc702))
* generic starting version for starter file ([aaa0b15](https://github.com/squarecandy/squarecandy-common/commit/aaa0b1559c44cb9cdba048777092d8f1e10c471e))
* restore deleted sniffs with updated names ([97a3912](https://github.com/squarecandy/squarecandy-common/commit/97a3912f683c12d0940fe45d35c0b4a395f1b78a))
* restore phpcompatibility check ([d8deb72](https://github.com/squarecandy/squarecandy-common/commit/d8deb722ae61921617fb53bcd069de45a7e7e0ad))
* update the phpcs copy that gets deployed too! ([e4d84cb](https://github.com/squarecandy/squarecandy-common/commit/e4d84cb8cd997a14fc11122c7fcba7fb7f6e28a3))

### [1.7.1](https://github.com/squarecandy/squarecandy-common/compare/v1.7.0...v1.7.1) (2025-11-24)


### Bug Fixes

* add git updater ignore for woocommerce name your price plugin ([e6d3fd6](https://github.com/squarecandy/squarecandy-common/commit/e6d3fd623b82a55d911d9c755f89cd935f401249))
* add is_acf_fontawesome_plugin_active function ([e885efd](https://github.com/squarecandy/squarecandy-common/commit/e885efdf9bfcbea57cde740e57d43e547c4a05e6))

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
