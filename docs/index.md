# Cycling Apps Component for Symfony

This bundle provides reusable components and utilities for Symfony applications.

## Repository

https://github.com/EnabelUx/component-bundle

## Components

- [Alert](Alert/alert.md) - A Bootstrap alert component for displaying messages
- [Card](Card/card.md) - A Bootstrap card component for displaying content in a flexible and extensible container
- [Modal](modal.md) - Easily render modals from a dedicated controller
- **Navigation Components** - Bootstrap components for creating responsive navigation:
  - [Navbar](Navigation/navbar.md) - Main navigation container with logo, name, and link configuration
  - [Menu](Navigation/menu.md) - Menu container for organizing navigation items
  - [MenuItem](Navigation/menuItem.md) - Individual navigation links or items
  - [LocaleSwitcher](LocaleSwitcher/localeSwitcher.md) - A locale switcher dropdown for Bootstrap navbar
  - [Tab](Navigation/tab.md) - A Bootstrap nav-tabs/nav-pills component for creating tabbed navigation

## Layouts

- [Bootstrap](Layout/bootstrap.md) - A layout for rendering Bootstrap components with Enabel UX components & Enabel Bootstrap Theme

## Install

Install with composer

```bash
  composer require enabel/ux
```

## Requirements

**Client:**
- Bootstrap 5
- Stimulus

**Server:**
- Symfony 7.3+
- Symfony UX Twig components 2.3+
- Symfony UX Icons components 2.3+

## How to override templates

- Create a `bundles/EnabelUx/` directory in your template directory
- Copy/paste the original file (for example the `templates/locale_switcher.html.twig` to your `templates/bundles/EnabelUx/locale_switcher.html.twig`)
- Update it with your own twig code

## How to override components

- Create a `src/Component/Navigation/LocaleSwitcher.php` file in your application
- Extend from the component of this bundle
- Update your `config/services.yaml`

```yaml
services:
  Enabel\Ux\Component\Navigation\LocaleSwitcher:
    class: App\Component\Navigation\LocaleSwitcher
    tags:
      - { name: 'twig.component', key: 'Enabel:Ux:LocaleSwitcher', template: 'templates/bundles/EnabelUx/locale_switcher.html.twig', expose_public_props: true }
```
