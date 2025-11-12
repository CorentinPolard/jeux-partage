const form = document.querySelector("#eventForm");
if (form) {
    const streetField = document.querySelector("#event_address_street");
    const cityField = document.querySelector("#event_address_city");
    const postcodeField = document.querySelector("#event_address_postcode");

    const longitudeField = document.querySelector("#event_address_longitude");
    const latitudeField = document.querySelector("#event_address_latitude");

    const completion_url = "https://data.geopf.fr/geocodage/completion/?text=";
    const addressesSuggestions = document.querySelector("#addresses-suggestions");

    let timeoutId;

    streetField.addEventListener("input", async () => {
        const streetFieldValue = streetField.value.trim();
        if (streetFieldValue.length >= 3 && streetFieldValue.length <= 200) {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(async () => {
                await findAddresses(streetFieldValue);
            }, 300);
        }
    })

    // Search addresses corresponding
    // And shox the user the suggestion
    async function findAddresses(street) {
        const url = "https://data.geopf.fr/geocodage/completion/?text=";
        try {
            const response = await fetch(completion_url + encodeURIComponent(street));
            if (!response.ok) {
                console.error("API response error:", response.status);
                addressesSuggestions.innerHTML = "";
                return;
            }
            const datas = await response.json();
            if (datas.results && datas.results.length > 0) {
                addressesSuggestions.innerHTML = "";
                datas.results.map(result => {
                    const li = document.createElement("li");
                    li.className = "address-suggestion";
                    li.dataset.name = "address-suggestion";
                    li.dataset.longitude = result.x;
                    li.dataset.latitude = result.y;
                    li.textContent = result.fulltext;
                    addressesSuggestions.appendChild(li);
                });
                initAdrressValidation();
                addressesSuggestions.classList.remove("hidden");
            }
        } catch (e) {
            console.error("Geocoding error:", e);
            addressesSuggestions.innerHTML = "";
        }
    }

    // For each addresses :
    // I add an click event listener
    // which fill the address fields and hide the suggestion list
    function initAdrressValidation() {
        const addresses = document.querySelectorAll("li[data-name=address-suggestion]");
        addresses.forEach((address) => {
            address.addEventListener("click", (e) => {
                const fullAddress = e.target.innerText.split(",");

                streetField.value = fullAddress[0];
                postcodeField.value = fullAddress[1].substring(0, 6);
                cityField.value = fullAddress[1].substring(6);
                longitudeField.value = e.target.dataset.longitude;
                latitudeField.value = e.target.dataset.latitude;

                addressesSuggestions.classList.add("hidden");
            })
        })
    }

    // On submit : 
    // Check if there latitude and longitudes fields are filled
    // Complete in needs
    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const streetValue = streetField.value.trim();
        const postcodeValue = postcodeField.value.trim();
        const cityValue = cityField.value.trim();
        const latitudeValue = latitudeField.value.trim();

        if (!streetValue || !postcodeValue || !cityValue) {
            alert("Certains champs d'adresse sont manquants !");
            return;
        }

        // Check if coordinates are already set and valid
        if (latitudeValue && !isNaN(parseFloat(latitudeField.value))) {
            form.submit();
            return;
        }

        // Try to geocode if coordinates are missing
        try {
            const search_url = `https://data.geopf.fr/geocodage/search?index=address,poi&q=${encodeURIComponent(streetValue)}&postcode=${encodeURIComponent(postcodeValue)}&city=${encodeURIComponent(cityValue)}`;
            const response = await fetch(search_url);

            if (!response.ok) {
                alert("Erreur liée à l'adresse lors de la soumission du formulaire.");
                return;
            }

            const datas = await response.json();

            if (!datas.features || datas.features.length === 0) {
                alert("Aucune adresse trouvée !");
                return;
            }

            // Validate and extract coordinates
            const geometry = datas.features[0].geometry;
            if (!geometry || !geometry.coordinates || geometry.coordinates.length < 2) {
                alert("Coordonnées invalides reçues de l'API.");
                return;
            }

            const [longitude, latitude] = geometry.coordinates;

            // Validate coordinates are valid numbers
            if (isNaN(longitude) || isNaN(latitude)) {
                alert("Coordonnées invalides reçues de l'API.");
                return;
            }

            longitudeField.value = longitude;
            latitudeField.value = latitude;

            form.submit();
        } catch (error) {
            console.error("Geocoding error during form submission:", error);
            alert("Une erreur s'est produite lors de la géocodification de l'adresse.");
        }
    })

} 
