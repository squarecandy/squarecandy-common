# squarecandy-common

Common files shared between themes and plugins. Let's track and edit them in one place!

*Note: do not run `npm install` or `composer install` on this repo! Copy the files to your project repo and do the install there.*

---

## file structure

Files in the `common` directory should be copied and maintained to be identical on every Square Candy WordPress theme and plugin project. These define our common rules for linting and automated version updating. These files should not be edited to be unique in any individual theme/plugin repository. Instead, use disable/ignore statements in your code for special circumstances, and propose changes to the global rule sets if you think there is a standard that should change globally.

Files in the `plugin` and `theme` directory should be copied - as appropirate - once in the intial setup phase of each new project. Ideally, new projects should use the standard file structure found in `squarecandy-plugin-starter`, `theoryone-child-theme-starter`, etc - which would allow you to use `.versionrc` without modification. However, some legacy code converstions especially may need a unique version of these files, so best not to force them to be overwritten when you run `grunt preflight`.

Files in the `starter` directory are for reference. You can manually copy and modify these files or just use them as a reference point. Each project's needs are going to be unique enough that we can't standardize these globally... however, we should strive to maintain as much consistency as possible - espeically in the linting features.

## upgrade notes - Jan 2026

### Recommended Node Version

New systems work best with node 24 / npm 11

```bash
brew install nvm
nvm install 24
nvm use 24
```

### Major dependency upgrades

* eslint-plugin 5 → 24
* scripts 23 → 31
* stylelint-config 20 → 23
* autoprefixer 9 → 10
* grunt 1.5 → 1.6
* grunt-eslint 22 → 26
* grunt-modernizr 3 → 5
* stylelint 14 → 16 with all related configs/systems updated
* terser 4→5

### npm config

The `.npmrc` file was added to help cleanup unavoidable "bad engine" warnings in the output

### ESLint config file upgrade

The new versions of eslint require a new config file format, provided in the new file `eslint.config.js`

The old files (`.eslintignore` and `.eslintrc`) should be deleted in all projects when this update is implemented

### Browser List Updates

The settings for supported browsers have been moved out of `package.json` and into `.browserslistrc`

The list of browsers has also been tweaked to match our client needs and geography and agency browser support policies better:

* Goes slightly further back by default ("last 7 versions")
* Sets support for older versions of Safari and iOS (users tend to get locked into older versions and can't update due to hardware or major version OS issues)
* Explicity opts out of support for browsers above 0.5% globally, but not popular in North America: KaiOS, Opera Mini, Opera Mobile, Opera, QQ Browser, UC Browser

### Simplified package.json

Removed project specific metadata from the `package.json` file. We're only using npm for development purposes. The info such as name, description, author, licence, etc. is already duplicated in other places in our WP themes and plugins.

### Removed Dependencies

* `pixrem` has been removed now that 'rem' units have very good support and IE support requirement is gone.
* `prettier` library had a lot of overlap and conflicts with the newest versions of `eslint`. It's been removed in favor of just using `eslint`. The configuration has been setup to minimize changes to legacy Square Candy custom js files.

### Version Pinning

* Changed from `^` (caret) to exact versions in `package.json` for better reproducibility. This allows running `npm update` to grab new versions of squarecandy-common, for example, without inadvertently breaking other build tools with complex dependency relationships.
* Security concerns are minimal since we don't deploy `node_modules`
* We should update the entire system more frequently with this pattern. New fully compatible sets of exact version numbers should be setup in `squarecandy-common` and then deployed to all project so everything is identical.

## upgrade notes - May 2022

The following are notes on non-standard upgrade paths that are required for specific node packages we have used in the past.

### stylelint-config-rational-order

`stylelint-config-rational-order` is sadly abandoned and dependent on a lot of outdated packages. There does not seem to be a viable comparable pacakge available. The best solution seems to be to just remove the package completely, and just define and maintain our [own custom list of properties and the order they should go in](https://github.com/squarecandy/squarecandy-common/blob/main/common/.stylelintrc#L26). This initial list was just copied over from the existing lists in the `stylelint-config-rational-order` package. It seems to have minimal impact on our existing code bases. A few items do seem to order differently based on this new system, but it's minimal. From this point forward I think we should attempt to never adjust this existing order. However if there are new properties that are not on this list that you would like to add in a logical place within it, please create a pull request.

### grunt-postcss

The main `grunt-postcss` pacakge is abandoned. We are now using this fork: `@lodder/grunt-postcss`

### sass

The `node-sass` package - while still technically receiving maintenance/security updates - is basically abandoned and the whole Sass community is focused on Dart Sass. We are migrating any existing projects that used `node-sass` to use the `sass` (which is the npm slug for the Dart Sass package).

One unfortunate side-effect of this change is that `grunt-sass` when combined with Dart Sass seems to create css map files with absolute local file URLs. Relative URLs would be much better. We are still looking a solution for this issue:
https://github.com/sindresorhus/grunt-sass/issues/299

### WordPress packages

All of the most updated wordpress packages are under the `@wordpress` user prefix. For example, we are no longer using `stylelint-config-wordpress` and should instead use `@wordpress/stylelint-config`.

### 3rd party libraries

As much as possible we should include 3rd party/vendor js/css/php libraries into our `package.json` system so that they are easy to update. The full packages should be downloaded/updated into `node_modules` which is not under version control, and then just the required files should be moved to `dist/css/vendor` or `dist/js/vendor` using Grunt `copy`.

Note that the original Cycle2 library (by @malsup) used on a lot of our projects is no longer supported by the original author. Use @thecarnie's fork that is actively maintained and jQuery 3.x compatibile instead:
https://github.com/thecarnie/cycle2

## Stylelint / VSCode and node version issues

The current VSCode stylelint plugin is no longer supporting stylelint versions lower than 14.x - this is causing a bit of a cascade of required upgrades. The eventual goal is to upgrade all developers and all repos to use node 16.x, stylelint 14.x and vscode-stylelint 1.x.

While we are in transition and have a mix of projects using the new and legacy stylelint systems, you may need to be able to switch between various versions of node/npm easily. You will need to install `nvm` to accomplish this.

https://tecadmin.net/install-nvm-macos-with-homebrew/

For legacy squarecandy repos, you will need to still use node 14 or lower
`nvm use 14`

For our updated repos, you will need to use node 16
`nvm use 16`

To get the 1.x version of the VSCode Stylelint extension https://github.com/stylelint/vscode-stylelint working again I had to do the following:

in your global vscode `settings.json` file, set `"stylelint.config": null,`
(as per: https://stackoverflow.com/a/69149059/947370 )

in the local repo there should now already be a `.vscode/settings.json` and `.vscode/extensions.json` files.
( You shouldn’t need to add anything new once the repo is updated, but double check as per here: https://stackoverflow.com/a/71817658/947370 )
