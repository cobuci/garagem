---
name: Garagem
description: High-density commercial operations and distribution command center.
colors:
  primary: "#0284c7"
  primary-hover: "#0369a1"
  primary-light: "#e0f2fe"
  nav-indigo: "#4f46e5"
  status-paid: "#10b981"
  status-pending: "#f59e0b"
  status-danger: "#ef4444"
  neutral-bg: "#f9fafb"
  neutral-surface: "#ffffff"
  neutral-border: "#f3f4f6"
  neutral-text: "#111827"
  neutral-muted: "#6b7280"
  dark-bg: "#0f172a"
  dark-surface: "#1e293b"
  dark-border: "#334155"
  dark-text: "#f8fafc"
  dark-muted: "#94a3b8"
typography:
  display:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(2rem, 4vw, 3rem)"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "-0.025em"
  headline:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "-0.02em"
  title:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.3
    letterSpacing: "normal"
  body:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: "0.05em"
rounded:
  sm: "6px"
  md: "8px"
  lg: "12px"
  xl: "16px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.neutral-surface}"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
  card:
    backgroundColor: "{colors.neutral-surface}"
    rounded: "{rounded.xl}"
    padding: "24px"
---

# Design System: Garagem

## Overview

**Creative North Star: "The Command Depot"**

Garagem is built as a high-density, robust commercial workstation that pairs operational hierarchy with immediate, unambiguous status signaling. Designed for wholesale distributors and central store operators, the interface favors tabular clarity, crisp data boundaries, and rapid navigation over decorative softness. Every surface operates like an instrument panel in an active distribution hub: clear, responsive, and uncompromising on numerical legibility.

The visual language balances pragmatic slate neutrals with purposeful "Command Sky" active accents and an unwavering "Functional Semaphore" color discipline. Data density is treated with respect: cards provide clean groupings without excess padding, tables maintain sticky context headers, and monetary amounts are always formatted for immediate visual verification.

**Key Characteristics:**
- **High-Density Tabular Precision:** Tight vertical rhythm, horizontal rule dividers, and monospace tabular numbers for immediate scanning.
- **Purposeful Semaphore Signaling:** Strict, non-negotiable color semantic roles where emerald exclusively means collected/profit, amber means pending receivable, and red means liability/loss.
- **Tonal Depth Hierarchy:** Flat at rest with hairline borders (1px) and subtle interactive lift on active inspection.
- **Dual-Mode Cohesion:** Native dark mode mirroring the light mode structure without contrast degradation.

## Colors

The palette is anchored by pragmatic neutrals, accented with an industrial sky blue for primary system actions, and structured around a strict functional semaphore for financial state recognition.

