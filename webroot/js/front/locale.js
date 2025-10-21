const locale = {
    _elem: null,
    _cookies: {},
    _ev: null,
    _store: null,
    setEvent: function (event) {
        this._ev = event;
    },
    setElem: function (elem) {
        this._elem = elem;
    },
    countryCurrency: async function () {
        const country = this._elem;
        const countryId = country.getAttribute('data-country');
        const currencyId = country.getAttribute('data-currency');
        const url = this._elem.parentNode.getAttribute('data-url');
        const formData = new FormData();

        formData.append('country', countryId);
        formData.append('currency', currencyId);

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });

        if (response.ok && response.status === 200) {
            localStorage.setItem('currency', '1');
            document.location.reload();
        }
    },
    countrySearch: async function () {
        if (this._elem.value.length < 1) return;

        let uri = this._elem.getAttribute('data-uri');
        const url = new URL(uri);
        const params = new URLSearchParams();
        let newUrl;
        params.append('search', this._elem.value);
        newUrl = url.pathname + '?' + params.toString();

        const res = await fetch(newUrl, { 
            method: 'GET', 
            mode: 'cors', 
            credentials: 'include' 
        });
        
        if (!res.ok) {
            const json = await res.json();
            return;
        } else {
            const countryList = document.getElementById('country-list');
            const text = await res.text();
            countryList.innerHTML = text;
        }
    }
}
