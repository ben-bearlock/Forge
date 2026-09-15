/**
 * Forge dashboard cleanup. Runs with wp-hooks so filters exist before the React app mounts.
 */
(function () {
	if (typeof window.astra_addon_admin === 'undefined') {
		window.astra_addon_admin = { update_nonce: '', forge_stub: true };
	}

	if (window.wp && wp.hooks) {
		wp.hooks.addFilter('astra_dashboard.main_navigation', 'forge', function (items) {
			return (items || []).filter(function (item) {
				return ['free-vs-pro', 'starter-templates', 'site-builder', 'learn'].indexOf(item.path) === -1;
			});
		});

		wp.hooks.addFilter('astra_dashboard.settings_navigation', 'forge', function (items) {
			return (items || []).filter(function (item) {
				return ['version-control', 'white-label', 'mcp'].indexOf(item.slug) === -1;
			});
		});
	}

	var hideExact = {
		'Help Center': true,
		'Join the Community': true,
		'Rate Us': true,
		'Knowledge Base': true,
		'Activate All': true,
		'Deactivate All': true
	};

	function shouldHide(el) {
		var text = (el.textContent || '').replace(/\s+/g, ' ').trim();
		var href = (el.getAttribute && el.getAttribute('href')) || '';

		if (hideExact[text]) {
			return true;
		}
		if (href.indexOf('facebook.com/groups/wpastra') !== -1) {
			return true;
		}
		if (href.indexOf('wordpress.org/support/theme/astra') !== -1) {
			return true;
		}
		if (href.indexOf('wpastra.com/docs') !== -1) {
			return true;
		}
		return false;
	}

	function hideNode(node) {
		var row = node.closest('.bg-background-primary');
		(row || node).style.display = 'none';
	}

	function trimChrome() {
		var root = document.getElementById('astra-dashboard-app');
		if (!root) {
			return;
		}

		root.querySelectorAll('a, button').forEach(function (el) {
			if (shouldHide(el)) {
				hideNode(el);
			}
		});
	}

	function watch() {
		trimChrome();
		var root = document.getElementById('astra-dashboard-app');
		if (!root || root.getAttribute('data-forge-trimmed')) {
			return;
		}
		root.setAttribute('data-forge-trimmed', '1');
		var observer = new MutationObserver(trimChrome);
		observer.observe(root, { childList: true, subtree: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', watch);
	} else {
		watch();
	}
})();
