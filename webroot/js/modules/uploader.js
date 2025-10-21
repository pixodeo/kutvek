const uploader = {
	_elem: null,	
	_posts: null,
	_preview: null,
	_fileList: null,
	_file: null,
	_event: null,
	_bytes: 0,
	_units: ["o", "Ko", "Mo", "Go", "To", "Po", "Eo", "Zo", "Yo"],
	_types: ["png", "jpeg", "webp", "pdf", "csv", "svg+xml", "mp4", "document", "sheet", "ms-excel", "json", "x-msdownload", "x-gzip", "x-zip-compressed", "tsv", "msi", "otf", "ai"],
	_thumbnails: [],
	setElem: function(elem){
	this._elem = elem;
	},	
	setEvent: function(event){
		this._ev = event;
	},
	init: function(ev) {
		this._event = ev;
		this._elem = ev.target;
		this._fileList =   this._elem.files;	
		this._posts = this._elem.form.parentNode.querySelector('.posts');
		this._preview = this._elem.form.querySelector('.preview');
		console.log(this);
	},
	handleFiles: function(){},
	sendFiles:  function() {
		// attention chaque commande peu avoir des réponses avec fichiers à téléverser
		const files = document.querySelectorAll(".obj");
	},

	addFiles: function(){
		console.log(this._elem);
		this._fileList = this._elem.files;	
		this._preview = this._elem.form.querySelector('.preview');	
		this.thumbnail();
	},
	thumbnail: function() {
		// on va générer des miniatures dans les posts		
		console.log(this._fileList);
		const imageType = /^image\//;
		if(this._fileList !== null && this._fileList.length > 0) {
			
			for (let i = 0; i < this._fileList.length; i++) {
				let _type;
				this._file = this._fileList[i];				 
				this._bytes += this._file.size;

				console.debug(this._file.type.length);
				if(this._file.type.length < 1) {
					_type = this._file.name.split('.').pop();					
				} else {					
					let fileParts = this._file.type.split('/');
					console.log(fileParts);					
					_type = fileParts[1].split('.').pop();				
				}				
				console.log(`name: ${this._file.name} type: ${_type}`);

				// on créé une vignette
				//console.log(this._types.indexOf(_type));
				
				if(this._types.includes(_type)) {
					// On a l'extension 					
					console.log('extension exist');
					switch(_type){
						case 'png':
							this._thumbnails[i] = document.createElement("img");
    						this._thumbnails[i].classList.add("obj");
    						this._thumbnails[i].file = this._file;
    						this._thumbnails[i].title = this._file.name;
    						//let tpl = document.getElementById('img-tpl');
    						//let clone = document.importNode(tpl.content, true);
    						this._preview.appendChild(this._thumbnails[i]);
    						this._reader = new FileReader();
    						this._reader.onload = (e) => {
						      this._thumbnails[i].src = e.target.result;
						    };						    
						    this._reader.readAsDataURL(this._file);
							break;
						case 'jpeg':
							this._thumbnails[i] = document.createElement("img");
    						this._thumbnails[i].classList.add("obj");
    						this._thumbnails[i].file = this._file;
    						this._thumbnails[i].title = this._file.name;
    						//let tpl = document.getElementById('img-tpl');
    						//let clone = document.importNode(tpl.content, true);
    						this._preview.appendChild(this._thumbnails[i]);
    						this._reader = new FileReader();
    						this._reader.onload = (e) => {
						      this._thumbnails[i].src = e.target.result;
						    };						    
						    this._reader.readAsDataURL(this._file);
							break;
						case 'webp':
							this._thumbnails[i] = document.createElement("img");
    						this._thumbnails[i].classList.add("obj");
    						this._thumbnails[i].file = this._file;
    						this._thumbnails[i].title = this._file.name;
    						//let tpl = document.getElementById('img-tpl');
    						//let clone = document.importNode(tpl.content, true);
    						this._preview.appendChild(this._thumbnails[i]);
    						this._reader = new FileReader();
    						this._reader.onload = (e) => {
						      this._thumbnails[i].src = e.target.result;
						    };						    
						    this._reader.readAsDataURL(this._file);
							break;
						case 'pdf':
							this._thumbnails[i] = document.createElement("span");
							this._thumbnails[i].classList.add("obj");
							this._thumbnails[i].classList.add("pdf");
    						this._thumbnails[i].file = this._file;
    						this._thumbnails[i].title = this._file.name;
    						this._preview.appendChild(this._thumbnails[i]);
							break;
						default:
							this._thumbnails[i] = document.createElement("span");
							this._thumbnails[i].classList.add("obj");
							this._thumbnails[i].classList.add("file");									
    						this._thumbnails[i].file = this._file;
    						this._thumbnails[i].title = this._file.name;
    						let b = document.createElement("b");
    						b.classList.add('name');
    						b.textContent = this._file.name;
    						this._thumbnails[i].appendChild(b);
    						this._preview.appendChild(this._thumbnails[i]);
							break;
					}
						
				} else {
					// vignette générique

				}
			}
			
			const exponent = Math.min(
		      Math.floor(Math.log(this._bytes) / Math.log(1024)),
		      this._units.length - 1,
		    );
		    const approx = this._bytes / 1024 ** exponent;
		    const output = exponent === 0 ? `${this._bytes} octets` : `${approx.toFixed(3)} ${this._units[exponent]} (${this._bytes} octets)`;
		    console.log(`Taille totale : ${output}`);
		} else {
			console.log('No files uploaded');
		}
		//var span = document.createElement('span');
		//span.file = file;
		//span.classList.add("obj");	
		
		

		/*if (imageType.test(file.type)) {
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
		preview.appendChild(span);  */

	}
}

export default uploader;