const order = {
	_elem: null,		
	setElem: function(elem){
	this._elem = elem;
	},	
	setEvent: function(event){
		this._ev = event;
	},
	mockupsToValidate: function() {		
		let div = document.getElementById('mockups');
		let url = div.getAttribute('data-uri');
		getOrdersToValidate(url)
		.then( json => {
				if(json.orders && json.orders.length > 0){
					//console.log(json.orders);
					div.setAttribute('data-fetched', 1);
					let link = document.querySelector('a[href="#mockups"]');
					link.querySelector('span.counter > small').textContent = json.orders.length;
					let ul = div.querySelector('ul.tasks');
					let tpl = document.getElementById('task');
					var clone = document.importNode(tpl.content, true);
					json.orders.forEach(task => {
						let li = clone.cloneNode(true);
						let reference = li.querySelector('span.reference');
						reference.textContent = task.app_com_num;
						li.querySelector('.designation').textContent = task.designation;
						// tous les ids et les hrefs
						let links = li.querySelectorAll('ul.tabs > li > a');
						let divs = li.querySelectorAll('div.tab_content');
						links.forEach(link => {							
							link.href = `${link.hash}-${task.id}`;
							link.addEventListener('click', function(e) {		
								display_tab(this, e);		
						});
						});
						divs.forEach(div => {div.id = `${div.id}-${task.id}`});
						let form = li.querySelector('form.form-posts');
						form.id = `form-posts-${task.id}`;
						let fileInput = form.querySelector('input[type="file"');
						fileInput.id = `files-${task.id}`;
						fileInput.addEventListener('change', () => {
							uploader.init(event);
							uploader.thumbnail();
						}, false);
						form.parentNode.querySelector('label.files-label').htmlFor = `files-${task.id}`;						
						form.querySelector('textarea').innerHTML = task.designation;						
						// posts / chat
						if(task.posts.length > 0){
							let postTpl = document.getElementById('post-tpl');
							let content = document.importNode(postTpl.content, true);
							let postsDiv = li.querySelector('div.posts');
							
							
							task.posts.forEach(post => {
								let clone = content.cloneNode(true);
								clone.querySelector('div.post').id = post.id;
								clone.querySelector('span.created').textContent = post.created;
								clone.querySelector('span.user').textContent = post.type_user;
								clone.querySelector('div.body').innerHTML = post.body;
								clone.querySelector('div.post').classList.add(post.class);
								postsDiv.appendChild(clone);
							});
						}
						ul.appendChild(li);
					});					
				this.pell();

				} else 	console.log(json);												
			}
		).catch(error => {console.debug(error)});	

	},
	currentOrders: function(){
		
		let div = document.getElementById('in-progress');
		let url = div.getAttribute('data-uri');
		getCurrentOrders(url)
		.then( json => {
			if(json.orders && json.orders.length > 0){
					console.log(json.orders);
					div.setAttribute('data-fetched', 1);
				} else 	console.log(json);							
		}
		).catch(error => {console.debug(error)});	

	},
	ordersByYear(): function{
		let div = document.getElementById('orders');
		let url = div.getAttribute('data-uri');
		getOrdersByYear(url)
		.then( json => {
			if(json.orders && json.orders.length > 0){
					console.log(json.orders);
					
				} else 	console.log(json);							
		}
		).catch(error => {console.debug(error)});	
	},
	pell: function(){				  
  		const editors = document.querySelectorAll('.pell-editor');
		editors.forEach(function(el,idx)
		{  
		  let editor = pell.init({
		    element: el,
		    onChange: html => {
		        el.parentNode.querySelector('.pell-editor-output').innerHTML = html
		    },
		    defaultParagraphSeparator: 'p',
		    styleWithCSS: true,
		    actions: [
		     
		    ],
		    classes: {
		      actionbar: 'pell-actionbar-custom-name',
		      button: 'pell-button-custom-name',
		      content: 'writing-area',
		      selected: 'pell-button-selected-custom-name'
		    }
		  });		  
		    editor.content.innerHTML = el.parentNode.querySelector('.pell-editor-output').innerHTML;		    
		});
	}
}
async function getCurrentOrders(url) {
		console.log('Fetch orders in progress...');
		// il faut envoyer xsrf token

		let xsrfToken = localStorage.getItem('xsrfToken');
		
		// inclure token csrf
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
			url,
			{ method: 'GET', mode: 'cors', credentials: 'include', headers}
		);
		if(!res.ok) return false;
		const json = await res.json();
		return json;			
}

async function getOrdersByYear(url) {
		console.log('Fetch orders...');
		// il faut envoyer xsrf token

		let xsrfToken = localStorage.getItem('xsrfToken');
		
		// inclure token csrf
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
			url,
			{ method: 'GET', mode: 'cors', credentials: 'include', headers}
		);
		if(!res.ok) return false;
		const json = await res.json();
		return json;			
}
async function getOrdersToValidate(url) {
		console.log('Fetch orders to validate...');
		// il faut envoyer xsrf token

		let xsrfToken = localStorage.getItem('xsrfToken');
		
		// inclure token csrf
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
			url,
			{ method: 'GET', mode: 'cors', credentials: 'include', headers}
		);
		if(!res.ok) return false;
		const json = await res.json();
		return json;			
}