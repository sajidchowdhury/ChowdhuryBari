<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Test v2</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.14.2/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="p-8">
    <h1>Modal Test v2</h1>

    <p>Alpine.js status: <span id="alpine-status" style="color: red;">NOT loaded</span></p>
    <script>
        document.addEventListener('alpine:init', () => {
            document.getElementById('alpine-status').textContent = 'LOADED ✓';
            document.getElementById('alpine-status').style.color = 'green';
        });
    </script>

    <hr>

    <h2>Test 1: Pure Alpine modal (no Blade component)</h2>
    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'test' }))"
            style="padding: 12px 24px; background: #0F766E; color: white; border: none; border-radius: 8px; cursor: pointer;">
        Open Modal
    </button>

    <div x-data="{ show: false }"
         x-on:open-modal.window="$event.detail === 'test' && (show = true)"
         x-on:close-modal.window="$event.detail === 'test' && (show = false)"
         x-on:keydown.escape.window="show = false"
         x-show="show"
         x-cloak
         style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; padding: 24px;">
        <div style="background: white; max-width: 500px; margin: auto; padding: 24px; border-radius: 16px;">
            <h2>✅ Modal 1 Works!</h2>
            <p>If you can see this, Alpine.js + the event dispatch + the modal logic all work.</p>
            <button onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'test' }))"
                    style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer;">
                Close
            </button>
        </div>
    </div>

    <hr style="margin: 40px 0;">

    <h2>Test 2: Blade &lt;x-modal&gt; component</h2>
    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'blade-test' }))"
            style="padding: 12px 24px; background: #0F766E; color: white; border: none; border-radius: 8px; cursor: pointer;">
        Open Blade Modal
    </button>

    <x-modal name="blade-test" maxWidth="lg">
        <div style="padding: 24px;">
            <h2>✅ Blade Modal Works!</h2>
            <p>If you can see this, the Blade &lt;x-modal&gt; component renders correctly.</p>
            <button onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'blade-test' }))"
                    style="padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer;">
                Close
            </button>
        </div>
    </x-modal>
</body>
</html>
