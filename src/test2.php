<!DOCTYPE html>
<html>
<head>
    <title>Mapa de Rotas - Itapetininga</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    
    <style>
        /* IMPORTANTE: Sem isso o mapa não aparece */
        #map { 
            height: 500px; 
            width: 100%; 
            border: 2px solid #ccc;
        }
        #instructions { margin: 20px; font-family: sans-serif; }
        .loading { color: orange; font-weight: bold; }
    </style>
</head>
<body>

    <div id="instructions">Carregando endereços...</div>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        const map = L.map('map').setView([-23.5916, -48.0530], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const azulIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        // TESTE COM APENAS 1 ENDEREÇO
        const enderecoTeste = "Rua Monsenhor Soares, Itapetininga, SP";
        const geocoder = L.Control.Geocoder.nominatim();

        // Fazemos apenas UMA chamada
        geocoder.geocode(enderecoTeste, function(results) {
            if (results.length > 0) {
                const ponto = results[0].center;
                
                // Adiciona um marcador simples para testar a resposta
                L.marker(ponto, { icon: azulIcon })
                    .addTo(map)
                    .bindPopup(`<b>Sucesso!</b><br>${enderecoTeste}`)
                    .openPopup();

                // Centraliza o mapa no ponto encontrado
                map.setView(ponto, 16);
                
                document.getElementById('instructions').innerHTML = "Endereço encontrado com sucesso!";
            } else {
                document.getElementById('instructions').innerHTML = "O serviço não encontrou o endereço.";
            }
        });
    </script>
</body>
</html>