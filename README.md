# silverstripe-rolluppage

A module for the SilverStripe CMS which rolls up the contents of child pages for display on a single page.

## Installation

```sh
composer require "logicbrush/silverstripe-rolluppage"
```

## Usage

This module defines a new page class of type `Logicbrush\RollupPage\Model\RollupPage`.  When you create an instance of this page type, all of the children of that instance will have their content displayed inline with the page.

### Options

- Show Links Only: When set to "Show Full Content", the content of the child pages will be embedded into the Rollup Page's content. If set to "Show Links Only", a list of links to the child pages will be displayed instead.
