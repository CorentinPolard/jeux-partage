const form = document.querySelector("#eventForm");
if (form) {
    const streetField = document.querySelector("#event_address_street");
    const cityField = document.querySelector("#event_address_city");
    const postcodeField = document.querySelector("#event_address_postcode");

    const longitudeField = document.querySelector("#event_address_longitude");
    const latitudeField = document.querySelector("#event_address_latitude");

    const url = "https://data.geopf.fr/geocodage/completion/?text=";
    const addressesSuggestions = document.querySelector("#addresses-suggestions");

    streetField.addEventListener("input", async () => {
        const streetFieldValue = streetField.value.trim();
        if (streetFieldValue.length >= 3 && streetFieldValue.length <= 200) {
            console.log(streetFieldValue);
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
            const response = await fetch(url + encodeURIComponent(street));
            if (!response.ok) {
                console.log("response not ok");
                return;
            }
            const datas = await response.json();
            if (datas.results && datas.results.length > 0) {
                addressesSuggestions.innerHTML = "";
                datas.results.map(result => addressesSuggestions.innerHTML += `<li id="address-suggestion" class="address-suggestion" data-longitude="${result.x}" data-latitude="${result.y}">${result.fulltext}</li>`);
                initAdrressValidation();
                addressesSuggestions.classList.remove("hidden");
            }
        } catch (e) {
            console.error(e);
        }
    }

    // For each addresses :
    // I add an click event listener
    // which fill the address fields and hide the suggestion list
    function initAdrressValidation() {
        const addresses = document.querySelectorAll("#address-suggestion");
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

        if (streetField.value.trim() == "" || postcodeField.value.trim() == "" || cityField.value.trim() == "") {
            alert("Certains champs d'adresse sont manquants !");
            return;
        } else if (latitudeField.value.trim() == "") {
            console.log("try")
            try {
                const url = `https://data.geopf.fr/geocodage/search?index=address,poi&q=${encodeURIComponent(streetField.value)}&postcode=${encodeURIComponent(postcodeField.value)}&city=${encodeURIComponent(cityField.value)}`;
                const response = await fetch(url);
                if (!response.ok) {
                    alert("Erreur liée à l'adresse lors de la soumission du formulaire.");
                    return;
                }
                const datas = await response.json();
                if (!datas.features || datas.features.length === 0) {
                    alert("Aucune adresse trouvée !");
                    return;
                }
                const coordinates = [...datas.features[0].geometry.coordinates]; //longitude puis latitude
                longitudeField.value = coordinates[0];
                latitudeField.value = coordinates[1];
            } catch (error) {
                console.error(error);
            }
        }

        form.submit();
    })

} 
