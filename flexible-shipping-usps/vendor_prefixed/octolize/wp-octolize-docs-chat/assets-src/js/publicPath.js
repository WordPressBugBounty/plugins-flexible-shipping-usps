/* global __webpack_public_path__ */

function getCurrentScriptUrl() {
	if (typeof document === 'undefined') {
		return null;
	}

	if (document.currentScript?.src) {
		return document.currentScript.src;
	}

	const scripts = Array.from(document.getElementsByTagName('script'));
	const currentScript = scripts.reverse().find((script) => script.src && script.src.includes('OctolizeDocsChat.js'));

	return currentScript?.src ?? null;
}

const currentScriptUrl = getCurrentScriptUrl();

if (currentScriptUrl) {
	__webpack_public_path__ = currentScriptUrl.replace(/\/[^/]*$/, '/');
}
