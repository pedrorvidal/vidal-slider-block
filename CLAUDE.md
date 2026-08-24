# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A WordPress Gutenberg block plugin (single block: `create-block/vidal-slider-block`, an image slider/carousel) scaffolded with `@wordpress/create-block`. Source lives in `src/`, compiled/registered assets are emitted to `build/` (gitignored, generated — never edit `build/` by hand).

## Commands

```
npm run start      # wp-scripts start --blocks-manifest — dev build with watch
npm run build       # wp-scripts build --blocks-manifest — production build into build/
npm run format       # wp-scripts format
npm run lint:js      # wp-scripts lint-js
npm run lint:css     # wp-scripts lint-style
npm run plugin-zip    # package the plugin into a distributable zip
```

There is no test suite configured. This plugin runs inside a WordPress install (it's under `wp-content/plugins/`), so verifying behavior requires a running WordPress site with the plugin active — build with `npm run build` (or `npm run start` for iterative work), then check the block in the editor and on the front end.

## Architecture

Single-block plugin using the WordPress block metadata collection API (WP 6.7+/6.8+):

- `vidal-slider-block.php` — plugin bootstrap. Calls `wp_register_block_types_from_metadata_collection()` against `build/` + `build/blocks-manifest.php`, which registers the block and its assets in one call. It does not reference `src/` at all — if you change block behavior, you must rebuild before it takes effect in WordPress.
- `src/vidal-slider-block/block.json` — the single source of truth for the block's name, attributes, and asset wiring (`editorScript`, `editorStyle`, `style`, `viewScript`, `render`). Attributes: `images` (array of `{id, url, alt}`), `layout` (`"boxed" | "full"`), `interval` (ms), `autoplay` (bool).
- `src/vidal-slider-block/index.js` — registers the block type client-side (`registerBlockType`), wiring `edit` and `save`.
- `src/vidal-slider-block/edit.js` — editor UI. Image selection/removal via `MediaUpload`/`MediaUploadCheck`, and slider settings (full width, autoplay, interval) via `InspectorControls`.
- `src/vidal-slider-block/save.js` — returns `null`. This is a **dynamic block**: nothing is serialized into `post_content` beyond the attributes; markup is generated server-side.
- `src/vidal-slider-block/render.php` — server-side render callback (PHP, receives `$attributes`, `$content`, `$block`). Builds the front-end markup (`.vidal-slider`, `.vidal-slider__track`, `.vidal-slider__slide`, `.vidal-slider__dots`) from attributes. Returns early (renders nothing) when `images` is empty. UI strings here are in Portuguese.
- `src/vidal-slider-block/view.js` — front-end script enqueued via `viewScript`. Intended to drive the actual slider behavior (autoplay/interval/dot navigation) against the markup `render.php` outputs — currently still boilerplate and not implemented.
- `src/vidal-slider-block/style.scss` / `editor.scss` — front-end (both editor+front) and editor-only styles, respectively — still mostly scaffold placeholders, not yet styled for the actual slider markup.

Because `edit.js`/`save.js` only shape editor state and attributes (save always returns `null`), the real rendering contract for the slider lives in `render.php`, and the class names it emits (`vidal-slider__*`) are what `view.js` and the stylesheets need to target.
