# European VAT number

**This repository is forked and maintained by Vilkas Group.**

## About

Enables you to enter the intra-community VAT number when creating the address. You must fill in the company field to allow entering the VAT number. This module now uses the new Vies RestAPI endpoint.

## Developing

```
composer dump-autoload --optimize --no-dev --classmap-authoritative
```

## Creating a new release
Remember to:
- Up the version number in the main module file
- Update CHANGELOG

Releases are triggered by tags matching vx.x.x being pushed, for example:
```
git tag v1.0.0
git push --tags
```

## Running tests

Tests require apikey to be defined.

```
composer run-script test
```

## Reporting issues

You can report issues with this module in the main PrestaShop repository. [Click here to report an issue][report-issue]. 

## Contributing

PrestaShop modules are open source extensions to the [PrestaShop e-commerce platform][prestashop]. Everyone is welcome and even encouraged to contribute with their own improvements!

Just make sure to follow our [contribution guidelines][contribution-guidelines].

## License

This module is released under the [Academic Free License 3.0][AFL-3.0] 

[report-issue]: https://github.com/PrestaShop/PrestaShop/issues/new/choose
[prestashop]: https://www.prestashop.com/
[contribution-guidelines]: https://devdocs.prestashop.com/1.7/contribute/contribution-guidelines/project-modules/
[AFL-3.0]: https://opensource.org/licenses/AFL-3.0
