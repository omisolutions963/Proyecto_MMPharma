# Design System Specification: Clinical Precision & Tonal Depth

## 1. Overview & Creative North Star: "The Clinical Curator"
In the B2B pharmaceutical sector, trust is not merely a feeling; it is a requirement. This design system departs from the rigid, boxy layouts of traditional portals like McKesson or Nadro, moving toward a "Clinical Curator" aesthetic. 

Our North Star is **Atmospheric Authority**. We achieve this by replacing harsh 1px borders with tonal layering and expansive whitespace. The goal is to make data-dense pharmaceutical inventory feel breathable and intentional. We utilize asymmetric layouts—where sidebars and content blocks overlap subtly—to create a sense of bespoke engineering rather than a generic template.

## 2. Colors & Surface Philosophy
The palette is rooted in deep medical navies and high-efficacy greens, but the implementation relies on **Tonal Hierarchy** rather than outlines.

### The "No-Line" Rule
Explicitly prohibit the use of 1px solid borders for sectioning content. Boundaries must be defined through background color shifts. For example, a `surface_container_low` dashboard card should sit on a `surface` background. The eye should perceive the edge through the shift in value, not a stroke.

### Surface Hierarchy & Nesting
Treat the UI as a series of stacked, physical layers of "frosted glass."
- **Base Layer:** `surface` (#f7f9ff)
- **Content Zones:** `surface_container_low` (#edf4ff) for secondary modules.
- **Active Focus:** `surface_container_lowest` (#ffffff) for primary interactive cards.
- **Elevation:** Use `surface_bright` to highlight temporary overlays.

### The "Glass & Gradient" Rule
To escape the "flat web" look, use Glassmorphism for floating elements (e.g., "Red Fría" status modals) using `surface` tokens with a 20px `backdrop-blur`. Main CTAs should utilize a subtle linear gradient from `primary` (#002451) to `primary_container` (#1a3a6b) at a 135-degree angle to provide visual "soul."

## 3. Typography: The Editorial Scale
We use **Inter** as a tool of precision. The hierarchy is designed to make complex pharmaceutical data legible at a glance.

- **Display (lg/md/sm):** Used for hero metrics (e.g., total quarterly spend). Tight letter-spacing (-0.02em) to maintain an authoritative, editorial feel.
- **Headline (lg/md/sm):** Reserved for page titles and major section headers. Use `on_surface` (#051d30) for maximum contrast.
- **Title (lg/md/sm):** Used for card headings. Medium weight (500) ensures clarity without visual bulk.
- **Body (lg/md/sm):** The workhorse for SKU descriptions and clinical notes. Use `on_surface_variant` (#43474f) for secondary body text to reduce eye fatigue.
- **Label (md/sm):** All-caps for "Red Fría" badges or inventory status, with increased letter-spacing (+0.05em).

## 4. Elevation & Depth
Depth is a functional tool, not a decoration.

- **Tonal Layering:** Instead of shadows, stack `surface_container_lowest` cards on top of `surface_container_high` backgrounds. This creates a soft, natural lift.
- **Ambient Shadows:** For floating action buttons or dropdowns, use a 40px blur, 8% opacity shadow tinted with `primary` (#002451). Avoid "pure gray" shadows; they look "dirty" in a clinical context.
- **The "Ghost Border" Fallback:** If a boundary is legally or accessibility-required, use a "Ghost Border": `outline_variant` (#c4c6d0) at 15% opacity. 100% opaque borders are strictly forbidden.

## 5. Components

### High-Density Data Tables
*   **Structure:** No vertical or horizontal lines. Use `surface_container_low` for the header row and alternating `surface` / `surface_container_lowest` for rows.
*   **Spacing:** Use `spacing[4]` (1rem) for cell padding to allow data to breathe.

### "Red Fría" (Cold Chain) Badges
*   **Visual:** A glassmorphic pill shape using `tertiary_container` (#004520) with a 40% opacity. 
*   **Iconography:** A micro-snowflake icon (12px) paired with `label-sm` text.

### Buttons
*   **Primary:** Gradient fill (`primary` to `primary_container`), `rounded-md` (0.375rem), white text.
*   **Secondary:** `surface_container_highest` background with `on_secondary_container` text. No border.
*   **Tertiary:** Ghost style. Only `on_surface` text that shifts to a subtle `surface_variant` background on hover.

### Progress Bars (Multi-step Registration)
*   **The Track:** Use `surface_container_high`.
*   **The Progress:** A gradient of `secondary` (#006397).
*   **The "Pulse":** For the active step, use a soft glow using the `secondary_fixed` token.

### Input Fields
*   **State:** Use `surface_container_lowest` for the fill. 
*   **Focus:** Instead of a thick border, use a 2px outer glow of `primary_fixed` (#d7e2ff).

## 6. Do's and Don'ts

### Do:
- Use `spacing[8]` (2rem) between major content blocks to convey a premium, "un-cluttered" feel.
- Use `tertiary` (#002c13) for all "Success" and "Health-related" affirmations.
- Align text to a strict baseline grid to maintain clinical order.

### Don't:
- **No Dividers:** Never use a `<hr>` or `border-b` to separate list items. Use `spacing[4]` or a subtle background shift instead.
- **No High-Saturation Bling:** Avoid neon greens or bright cyans. Stick to the muted, professional tones of the provided tokens.
- **No Hard Corners:** Ensure all interactive elements use the `md` (0.375rem) or `lg` (0.5rem) roundedness scale to soften the "industrial" feel of B2B.

### Director's Final Note:
Remember, in pharma, clarity is safety. We are not just building a portal; we are building a laboratory-grade interface. Every pixel must feel like it was placed with a scalpel.