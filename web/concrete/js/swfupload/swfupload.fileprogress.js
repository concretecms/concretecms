/*
	A simple class for displaying file information and progress
	Note: This is a demonstration only and not part of SWFUpload.
	Note: Some have had problems adapting this class in IE7. It may not be suitable for your application.
*/

// Constructor
// file is a SWFUpload file object
// targetID is the HTML element id attribute that the FileProgress HTML structure will be added to.
// Instantiating a new FileProgress object with an existing file will reuse/update the existing DOM elements
function FileProgress(file, targetID) {
	this.fileProgressID = file.id;

	this.opacity = 100;
	this.height = 0;
	

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);

	if (!this.fileProgressWrapper) {
		/* 
		
		this.fileProgressWrapper = document.createElement("TR");
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement1 = document.createElement("TD");
		this.fileProgressElement2 = document.createElement("TD");
		
		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		this.fileProgressElement1.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement1.appendChild(progressCancel);
		this.fileProgressElement1.appendChild(progressText);
		this.fileProgressElement1.appendChild(progressStatus);
		this.fileProgressElement1.appendChild(progressBar);
		
		this.fileProgressWrapper.appendChild(this.fileProgressElement1);
		this.fileProgressWrapper.appendChild(this.fileProgressElement2);
		*/

		$("#" + targetID).append('<tr id="' + this.fileProgressID + '"><td><div style="word-wrap: break-word; width: 150px">' + file.name + '</div></td><td width="100%"><div style="width: 250px !important" class="ccm-file-manager-progress-bar-pending">' + ccmi18n_filemanager.pending + '</div><div class="ccm-file-manager-progress-bar" style="display: none"><div class="progress progress-striped active"><div class="bar" style="width: 0%;"></div></div></div></tr>');
		this.fileProgressWrapper = $("#" + this.fileProgressID).get(0);
		
//		document.getElementById(targetID).appendChild(this.fileProgressWrapper);
	} else {
		this.fileProgressElement = $(this.fileProgressWrapper).find('div.ccm-file-manager-progress-bar').get(0);
		this.fileProgressPendingElement = $(this.fileProgressWrapper).find('div.ccm-file-manager-progress-bar-pending').get(0);
		this.reset();
	}

	this.fileProgressElement = $(this.fileProgressWrapper).find('div.ccm-file-manager-progress-bar').get(0);
	this.fileProgressPendingElement = $(this.fileProgressWrapper).find('div.ccm-file-manager-progress-bar-pending').get(0);

	this.height = this.fileProgressWrapper.offsetHeight;
	this.setTimer(null);

}

FileProgress.prototype.setTimer = function (timer) {
	this.fileProgressElement["FP_TIMER"] = timer;
};
FileProgress.prototype.getTimer = function (timer) {
	return this.fileProgressElement["FP_TIMER"] || null;
};

FileProgress.prototype.reset = function () {
	$(this.fileProgressPendingElement).hide();
	$(this.fileProgressElement).show();
	
	/*
	this.fileProgressElement.className = "progressContainer";

	this.fileProgressElement.childNodes[2].innerHTML = "&nbsp;";
	this.fileProgressElement.childNodes[2].className = "progressBarStatus";
	
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = "0%";
	
	this.appear();	*/
	
	
};

FileProgress.prototype.setProgress = function (percentage) {
	$(this.fileProgressElement).find('.bar').css('width', percentage + "%");
	this.appear();	
};
FileProgress.prototype.setComplete = function () {
	var pendingBar = $(this.fileProgressWrapper).find('div.ccm-file-manager-progress-bar-pending');
	$(this.fileProgressElement).fadeOut(200, function() {
		pendingBar.html(ccmi18n_filemanager.uploadComplete);
		pendingBar.show();
	});
};
FileProgress.prototype.setError = function (msg) {
	var pendingBar = $(this.fileProgressWrapper).find('div.ccm-file-manager-progress-bar-pending');
	$(this.fileProgressElement).fadeOut(200, function() {
		pendingBar.addClass('ccm-error');
		pendingBar.html(msg);
		pendingBar.show();
	});
};
FileProgress.prototype.setCancelled = function () {
	/*this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

	var oSelf = this;
	this.setTimer(setTimeout(function () {
		oSelf.disappear();
	}, 2000));
	*/
};
FileProgress.prototype.setStatus = function (status) {
	var pendingBar = $(this.fileProgressWrapper).find('div.ccm-file-manager-progress-bar-pending');
	pendingBar.html(status);
};

// Show/Hide the cancel button
FileProgress.prototype.toggleCancel = function (show, swfUploadInstance) {
	/*this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfUploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfUploadInstance.cancelUpload(fileID);
			return false;
		};
	}*/
	
};

FileProgress.prototype.appear = function () {
	if (this.getTimer() !== null) {
		clearTimeout(this.getTimer());
		this.setTimer(null);
	}
	
	if (this.fileProgressWrapper.filters) {
		try {
			this.fileProgressWrapper.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 100;
		} catch (e) {
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			this.fileProgressWrapper.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=100)";
		}
	} else {
		this.fileProgressWrapper.style.opacity = 1;
	}
		
	this.fileProgressWrapper.style.height = "";
	
	this.height = this.fileProgressWrapper.offsetHeight;
	this.opacity = 100;
	this.fileProgressWrapper.style.display = "";
	
};

// Fades out and clips away the FileProgress box.
FileProgress.prototype.disappear = function () {

	var reduceOpacityBy = 15;
	var reduceHeightBy = 4;
	var rate = 30;	// 15 fps

	if (this.opacity > 0) {
		this.opacity -= reduceOpacityBy;
		if (this.opacity < 0) {
			this.opacity = 0;
		}

		if (this.fileProgressWrapper.filters) {
			try {
				this.fileProgressWrapper.filters.item("DXImageTransform.Microsoft.Alpha").opacity = this.opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				this.fileProgressWrapper.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=" + this.opacity + ")";
			}
		} else {
			this.fileProgressWrapper.style.opacity = this.opacity / 100;
		}
	}

	if (this.height > 0) {
		this.height -= reduceHeightBy;
		if (this.height < 0) {
			this.height = 0;
		}

		this.fileProgressWrapper.style.height = this.height + "px";
	}

	if (this.height > 0 || this.opacity > 0) {
		var oSelf = this;
		this.setTimer(setTimeout(function () {
			oSelf.disappear();
		}, rate));
	} else {
		this.fileProgressWrapper.style.display = "none";
		this.setTimer(null);
	}
};