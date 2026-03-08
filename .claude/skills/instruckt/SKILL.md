---
name: instruckt
description: "Visual feedback from users via in-browser annotations. Activates when the user pastes UI feedback markdown starting with '# UI Feedback', when annotations or visual feedback are mentioned, or when you need to check for pending UI feedback via MCP."
license: MIT
metadata:
    author: joshcirre
---

# Instruckt — Visual Feedback for AI Agents

Instruckt lets users annotate elements directly in the browser. The user pastes structured markdown into the chat —
everything you need is in the paste.

## Workflow

When the user pastes UI feedback:

1. **Make the fix** — the pasted markdown has the element, component, classes, and text context to find the right code
2. **Check for screenshots** — if any annotation mentions a screenshot, call `instruckt.get_screenshot` to see what the
   user sees
3. **Resolve via MCP** — call `instruckt.get_all_pending` to get annotation IDs, then call `instruckt.resolve` for each
   one you fixed. This removes the markers from the user's browser automatically (no reload needed).

## Recognizing Pasted Feedback

When the user pastes text like this, treat it as instruckt annotations:

```
# UI Feedback: /dashboard

## 1. Change the heading text
- Element: `h1.text-xl` in `pages::dashboard`
- Classes: `text-xl font-bold`
- Text: "Welcome"
- Screenshot: `storage/app/_instruckt/screenshots/01JWXYZ.png`
```

Each `##` item is one annotation. The component name (e.g. `pages::dashboard`) tells you which file to edit.

## Screenshots

Annotations may include a screenshot of the clicked element or a user-selected region. When `get_all_pending` returns an
annotation with `has_screenshot: true`, call `get_screenshot` to see exactly what the user is referring to.

## MCP Tools

| Tool                        | When to use                                                                |
|-----------------------------|----------------------------------------------------------------------------|
| `instruckt.get_all_pending` | Get all pending annotations                                                |
| `instruckt.get_screenshot`  | Get the screenshot image for an annotation (when `has_screenshot` is true) |
| `instruckt.resolve`         | Mark as resolved after fixing — removes the marker from the browser        |

## Best Practices

- **Always resolve after fixing** — without this, the marker stays visible in the user's browser
- Call `get_all_pending` once to get all IDs, then resolve each after fixing
- The component name tells you which file to look at — use it to navigate directly
