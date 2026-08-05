# Kaz Theme for WikiDocs

🎨 **Modern UI/UX theme** for [WikiDocs](https://github.com/Zavy86/WikiDocs) — a complete frontend redesign.

![Version](https://img.shields.io/badge/version-1.0.0-brightgreen)
![License](https://img.shields.io/badge/license-MIT-blue)

---

## 📋 What Changed

This is a **complete UI/UX rewrite** of the WikiDocs wiki engine frontend. The PHP backend logic is **untouched** — only the presentation layer has been rebuilt.

### Design Philosophy

- **Modern, clean, minimal** — inspired by Notion, Linear, and modern documentation tools
- **CSS custom properties** — full theming system with light/dark mode via `data-theme` attribute
- **No framework dependency** — removed Materialize CSS/JS entirely; pure CSS + vanilla JS
- **Responsive-first** — works beautifully from mobile to desktop
- **Smooth animations** — CSS transitions and transforms throughout

## 📁 Files to Replace

Copy these files into your WikiDocs root:

| New File | Replaces | Notes |
|---|---|---|
| `styles/styles.css` | `styles/styles.css` | Complete rewrite — 800+ lines of modern CSS |
| `template.inc.php` | `template.inc.php` | New HTML structure, modern layout |
| `settings.php` | `settings.php` | Modern settings UI with color picker |
| `print.inc.php` | `print.inc.php` | Clean print stylesheet |
| `scripts/app.js` | `scripts/initializations.js` | Replaces Materialize: sidebar, modals, toasts |
| `scripts/editor.js` | `scripts/editor.js` | Updated for new modal system |
| `scripts/editor-shortcuts.js` | `scripts/editor-shortcuts.js` | Updated for new UI |
| `scripts/images.js` | `scripts/images.js` | Updated for new modal system |
| `scripts/attachments.js` | `scripts/attachments.js` | Updated for new modal system |

## 🗑️ Files to Delete

These are no longer needed:

```
styles/styles-light.css   — themes handled by data-theme + CSS variables
styles/styles-dark.css    — themes handled by data-theme + CSS variables
helpers/materialize-1.0.0/  — no longer used
helpers/material-icons-1.13.6/ — no longer needed (using emoji + Font Awesome)
```

## 🚀 Quick Install

```bash
# From the project root:
cp kaz-theme-wikidocs/styles.css styles/styles.css
cp kaz-theme-wikidocs/template.inc.php template.inc.php
cp kaz-theme-wikidocs/settings.php settings.php
cp kaz-theme-wikidocs/print.inc.php print.inc.php
cp kaz-theme-wikidocs/app.js scripts/app.js
cp kaz-theme-wikidocs/editor.js scripts/editor.js
cp kaz-theme-wikidocs/editor-shortcuts.js scripts/editor-shortcuts.js
cp kaz-theme-wikidocs/images.js scripts/images.js
cp kaz-theme-wikidocs/attachments.js scripts/attachments.js

# Remove old files
rm styles/styles-light.css styles/styles-dark.css
```

## 🎨 Key Features

### New Sidebar
- Collapsible on mobile with smooth slide animation
- Sticky sidebar footer with owner info
- Active page indicator with left border accent
- Three-level navigation with proper indentation
- Search bar integrated at top

### Top Bar
- Breadcrumb navigation with proper separators
- Action buttons grouped on the right
- Sticky positioning

### Content Area
- Max-width centered (820px) for optimal readability
- Modern typography with Inter font
- Monospace code with JetBrains Mono
- Heading anchor links on hover
- Code blocks with copy button (appears on hover)
- Clean table styling

### Modals
- Custom modal system with backdrop blur
- Scale-in animation
- ESC to close, click overlay to close
- Used for: images, attachments, versions, privacy consent

### Toast Notifications
- Animated slide-in from bottom right
- Auto-dismiss after 4 seconds
- Color-coded: success (green), warning (amber), danger (red), info (blue)

### Dark Mode
- Full dark theme via CSS custom properties
- Toggle in settings page
- Auto-detected from PHP DARK constant
- All components properly themed

### Settings Page
- Organized in sections with clear headers
- Color picker with text input sync
- Grid layout for form fields
- Modern select dropdowns

### Mobile Support
- Sidebar transforms to overlay menu
- Hamburger button in top bar
- Close on overlay tap, ESC key, or window resize
- Full-width content on small screens

## 🎯 CSS Custom Properties

Override in `styles/styles-custom.css`:

```css
:root {
  --color-primary: #4CAF50;
  --color-primary-hover: #43A047;
  --sidebar-width: 280px;
}
```

## ⚠️ Notes

- jQuery is still used (EasyMDE depends on it)
- Material Icons font is removed — using emoji + Font Awesome
- The `helpers/material-icons-1.13.6/` folder can be safely deleted
- The `helpers/materialize-1.0.0/` folder can be safely deleted

## 📸 Preview

| Light Mode | Dark Mode |
|---|---|
| Clean, minimal interface | Full dark theme |

## 📄 License

MIT — same as WikiDocs