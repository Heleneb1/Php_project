import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["body"];

    connect() {
        this.darkMode = JSON.parse(localStorage.getItem('darkMode')) ?? false;
        console.log('Dark Mode Status on Connect:', this.darkMode);
        this.updateTheme();
    }

    toggleDarkMode(event) {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        console.log('Dark Mode Toggled:', this.darkMode);
        this.updateTheme();
    }

    updateTheme() {
        console.log('Updating Theme:', this.darkMode ? 'dark' : 'light');
        this.bodyTarget.setAttribute('data-bs-theme', this.darkMode ? 'dark' : '');
    }
}
