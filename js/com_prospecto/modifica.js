class IVITabGroup extends HTMLElement {
    connectedCallback() {
        this.currentTab = 0;
        this.tabs = this.querySelectorAll('ivi-tab');
        this.render();
    }

    render() {
        this.innerHTML = '';
        const tabHeaders = document.createElement('div');
        const tabContents = document.createElement('div');
        tabHeaders.classList.add('tab-headers');
        tabContents.classList.add('tab-contents');

        this.tabs.forEach((tab, index) => {
            const tabHeader = document.createElement('div');
            tabHeader.textContent = tab.getAttribute('label');
            tabHeader.classList.add('tab-header');
            tabHeader.addEventListener('click', () => this.showTab(index));
            tabHeaders.appendChild(tabHeader);

            const tabContent = document.createElement('div');
            tabContent.innerHTML = tab.innerHTML;
            tabContent.classList.add('tab-content');
            tabContents.appendChild(tabContent);

            if (tab.hasAttribute('active')) {
                this.currentTab = index;
            }
        });

        this.appendChild(tabHeaders);
        this.appendChild(tabContents);

        this.showTab(this.currentTab);
    }

    showTab(index) {
        const tabHeaders = this.querySelectorAll('.tab-header');
        tabHeaders.forEach((header, headerIndex) => {
            if (headerIndex === index) {
                header.classList.add('active');
            } else {
                header.classList.remove('active');
            }
        });

        const tabContents = this.querySelectorAll('.tab-content');
        tabContents.forEach((content, contentIndex) => {
            if (contentIndex === index) {
                content.style.display = 'block';
            } else {
                content.style.display = 'none';
            }
        });

        this.currentTab = index;
    }
}

class IVITab extends HTMLElement {
    connectedCallback() {
        this.style.display = 'none';
    }
}

customElements.define('ivi-tab-group', IVITabGroup);
customElements.define('ivi-tab', IVITab);