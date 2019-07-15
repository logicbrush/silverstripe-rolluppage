# silverstripe-rolluppage

A fairly simple module for the SilverStripe CMS which rolls up the contents of child pages for display on a single page.

## Why?

Have you ever found yourself trying to decide if you should have a single page of content with multiple headings, or a series of pages in a heirarchy. This module lets you set up your content as distinct pages in the page tree, and then flip between structures using a single radio button.

## Installation

```sh
composer require "logicbrush/silverstripe-rolluppage"
```

## Usage

This module defines a new page class of type `Logicbrush\RollupPage\Model\RollupPage`.  When you create an instance of this page type, all of the children of that instance will have their content displayed inline -- "rolled up" -- with the output of the page.

### Options

#### Rollup Pages

- Content/Main Content/**Rollup Display**: When set to *"Show Full Content"*, the content of the child pages will be embedded into the Rollup Page's content. If set to *"Show Links Only"*, a list of links to the child pages will be displayed instead.

#### Children of Rollup Pages

- Settings/Behavior/Visibility/**Never Rollup**: If checked, this page will never be rolled up into the parent `RollupPage`.
