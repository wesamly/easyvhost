import fs from 'fs-extra';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function copyBootstrapIcons() {
    const sourceFonts = path.resolve(__dirname, 'node_modules/bootstrap-icons/font/fonts');
    const destinationFonts = path.resolve(__dirname, 'public/bundle/fonts');

    try {
        //await fs.copy(sourceCss, destinationCss);
        await fs.copy(sourceFonts, destinationFonts);
        console.log('Bootstrap Icons fonts copied successfully.');
    } catch (err) {
        console.error('Error copying Bootstrap Icons fonts:', err);
    }
}

copyBootstrapIcons();