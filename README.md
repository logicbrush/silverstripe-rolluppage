# silverstripe-rolluppage

A fairly simple module for the SilverStripe CMS which rolls up the contents of
child pages for display on a single page.

## Why?

Have you ever found yourself trying to decide if you should have a single page
of content with multiple headings, or a series of pages in a heirarchy. This
module lets you set up your content as distinct pages in the page tree, and then
flip between structures using a single radio button.

## Installation

```sh
composer require "logicbrush/silverstripe-rolluppage"
```

## Usage

This module defines a new page class of type
`Logicbrush\RollupPage\Model\RollupPage`.  When you create an instance of this
page type, all of the children of that instance will have their content
displayed inline -- "rolled up" -- with the output of the page.

### Options

#### Rollup Pages

- Content/Main Content/**Rollup Display**: 

  When set to *"Show children inline"*, the content of the child pages will be
  embedded into the Rollup Page's content one after another. 
  
  If set to *"Show children in tabs"*, a set of tabs will be displayed that
  allows the visitor to switch back and forth between the content of the child
  pages.

  If set to *"Show children as links"*, a list of links to the child pages will be
  displayed.

#### Children of Rollup Pages

- Settings/Behavior/Visibility/**Never Rollup**: 

  If checked, this will prevent the page from ever being rolled up into a parent
  `RollupPage`.
