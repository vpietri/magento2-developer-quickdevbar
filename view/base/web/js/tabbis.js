class tabbisClass {
    tabOptions ={}

    constructor(options) {
        this.thisOptions(options);
        this.thisMemory();
        this.setup();
    }

    getOption(key, groupIndex) {
        if(typeof groupIndex !== "undefined" && this.tabOptions.hasOwnProperty(groupIndex) && this.tabOptions[groupIndex].hasOwnProperty(key)) {
            return this.tabOptions[groupIndex][key];
        }
        return this.options[key];
    }

	// Setup
	setup() {
		const panes = document.querySelectorAll(this.getOption('paneGroup'));
		const tabs = document.querySelectorAll(this.getOption('tabGroup'));

		tabs.forEach((tabGroups, groupIndex) => {
			const paneGroups = panes[groupIndex];
			const activeIndex = this.getActiveIndex(tabGroups, groupIndex);

			tabGroups.setAttribute('role', 'tablist');
            this.tabOptions[groupIndex] = JSON.parse(tabGroups.getAttribute('tabbis-options'));

			// Reset items
			this.resetTabs([ ...tabGroups.children ]);
			this.resetPanes([ ...paneGroups.children ]);

			[ ...tabGroups.children ].forEach((tabItem, tabIndex) => {
				const paneItem = paneGroups.children[tabIndex];

				// Add attributes
				this.addTabAttributes(tabItem, groupIndex);
				this.addPaneAttributes(tabItem, paneItem);

				tabItem.groupIndex = groupIndex;

				// Trigger event
				tabItem.addEventListener(this.getOption('trigger'), (e) => {
					this.toggle(e.currentTarget, tabItem.groupIndex);
				});

				// Key event
				if (this.getOption('keyboardNavigation')) {
					tabItem.addEventListener('keydown', (e) => {
						this.eventKey(e);
					});
				}
			});

			if (activeIndex !== null) {
                this.toggle([ ...tabGroups.children ][activeIndex]);
			}
		});
	}

	// Event key
	eventKey(e) {
		if ([ 13, 37, 38, 39, 40 ].includes(e.keyCode)) {
			e.preventDefault();
		}

		if (e.keyCode == 13) {
			e.currentTarget.click();
		} else if ([ 39, 40 ].includes(e.keyCode)) {
			this.step(e, 1);
		} else if ([ 37, 38 ].includes(e.keyCode)) {
			this.step(e, -1);
		}
	}

	// Index
	index(el) {
		return [ ...el.parentElement.children ].indexOf(el);
	}

	// Step
	step(e, direction) {
		const children = e.currentTarget.parentElement.children;
		this.resetTabindex(children);

		let el = children[this.pos(e.currentTarget, children, direction)];
		el.focus();
		el.setAttribute('tabindex', 0);
	}

	resetTabindex(children) {
		[ ...children ].forEach((child) => {
			child.setAttribute('tabindex', '-1');
		});
	}

	// Pos
	pos(tab, children, direction) {
		let pos = this.index(tab);
		pos += direction;

		if (children.length <= pos) {
			pos = 0;
		} else if (pos == -1) {
			pos = children.length - 1;
		}

		return pos;
	}

	// Emit event
	emitEvent(eventName, tab, pane) {
		let event = new CustomEvent(eventName, {
			bubbles: true,
			detail: {
				tab: tab,
				pane: pane
			}
		});

		tab.dispatchEvent(event);
	}

	// Set active
	getActiveIndex(groupTabs, groupIndex) {
		const memory = this.loadMemory(groupIndex);

		if (typeof memory !== 'undefined') {
			return memory;
		} else {
			let element = groupTabs.querySelector(this.getOption('tabActive'));

			if (!element) {
				element = groupTabs.querySelector('[aria-selected="true"]');
			}

			if (element) {
				return this.index(element);
			} else if (this.getOption('tabActiveFallback') !== false) {
				return this.getOption('tabActiveFallback');
			} else {
				return null;
			}
		}
	}

	// ATTRIBUTES

	// Add tab attributes
	addTabAttributes(tab, groupIndex) {
		const tabIndex = this.index(tab);
		const prefix = this.getOption('prefix');

		tab.setAttribute('role', 'tab');
		tab.setAttribute('aria-controls', `${prefix}tabpanel-${groupIndex}-${tabIndex}`);
	}

	// Add tabpanel attributes
	addPaneAttributes(tab, pane) {
		pane.setAttribute('role', 'tabpanel');
		pane.setAttribute('aria-labelledby', tab.getAttribute('id'));
        pane.setAttribute('aria-controled-by', tab.getAttribute('aria-controls'));
        pane.setAttribute('tabindex', '0');
	}

    toggle(tab, groupIndex) {
        if(this.isActiveTab(tab, groupIndex) && this.getOption('collapsible', groupIndex)) {
            this.resetForTab(tab);
            this.resetMemoryGroup(groupIndex);
        } else {
            this.activate(tab,groupIndex);
        }
    }

    resetForTab(tab) {
        const pane = this.getPaneForTab(tab);
        this.resetTabs([ ...tab.parentNode.children ]);
        this.resetPanes([ ...pane.parentElement.children ]);
    }

    getPaneForTab(tab) {
        return document.querySelector('[aria-controled-by="'+tab.getAttribute('aria-controls')+'"]');
    }

	// Activate
	activate(tab, i) {
        this.resetForTab(tab)

        const pane = this.getPaneForTab(tab);
        if(tab.getAttribute('data-ajax')) {
            this.loadPaneContent(tab, pane);
            tab.removeAttribute('data-ajax');
        }

		this.activateTab(tab);
		this.activatePane(pane);

		this.saveMemory(tab, i);

		this.emitEvent('tabbis', tab, pane);
        this.emitEvent('tabbis_pane_activate', tab, pane);

    }

    isActiveTab(tab) {
        return tab.getAttribute('aria-selected') === "true";
    }

	// Activate tab
	activateTab(tab) {

		tab.setAttribute('aria-selected', 'true');
		tab.setAttribute('tabindex', '0');
        tab.classList.add(this.getOption('tabActiveClass'));
	}

	// Activate pane
	activatePane(pane) {
		pane.removeAttribute('hidden');
	}

    loadPaneContent(tab, pane) {
        let paneXhrUri = tab.getAttribute('data-ajax');
        if(!paneXhrUri) {
            throw new Error("No data-ajax attribute");
        }

        fetch(paneXhrUri, {
                //To be compliant with \Laminas\Http\Request::isXmlHttpRequest
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                }
            }
        )
        .then(response => {
        if (!response.ok) {
            throw new Error(response.status + " Failed Fetch ");
        }
        return response.text()
        })
        .then(function (html) {
            // console.log(html);
            pane.innerHTML = html;
            this.emitEvent('tabbis_pane_ajax_loaded', tab, pane);
        }.bind(this))
        .catch((err) =>
            console.log("Can’t access " + url + " response. Blocked by browser?" + err)
        );

    }

	// Remove tab attributes
	resetTabs(tabs) {
		tabs.forEach((el) => {
            el.setAttribute('aria-selected', 'false')
            el.classList.remove(this.getOption('tabActiveClass'));
        });
		this.resetTabindex(tabs);
	}

	// Reset pane attributes
	resetPanes(panes) {
		panes.forEach((el) => el.setAttribute('hidden', ''));
	}

	// MEMORY

	// Load memory
	loadMemory(groupIndex) {
		if (!this.options.memory) return;
		if (typeof this.memory[groupIndex] === 'undefined') return;
		if (this.memory[groupIndex] === null) return;

		return parseInt(this.memory[groupIndex]);
	}

	// Save memory
	saveMemory(tab, groupIndex) {
		if (!this.getOption('memory')) return;
		this.memory[groupIndex] = this.index(tab);
		localStorage.setItem(this.options.memory, JSON.stringify(this.memory));
	}

    resetMemoryGroup(groupIndex) {
        this.memory[groupIndex] = null;
        localStorage.setItem(this.options.memory, JSON.stringify(this.memory));
    }


	// This memory
	thisMemory() {
		if (!this.getOption('memory')) return;
		const store = localStorage.getItem(this.options.memory);
		this.memory = store !== null ? JSON.parse(store) : [];
	}

	// OPTIONS

	// Defaults
	defaults() {
		return {
			keyboardNavigation: true,
			memory: false,
			paneGroup: '[data-panes]',
			prefix: '',
			tabActive: '[data-active]',
            tabActiveClass: 'ui-tabs-active',
			tabActiveFallback: 0,
			tabGroup: '[data-tabs]',
			trigger: 'click',
            collapsible: false
		};
	}

	// This options
	thisOptions(options) {
		this.options = Object.assign(this.defaults(), options);
        if (this.options.memory !== true) return;
        this.options.memory = 'tabbis';
	}
}

// Function call
function tabbis(options = {}) {
	const tabs = new tabbisClass(options);
}
