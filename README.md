# Log Analyzer Plugin

Analyze any server log using AI (Google Gemini, OpenAI, Anthropic, or OpenAI-compatible) and get instant, concise solutions for crashes and errors.

## Features
- Analyzes all types of server logs with modern AI models (Gemini, OpenAI, Anthropic Claude, custom OpenAI-compatible endpoints)
- Returns short, actionable solutions for detected issues
- Customizable sidebar navigation position via Plugin Settings (place it at the top, after Console, after Files, at the bottom, or enter a custom position)

## Installation
1. Copy the plugin to your `plugins` directory (e.g. `pelican/plugins/log-analyzer`).
2. Run `php artisan p:plugin:install` or enable it via the panel's plugin manager.

## Configuration
Configure the plugin directly via the Pelican Panel plugin settings:
1. Choose your AI provider and enter your API key.
2. Select your desired AI model (or provide a custom model id / base URL).
3. **Sidebar Position:** Choose where the Log Analyzer tab should appear in the server sidebar navigation (Top, After Console, After Files, Bottom, or Custom number).

## Usage
- Open the Log Analyzer tab in your server.
- Click on analyze log.
- The response will be short, direct, and include a solution or a hint if no crash is found.

## Notes
- Do not share your API key publicly.
- The plugin sends log data to the chosen AI provider API.
- For support or feedback, use GitHub Issues.

---

**Made with ❤️ for server administrators**
