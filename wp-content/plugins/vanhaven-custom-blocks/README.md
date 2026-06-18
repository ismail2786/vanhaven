# VanHaven Custom Blocks

A single, extensible plugin bundling premium **Gutenberg + Kadence** blocks for the VanHaven brand. Everything is managed from one **VanHaven** menu in the admin sidebar, and a simple **module registry** makes adding new blocks easy.

## Blocks included

| Block title (in editor) | Block name | What it does |
|-------------------------|------------|--------------|
| **VH Product Showcase** | `vanhaven/product-showcase` | Tabbed WooCommerce product slider (tabs = product categories) |
| **VH Van Handovers** | `vanhaven/handovers` | Image gallery with tag badges in a multi-row carousel |
| **VH Solutions Slider** | `vanhaven/solutions` | Tabbed slider; each tab has heading, description, feature bullets, CTA, badge, image |
| **VH Feature Grid** | `vanhaven/feature-grid` | Flexible grid of icon cards with heading, description, optional key-value rows or a link (2–4 cols) |
| **VH Process Steps** | `vanhaven/process-steps` | Numbered step cards (01, 02, 03...) with title + description |
| **VH Project Gallery** | `vanhaven/project-gallery` | Mosaic gallery from a Projects CPT with click-to-zoom lightbox, Load More, and a 'Showing X of Y' counter |
| **VH Gallery with Filters** | `vanhaven/van-gallery` | Filterable media gallery: category tabs (with thumbnails), Photos/Videos toggle, sort dropdown, lightbox (image+video), Load More, counter |

All share the dark VanHaven aesthetic (orange `#F0651F` accent), need no build step, and server-render their first view for SEO.

---

## Requirements

| Item | Version |
|------|---------|
| WordPress | 6.2+ |
| PHP | 7.4+ |
| WooCommerce | 7.0+ (only for VH Product Showcase) |
| Kadence Blocks | optional |

---

## Installation

1. **Plugins → Add New → Upload Plugin** → `vanhaven-custom-blocks.zip` → **Install Now** → **Activate**.
2. **Settings → Permalinks → Save** once (flushes REST routes).

A **VanHaven** menu appears in the sidebar.

---

## Admin layout

```
VanHaven (top-level menu)
├─ Dashboard        ← overview of every block + counts + quick links
├─ Handovers        ← VH Van Handovers entries (CPT)
└─ Solutions        ← VH Solutions Slider entries (CPT)
```

WooCommerce products/categories (used by VH Product Showcase) stay under the WooCommerce menu. New blocks that define a CPT will automatically add their own submenu here.

---

## Using each block

**VH Product Showcase** — manage products in WooCommerce. Add the block, pick categories as tabs, set products-per-tab, heading, CTA, accent. Badge = a product custom field (configurable key) or *Featured*.

**VH Van Handovers** — **VanHaven → Handovers → Add Handover**: set Featured Image + Tag/Badge, use Order to sequence. Add the block, set rows (1–3) and max images.

**VH Solutions Slider** — **VanHaven → Solutions → Add Solution**: Title = tab label; fill heading, description, repeatable feature bullets, badge, button label + link, Featured Image. Add the block to a page.

**VH Feature Grid** — content lives *in the block* (no admin entry needed). Add the block, then on each card edit the title/description inline, pick an icon, and optionally add key-value rows (e.g. "Type: Full Conversion") or a link (e.g. "Get Directions →"). Set columns (2–4), icon style (filled / outline / none) and accent in the Inspector. This one block covers: 3-card value props, 6-card "why work here" grids, spec cards, and contact cards.

**VH Process Steps** — content lives *in the block*. Add the block, edit each step's title/description inline; numbers auto-increment (01, 02...) or set a custom number per step. Toggle zero-padding and columns in the Inspector. Covers "how it works" / build-process / consultation-flow sections.

