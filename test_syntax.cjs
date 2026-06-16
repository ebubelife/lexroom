const fs = require('fs');
let content = fs.readFileSync('resources/views/rooms/show.blade.php', 'utf8');

// Extract the script tag content
let scriptMatch = content.match(/<script>([\s\S]*?)<\/script>/);
if (!scriptMatch) {
    console.log("No script tag found");
    process.exit(1);
}
let scriptContent = scriptMatch[1];

// Remove blade tags
scriptContent = scriptContent.replace(/{!!.*?!!}/g, '[]');
scriptContent = scriptContent.replace(/{{.*?}}/g, '1');

try {
    eval(scriptContent);
    console.log("Syntax is OK");
} catch (e) {
    console.error("Syntax error:");
    console.error(e);
}
