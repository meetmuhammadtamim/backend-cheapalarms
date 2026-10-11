/**
 * Sync ManPower calculator iframe height to its content (same-origin).
 */
(function () {
	function syncFrame(frame) {
		try {
			var doc = frame.contentDocument;
			if (!doc || !doc.documentElement) {
				return;
			}
			var h = Math.ceil(
				Math.max(
					doc.documentElement.scrollHeight,
					doc.body ? doc.body.scrollHeight : 0
				)
			);
			if (h > 0 && frame.style.height !== h + 'px') {
				frame.style.height = h + 'px';
			}
		} catch (e) {
			/* cross-origin — leave starter height */
		}
	}

	function bind(frame) {
		var run = function () {
			syncFrame(frame);
		};

		frame.addEventListener('load', function () {
			run();
			try {
				var doc = frame.contentDocument;
				if (!doc || !doc.body || typeof ResizeObserver === 'undefined') {
					return;
				}
				var ro = new ResizeObserver(run);
				ro.observe(doc.body);
				ro.observe(doc.documentElement);
			} catch (e) {
				/* ignore */
			}
		});

		window.addEventListener('message', function (ev) {
			var data = ev && ev.data;
			if (!data || data.type !== 'sg-manpower-calc-height') {
				return;
			}
			var h = Number(data.height);
			if (h > 0) {
				frame.style.height = Math.ceil(h) + 'px';
			}
		});
	}

	function init() {
		var frame = document.querySelector('.sg-manpower-calculator-frame');
		if (frame) {
			bind(frame);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
