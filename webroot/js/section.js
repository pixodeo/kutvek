window.addEventListener("DOMContentLoaded", function(e) {
	const url = new URL(window.location.href); 	
	const inputs = [];
    for (var p of url.searchParams) {        
        const ids = p[1].split(',');
        const map = ids.map((x) => `#${p[0]}-${x}`);        
        inputs.push(...map);        
    }
    if(inputs.length > 0){
    	inputs.forEach((id)=> {
            console.log(id);
    		const input = document.querySelector(id);
    		if(input && input.type == 'checkbox') input.checked = true;
    	});
    }    	
});