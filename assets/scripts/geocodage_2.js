const form = document.querySelector("#eventForm");
if (form) {
    const streetField = document.querySelector("#event_address_street");
    // const url = `https://data.geopf.fr/geocodage/completion/?text=${encodeURIComponent(streetField.value)}&postcode=${encodeURIComponent(postcode.value)}&city=${encodeURIComponent(city.value)}`;
    const url = "https://data.geopf.fr/geocodage/completion/?text=";
    const addressesSuggestions = document.querySelector("#addresses-suggestions");

    streetField.addEventListener("input", async () => {
        const streetFieldValue = streetField.value.trim();
        if (streetFieldValue.length >= 3 && streetFieldValue.length <= 200) {
            console.log(streetFieldValue);
            timeoutId = setTimeout(async () => {
                await findAddresses(streetFieldValue);
            }, 300);
        } else {
            console.log("mauvaise longueur")
        }
    })

    async function findAddresses(street) {
        const url = "https://data.geopf.fr/geocodage/completion/?text=";
        try {
            const response = await fetch(url + encodeURIComponent(street));
            if (!response.ok) {
                console.log("response not ok");
                return;
            }
            const datas = await response.json();
            console.log(datas);
            if (datas.results && datas.results.length > 0) {
                addressesSuggestions.innerHTML = "";
                datas.results.map(result => addressesSuggestions.innerHTML += `<li>${result.fulltext}</li>`);
            }
        } catch (e) {
            console.error(e);
        }
    }

} 
