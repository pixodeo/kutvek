window.addEventListener("DOMContentLoaded", function (e) {
  dataLayer.push({ ecommerce: null });
  const id = document.querySelector('article').getAttribute('data-product');
  const price = parseFloat(document.getElementById('item-total').value);
  const data = {
    event: "view_item",
    ecommerce: {
      currency: document.querySelector('main').getAttribute('data-cur'),
      value: price.toFixed(2),
      items: [
      {
        item_id: `I_${id}`,
        item_name: document.getElementById('designation').textContent,        
        price: price.toFixed(2),
        quantity: 1
      }
      ]
    }
  };
  dataLayer.push(data); 
});