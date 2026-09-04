---
paths:
  - 'resources/views/pages/**'
---

# Livewire full-page components

## Guest/public pages need an explicit `#[Layout]`
`Route::livewire()` wraps every page component in `config('livewire.component_layout')` (`layouts::app`, the authenticated sidebar shell) by default. A public page rendered for a guest 500s inside `layouts/app/sidebar.blade.php` (it assumes `Auth::user()` exists). Set `#[Layout('layouts::public')]` (`resources/views/layouts/public.blade.php` — just `{{ $slot }}`) on any page component meant to render its own full `<html>` document for guests, e.g. `pages::⚡contact-us`.

## A full-page component's `<body>` must have exactly one root element
Livewire's dev-only `SupportMultipleRootElementDetection` check counts direct element children of `<body>` and throws `MultipleRootElementsDetectedException` if there's more than one. When a page component renders its own full document, everything — including `flux:modal`, `flux:toast.group`, and any other top-level markup — must live *inside* the page's single outer wrapper `<div>`, not as siblings after it. `@fluxScripts`/`<script>`/`<style>` tags are stripped before the count, so those are safe as trailing siblings.
