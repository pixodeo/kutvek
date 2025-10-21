function dropHandler(ev) {
    ev.stopPropagation();
    ev.preventDefault();
    console.log('File(s) dropped');
    console.log(ev.target.id);
    let preview = document.getElementById(ev.currentTarget.getAttribute('data-preview'));
    // Prevent default behavior (Prevent file from being opened)
    if (ev.dataTransfer.items) {
        // Use DataTransferItemList interface to access the file(s)
        for (var i = 0; i < ev.dataTransfer.items.length; i++) {
            // If dropped items aren't files, reject them
            if (ev.dataTransfer.items[i].kind === 'file') {
                var file = ev.dataTransfer.items[i].getAsFile();
                // Affichage 

                thumb(file, preview);
                console.log('... file[' + i + '].name = ' + file.name + ' type = ' + file.type);
            }
        }
    } else {
        // Use DataTransfer interface to access the file(s)
        for (var i = 0; i < ev.dataTransfer.files.length; i++) {
            console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
        }
    }
}

function dragOverHandler(ev) {
    console.log('File(s) in drop zone');
    // Prevent default behavior (Prevent file from being opened)
    ev.preventDefault();
}

function thumb (file, preview)
{	
	var span = document.createElement('span');
	span.file = file;
	span.classList.add("obj");	

	// Si fichier type image	
	const imageType = /^image\//;
	if (imageType.test(file.type)) {
		var img = document.createElement("img");		
		img.file = file;			
		var reader = new FileReader();
		reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
		reader.readAsDataURL(file);
		span.appendChild(img);
	} else {
		const fileParts = file.type.split('/');
		span.classList.add(fileParts[1]);
	}
	preview.appendChild(span);  
}
