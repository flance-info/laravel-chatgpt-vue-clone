chrome.commands.onCommand.addListener(function (command) {
	if (command === "takeScreenshot") {
		chrome.tabs.query({active: true, currentWindow: true}, function (tabs) {
			var activeTab = tabs[0];
			if (!activeTab) {
				console.log("No active tab found.");
				return;
			}
			chrome.scripting.executeScript({
				target: {tabId: activeTab.id},
				func: captureVisibleTab(),
				files: ['script.js'],
			}, function (results) {
				if (chrome.runtime.lastError) {
					console.error(chrome.runtime.lastError.message);
				} else if (!results || !results[0]) {
					console.log("Failed to execute script.");
				}
			});
		});
	}
});



function captureVisibleTab() {
	console.log("captureVisibleTab.");
	chrome.tabs.captureVisibleTab(null, {format: "png"}, function (dataUrl) {
		if (chrome.runtime.lastError) {
			console.error(chrome.runtime.lastError.message);
			return;
		}
		sendScreenshotToGPT(dataUrl);
	});
}

function sendScreenshotToGPT(dataUrl) {
    downloadImage(dataUrl);
    fetch('https://one-aware-mole.ngrok-free.app/upload-screenshot', {
        method: 'POST',
        body: JSON.stringify({screenshot: dataUrl}),
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to send screenshot to Laravel');
            }
            return response.json(); // Assuming the Laravel endpoint returns JSON
        })
        .then(data => {
            console.log('Screenshot sent to Laravel successfully', data);
        })
        .catch(error => {
            console.error('Error sending screenshot to Laravel:', error);
        });
}


function downloadImage(dataUrl) {
	chrome.downloads.download({
		url: dataUrl,
		filename: 'screenshot.png',
		saveAs: false
	});
}
