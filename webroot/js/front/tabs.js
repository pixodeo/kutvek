/**
	Au clic sur un onglet
on rajoute class active sur l'onglet appele	On retire class active sur l'onglet et le contenu actuellement actifs
*/
// Récupérer le fragment et le lien qui correspond dans l'url demandée pour afficher le contenu en rapport


var scrollToE = function(to, duration) {
    var
    element = document.body || document.documentElement,
    start = element.scrollTop,    
    change = to - start,
    startDate = +new Date(),
    // t = current time
    // b = start value
    // c = change in value
    // d = duration
    easeInOutQuad = function(t, b, c, d) {
        t /= d/2;
        if (t < 1) return c/2*t*t + b;
        t--;
        return -c/2 * (t*(t-2) - 1) + b;
    },
    animateScroll = function() {
        var currentDate = +new Date();
        var currentTime = currentDate - startDate;
        element.scrollTop = parseInt(easeInOutQuad(currentTime, start, change, duration));
        if(currentTime < duration) {
            requestAnimationFrame(animateScroll);
        }
        else {

            element.scrollTop = to;
        }
    };
    animateScroll();
}

var display_tab = function(a,event) {
	
	event.preventDefault();	

	// On récupère l'élément parent div > ul > li > a
	var div = a.parentNode.parentNode.parentNode;
	//console.log(div);

	// l'élémént li > a dans l'ul sur lequel on a .active
	var li = a.parentNode;

	// l'elem parent (li) a déjà la class active
	if(li.classList.contains('active') ||li.classList.contains('disabled')) return false;		
	var elem = div.querySelector('.tabs .active');
	elem.classList.remove('active');
	li.classList.add('active');

	// on retire .active sur le contenu tab_content .active
	var tab_content = div.querySelector('.tab_content.active');

	// on ajoute la class active sur le tab_content en rapport avec l'elem cliqué, sélection par son id qui correspond au href du lien
	// ! elem.href renvoi le lien absolu, utiliser getAttribute('href');
	var tab_content_to_active =  div.querySelector(a.getAttribute('href'));
	tab_content.classList.remove('active');	
	tab_content_to_active.classList.add('active');

	//window.location.hash = a.getAttribute('href');
}

window.addEventListener("DOMContentLoaded", function(e) 
{
	/*if(document.querySelector('a.scrollTop')) {
		var linkToScroll = document.querySelector('a.scrollTop');
		linkToScroll.addEventListener('click', function(e){	
			e.preventDefault();
			var to = linkToScroll.scrollTop;
			var vers = document.body || document.documentElement;
			var verSc = vers.scrollTop;	
			scrollToE(to, 900);
		},false);	
	}

	var hash = window.location.hash;
	if(hash) {		
		var a = document.querySelector('a[href="' + hash + '"]');
		if(a !== null && !a.parentNode.classList.contains('active')) {	
			display_tab(a, e);	
		}
		var id = hash.replace(/#/i, '');
		var elmnt = document.getElementById(id);
		scrollToE(elmnt.offsetTop, 900);
	}
	
	if(document.querySelector('a.reviews')) {
		var linkToReviews = document.querySelector('a.reviews');
		linkToReviews.addEventListener('click', function(e){
			e.preventDefault();
			let hash = this.getAttribute('data-href');
			var a = document.querySelector('a[href="#' + hash + '"]');
			display_tab(a,e);
			var elmnt = document.getElementById(hash);
			var oTop = elmnt.offsetTop;
			console.log(oTop);
			scrollToE(elmnt.offsetTop, 900);		
		},false);
	}*/

	var tabs = document.querySelectorAll('.tabs a');
	if(tabs.length ===  0)return;
	for (var i = 0; i < tabs.length; i++) {
		tabs[i].addEventListener('click', function(e) {	
			
			display_tab(this, e);		
		});
	}	
});