# Feature: Open Virtual Host in File Manager

EasyVHost allows you to open your virtual host folders directly in your OS file manager (Finder) from the web interface.

Because web browsers are sandboxed, we use **Hammerspoon** (a powerful automation tool for macOS) to bridge the browser and the operating system.

---

## 1. macOS Installation (via Hammerspoon)

### Step 1: Install Hammerspoon
If you don't have it installed already:
1.  Download and install [Hammerspoon](https://www.hammerspoon.org/).
2.  Run the app. It will sit quietly in your menu bar.
3.  Ensure you grant it **Accessibility** permissions when macOS asks.

### Step 2: Configure the Handler
1.  Click the Hammerspoon icon in the menu bar and select **Open Config**.
2.  This opens your `~/.hammerspoon/init.lua` file.
3.  Paste the following code into the file:

```lua
-- EasyVHost Integration
-- Listens for hammerspoon://openProject?path=/your/path
hs.urlevent.bind("openProject", function(eventName, params)
    local path = params["path"]
    
    if path then
        -- Open the path in Finder using the system 'open' command
        hs.execute("open " .. string.format("%q", path))
        
        -- Optional: Show a notification
        hs.alert.show("📂 Opened: " .. path)
    else
        hs.alert.show("⚠️ EasyVHost: No path provided")
    end
end)
````

4.  Save the file.
5.  Click the Hammerspoon icon in the menu bar and select **Reload Config**.

-----

## 2. Web Interface Integration

To enable the "Open Folder" button, you must configure the link pattern in your `.env` file. EasyVHost uses the `%path%` placeholder to insert the project directory.

**Open your `.env` file and add one of the following:**

### Option A: Hammerspoon (macOS Recommended)

```ini
EASYVHOST_DESKTOP_LINK_PATTERN="hammerspoon://openProject?path=%path%"
```

### Option B: VS Code

```ini
EASYVHOST_DESKTOP_LINK_PATTERN="vscode://file/%path%"
```

### Option C: PhpStorm

```ini
EASYVHOST_DESKTOP_LINK_PATTERN="phpstorm://open?url=file://%path%"
```

### Option D: Custom Protocol (Windows/Linux)

```ini
EASYVHOST_DESKTOP_LINK_PATTERN="easyvhost://%path%"
```

*If this variable is left empty, the "Open Folder" button will remain hidden.*

-----

## 3\. Windows / Linux

*Documentation for Windows and Linux custom protocol handlers coming soon.*