**VH Project Gallery** — **VanHaven → Projects → Add Project**: set the Featured Image and an optional caption, use Order to sequence. Add the block to a page; it shows a mosaic of the first N images with a magnify icon on hover. Clicking an image opens a lightbox (arrows + keyboard + Esc/backdrop close). A "Load More" button fetches the next batch and a counter shows "Showing X of Y Projects". Set images-per-load and accent in the Inspector.

**VH Gallery with Filters** — **VanHaven → Van Gallery → Add Media**: set the Featured Image (or, for a video, the poster image + a Video URL), choose Photo/Video, optionally mark Featured, and assign one or more Gallery Categories. Create the filter tabs under **Van Gallery → Categories** (each category can have a thumbnail shown behind its tab). Add the block to a page: it shows category tabs (All Builds / Featured / your categories), a Photos/Videos toggle, a Sort dropdown (Featured / Newest / Oldest), a lightbox (images zoom; videos play inline via YouTube/Vimeo/MP4), a "Showing X of Y" counter and Load More. Toggle the Photos/Videos and Sort controls on/off in the Inspector.

---

## Architecture (built to scale)

```
vanhaven-custom-blocks/
├─ vanhaven-custom-blocks.php          ← slim bootstrap (constants + delegates to registry)
├─ includes/
│  ├─ class-vhcb-registry.php          ← THE module manifest (add new blocks here)
│  ├─ class-vhcb-admin-menu.php        ← builds the VanHaven menu from the registry
│  ├─ showcase/    (VHSC_* classes)
│  ├─ handovers/   (VHHG_* classes)
│  └─ solutions/   (VHS_* classes)
└─ blocks/
   ├─ product-showcase/  (block.json, index.js, view.js, style.css, editor.css, placeholder.jpg)
   ├─ handovers/         (block.json, index.js, view.js, style.css, editor.css)
   └─ solutions/         (block.json, index.js, view.js, style.css, editor.css)
```

REST namespaces per block: `vanhaven/v1` (showcase), `vanhaven-handovers/v1`, `vanhaven-solutions/v1`.

---

## Adding a new block later (3 steps)

The plugin is registry-driven, so new blocks don't touch the bootstrap or menu code.

1. **Create the block assets** in `blocks/<your-slug>/` (a `block.json` with `"category": "vanhaven"`, plus `index.js`, `view.js`, `style.css`). Copy an existing block folder as a template.

2. **Create the PHP classes** in `includes/<your-slug>/` — at minimum a block class with a `register()` that calls `register_block_type( VHCB_PATH . 'blocks/<your-slug>' , ... )`. Add a CPT class (with a `register()` and `register_post_type()`) only if the block needs editable content.

3. **Register it** — add one entry to the `$modules` array in `includes/class-vhcb-registry.php`:

```php
'pricing' => array(
    'label'     => __( 'VH Pricing Table', 'vanhaven-custom-blocks' ),
    'dir'       => 'pricing',
    'files'     => array( 'class-vhp-cpt.php', 'class-vhp-block.php' ),
    'boot'      => array( 'VHP_CPT' => 'register', 'VHP_Block' => 'register' ),
    'cpt'       => 'vh_pricing',          // omit/empty if no CPT
    'cpt_label' => __( 'Pricing', 'vanhaven-custom-blocks' ),
    'needs_wc'  => false,
),
```

That's it — the registry loads its files, boots its classes, and the admin menu adds a "Pricing" submenu + a dashboard card automatically. (Re-save Permalinks once if you added a new CPT.)

---

## Migrating from the three separate plugins

This bundle reuses the same custom post types (`vh_handover`, `vh_solution`), meta keys, and block names. So your existing content + placed blocks carry over.

1. Deactivate the three standalone plugins.
2. Activate **VanHaven Custom Blocks**.
3. Confirm content shows, then delete the old three.

---

## Troubleshooting

- **404 on a block's data / "Publishing failed"** → Settings → Permalinks → Save; clear page cache (e.g. LiteSpeed).
- **VH Product Showcase empty** → WooCommerce active + categories have published products.
- **Handovers / Solutions empty** → add a published entry with a Featured Image.
- **Block missing in editor** → search "VH" or "VanHaven" in the inserter.
