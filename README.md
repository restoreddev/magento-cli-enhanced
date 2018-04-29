# CliEnhanced
CliEnhanced is a helper module that adds commands to the `bin/magento` list to simplify the development experience.

### Commands
#### theme:create
The theme create command generates a custom storefront theme in `app/design` based on Magento/blank.
Run the command in the project root and it will ask for a theme name including the namespace e.g. `Restoreddev/test`.
The command will create the theme and generate all the static theme folders.
In addition, it will add an `_extends.less` file in the `css/source` folder.
