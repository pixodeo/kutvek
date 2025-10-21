

const pellEditor = function(){
  console.log('pell init new editors');
  const editors = document.querySelectorAll('.pell-editor');
editors.forEach(function(el,idx)
{  
  let editor = pell.init({
    element: el,
    onChange: html => {
      console.log(el.parentNode);
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