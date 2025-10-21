class ShoppingCart extends HTMLElement {
  _store = JSON.parse(localStorage.getItem('cart'));
  constructor() {
    super();    
    this._url = decodeURIComponent(window.atob(document.querySelector('#shopping-cart').getAttribute("data-obf")));
    this.shadow = this.attachShadow({ mode: "open" });
    this.shadow.innerHTML = `
      <style>
        #cart-preview {
          position: fixed;
          top: -200vh;
          left:0;
          right:0;
          bottom:0;
          /*width: 100vw;*/
          max-height: 100vh;
          transition: top .4s ease;
          overflow: hidden;
          overflow-y: scroll;
          z-index: 101;
          background-color: #ffffff;
          padding: 1.6rem 1.6rem;
        }
        .item img {max-width: 12rem; margin-right: .4rem;} 
        #cart-preview.visible {top: 0;}
        @media only screen and (min-width:1024px){
          #cart-preview {padding: 3.2rem 5.6rem 0;}    
        }
    </style>`;

    /*const cart = this.shadow.querySelector(".shopping-cart");

    cart.addEventListener("click", () => {
      cart.textContent = "🌸🌹🌻 (voilà des fleurs)";
    });*/
  };
  async connectedCallback (){
      const url = this._store !== null ? this._url.replace(':id', this._store.id) : this._url.replace(':id', 0); 
      const req =  await fetch(url, {method: 'GET', mode: 'cors', credentials: 'include'});
      if(req.ok){
        const text = await req.text();
        const cart = document.createRange().createContextualFragment(text);
        this.shadow.appendChild(cart);
        this.shadow.querySelector('aside').classList.add('visible');
      }
  };
}

customElements.define('shopping-cart', ShoppingCart);
document.querySelector('#shopping-cart').addEventListener('click', e => {
  if(document.body.querySelector('shopping-cart') !== null ) {
    alert('exists !');
    document.querySelector('shopping-cart').remove();
    return;    
  }
  document.body.appendChild(new ShoppingCart);

}, false);