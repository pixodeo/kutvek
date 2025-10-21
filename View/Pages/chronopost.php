
<form method="post" data-ctrl="delivery.relays">
    <h4>Choisir un point relais Chronopost</h4>
    <p>Rentrez votre adresse pour obtenir les points relais les plus proches.</p>
  
    <p>
      <label for="adresse">Adresse</label><input type="text" name="adresse" id="adresse"/>
       <label for="cp">Code postal</label><input type="text" name="cp" id="cp"/>
       <label for="ville">Ville</label><input type="text" name="ville" id="ville"/>
       <button type="submit">Rechercher</button>
    </p>
  
       
      
  
  </form>
<div id="relay-map" style="height:64rem;">
</div>