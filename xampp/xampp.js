function o(x, m) {
	if (light == x) {
		return;
	}
	document.images[light + 1].src = "img/" + lightm + "_off.gif";
	light = x;
	lightm = m;
}

var last;
function h(t) {
	if (typeof last == "undefined") {
		last = document.anchors[0];
	}
	last.className = 'n';
	t.className = 'nh';
	t.blur();
	last = t;
}

function l(n, m) {
	document.images[n + 1].src = "img/" + m + "_on.gif";
}

function d(n, m) {
	if (light == n) {
		return;
	}
	document.images[n + 1].src = "img/" + m + "_off.gif";
}

function c(u) {
	parent.content.location = u;
}
