import React from 'react';
import { createRoot } from 'react-dom/client';

const App = () => <h1>Hello, React is working!</h1>;

// Assurez-vous que l'élément #root existe dans votre HTML
const rootElement = document.getElementById('root');
if (rootElement) {
    const root = createRoot(rootElement);
    root.render(<App />);
} else {
    console.error("Element with id 'root' not found in the DOM.");
}