### Primary
- **Command Sky** (#0284c7): The active operational accent. Used strictly for primary call-to-action buttons, active tabs, selected state indicators, focus outlines, and interactive links.

### Secondary
- **Nav Indigo** (#4f46e5): Used for persistent navigation anchors, sidebar branding badges, and system-level administrative badges.

### Tertiary
- **Semaphore Emerald** (#10b981): Strictly indicates confirmed revenue, positive profit margins, completed status, and success toasts.
- **Semaphore Amber** (#f59e0b): Strictly indicates pending receivables, uncollected customer balances, drafts, and unsaved changes.
- **Semaphore Red** (#ef4444): Strictly indicates bills payable, cancelled sales, negative balances, and critical warnings.

### Neutral
- **Slate Canvas (Light)** (#f9fafb): The default background canvas for back-office workspaces.
- **Surface Pure White (Light)** (#ffffff): Card surfaces, table containers, and modal dialog backgrounds.
- **Hairline Border (Light)** (#f3f4f6 / #e5e7eb): Structural 1px separation lines for tables and card perimeters.
- **Charcoal Text (Light)** (#111827): Primary high-contrast typography.
- **Muted Steel (Light)** (#6b7280): Secondary metadata, column labels, and timestamps.
- **Obsidian Canvas (Dark)** (#0f172a): Deep dark background canvas.
- **Slate Surface (Dark)** (#1e293b): Elevated card backgrounds and table containers in dark mode.
- **Border Slate (Dark)** (#334155): Low-contrast dividing lines in dark mode.
- **Ice Text (Dark)** (#f8fafc): Crisp typography on dark surfaces.

### Named Rules
**The Semaphore Rule.** Status colors are functional indicators, never decorative accents. Green, amber, and red must not be used for generic branding or background ornamentation.
**The Left-Accent Rule.** Selected records and active table rows project an inset 3px Command Sky indicator on their left border (`shadow-[inset_3px_0_0_0_#0ea5e9]`) accompanied by a subtle sky tint.

## Typography

**Display Font:** Instrument Sans (with ui-sans-serif, system-ui, sans-serif fallback)
**Body Font:** Instrument Sans (with ui-sans-serif, system-ui, sans-serif fallback)
**Label/Mono Font:** Instrument Sans with `font-variant-numeric: tabular-nums`

**Character:** Technical, clean, and legible. The pairing conveys contemporary software authority with maximum numerical legibility across dense data grids.

### Hierarchy
- **Display** (800, clamp(2rem, 4vw, 3rem), 1.1): Hero marketing banners and login branding titles.
- **Headline** (700, 1.5rem / 24px, 1.2): Screen headers, main page titles (`font-bold text-gray-900 dark:text-white`).
- **Title** (600, 1.125rem / 18px, 1.3): Card headings, section dividers, and modal headers.
- **Body** (400, 0.875rem / 14px, 1.5): Table rows, form inputs, customer descriptions, and general system text.
- **Label** (600, 0.75rem / 12px, 1.2, uppercase, tracking-wider): Table column headers, stat card metric titles, status pills.

### Named Rules
**The Tabular Numbers Rule.** All currency amounts, inventory counts, percentages, and dates must use tabular numbers (`tabular-nums`) to prevent jitter and maintain column alignment.
**The Micro-Label Rule.** Descriptive metadata and table column headers must always use uppercase 11-12px bold labels with letter spacing (`text-xs font-semibold uppercase tracking-wider text-gray-500`).

## Layout

The application operates on a persistent fixed sidebar layout (64px / 16rem width on desktop, collapsible drawer on mobile) paired with a responsive, high-density main content container.

- **Desktop Shell:** Fixed sidebar on the left (`w-64`), main viewport (`lg:ml-64`) with 24px to 32px padding (`p-6 lg:p-8`).
- **Mobile Shell:** Fixed top bar with drawer hamburger trigger and avatar dropdown; full-width scrollable viewport.
- **Grid Structure:** Metric cards adapt from 1 column on mobile to 3 columns on tablet/desktop (`grid-cols-1 md:grid-cols-3 gap-6`). Operational split grids use 2/3 main stage and 1/3 sidebar pattern (`lg:grid-cols-3` with `lg:col-span-2`).
- **Data Grids:** Full-width responsive tables with horizontal overflow wrapping, sticky header rows (`sticky top-0 z-10`), and fixed action columns.

## Elevation & Depth

Garagem follows a tonal and layered elevation philosophy. Surfaces are flat at rest, with depth primarily conveyed through subtle 1px border lines (`border-gray-100 dark:border-gray-700`) and distinct background contrast rather than heavy drop shadows.

### Shadow Vocabulary
- **Ambient Rest** (`box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)`): Applied to cards, panels, and standard data containers.
- **Interactive Lift** (`box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)`): Applied on card hover, dropdown menus, and active interactive elements.
- **Modal Overlay** (`box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)`): Applied to dialog cards and slide-over drawers.

### Named Rules
**The Flat-By-Default Rule.** Surfaces remain flat with hairline border definition at rest. Elevated shadows only appear to communicate modal priority or interactive hover response.

## Shapes

- **Card Containers:** Gently rounded (`rounded-xl` / 16px) with an inner 1px border.
- **Interactive Controls:** Moderately rounded (`rounded-md` / 8px) for buttons, text inputs, selects, and dropdown menus.
- **Indicators & Badges:** Fully rounded pills (`rounded-full` / 9999px) for status badges, notification pips, and user avatars.

## Components

### Buttons
- **Shape:** 8px radius (`rounded-md`).
- **Primary:** Solid Command Sky background (`#0284c7`), white text, medium weight (500/600), padding `8px 16px` (`px-4 py-2`).
- **Hover / Focus:** Darkens to `#0369a1` on hover; focus ring with 2px offset (`ring-2 ring-sky-500 ring-offset-2`).
- **Flat / Ghost:** Transparent background, primary or secondary text, hover background `bg-gray-100 dark:bg-gray-700/50`.

### Cards / Containers
- **Corner Style:** 16px radius (`rounded-xl` or `rounded-2xl` for stat widgets).
- **Background:** Pure white (`#ffffff`) in light mode; Slate-800 (`#1e293b`) in dark mode.
- **Border:** 1px solid `border-gray-100 dark:border-gray-700`.
- **Internal Padding:** `24px` (`p-6`).

### Data Tables
- **Header:** Sticky top header with subtle gray background (`bg-gray-50 dark:bg-gray-900/50`), uppercase tracking-wider text.
- **Row Interaction:** Subtle background hover highlight (`hover:bg-gray-100 dark:hover:bg-gray-700/50`).
- **Selection State:** Left inset 3px Command Sky line with soft sky background wash.

### Inputs / Fields
- **Style:** 1px border (`border-gray-300 dark:border-gray-600`), white/slate-900 surface, 8px radius.
- **Focus:** 2px Sky focus outline (`focus:ring-2 focus:ring-sky-500 focus:border-sky-500`).
- **Currency & Money Input:** Integrated BRL prefix (`R$`) with right-aligned tabular numbers.

### Navigation Sidebar
- **Sidebar:** Clean white/slate-800 panel with top brand logo and grouped navigation links.
- **Active Nav Item:** Soft indigo tint (`bg-indigo-50 dark:bg-indigo-900/50`) with indigo text (`text-indigo-700 dark:text-indigo-300`).

## Do's and Don'ts

### Do:
- **Do** format every currency value in Brazilian Real with integer cents precision (`R$ 1.250,00`).
- **Do** use `tabular-nums` on all numerical data columns, metric values, and stock counts.
- **Do** respect the Semaphore rule: emerald for paid/profit, amber for pending/unpaid, red for debt/loss/danger.
- **Do** maintain 1px hairline border separation between cards and backgrounds.
- **Do** support full dark mode contrast across all custom components and table rows.

### Don't:
- **Don't** use amber or red as decorative brand colors; they are strictly reserved for financial/operational state.
- **Don't** remove the 3px inset accent from selected table rows.
- **Don't** float floating action buttons or decorative elements over data grids.
- **Don't** apply heavy drop shadows to rest-state cards or data tables.
- **Don't** mix inconsistent rounding scales; use `rounded-xl` for cards, `rounded-md` for inputs/buttons, and `rounded-full` for badges.
