document.addEventListener("turbo:load", () => {
    console.log("GéoApi");
    const form = document.querySelector("#eventForm");
    if (form) {
        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const street = document.querySelector("#event_address_street");
            const city = document.querySelector("#event_address_city");
            const postcode = document.querySelector("#event_address_postcode");
            const longitude = document.querySelector("#event_address_longitude");
            const latitude = document.querySelector("#event_address_latitude");

            if (street && postcode && city) {
                try {
                    const response = await fetch(`https://data.geopf.fr/geocodage/search?q=${street.value}&postcode=${postcode.value}&city=${city.value}`);
                    const datas = await response.json();
                    if (!datas.features || datas.features.length === 0) {
                        alert("Aucune adresse trouvée !");
                        return;
                    } else {
                        const coordinates = [...datas.features[0].geometry.coordinates]; //longitude puis latitude
                        longitude.value = coordinates[0];
                        latitude.value = coordinates[1];
                    }
                } catch (error) {
                    console.error(error);
                }
            } else {
                alert("Certains champs d'adresse sont manquants !");
                return;
            }

            form.submit();
        })
    }
})