# CliEnhanced
CliEnhanced is a helper module that adds commands to the `bin/magento` list to simplify the development experience.

### Installation
Run `composer require restoreddev/magento-cli-enhanced --dev` and then `bin/magento setup:upgrade`.

### Commands
#### theme:create
The theme create command generates a custom storefront theme in `app/design` based on Magento/blank.
Run the command in the project root and it will ask for a theme name including the namespace e.g. `Restoreddev/test`.
The command will create the theme and generate all the static theme folders.
In addition, it will add an `_extends.less` file in the `css/source` folder.

#### module:create
The module create command creates a new module in `app/code`.
It generates the module folders using the provided namespace and name
then populates the registration file, module XML and composer configuration file.
