function myFunction(subjectId) {

    const options = {
        method: 'POST',
        headers: {
            "Content-Type": "application/json"
        }
        // 'Content-Type': 'application/x-www-form-urlencoded'
    }

    fetch('/subject/delete', options)
        .then(response => {
            console.log(response);
            console.log('la reponse est : ' + response.ok)
            console.log('le status est : ' + response.status)
            console.log('le statusText est : ' + response.statusText)
            console.log('le Text est : ' + response.responseText)
            if (response.ok) {
                console.log('Tout ce passe bien')
            } else {
                console.log('Erreur : ' + response.statusText)
            }
        })
        .then(data => console.log(data));


    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {

            strOutput = this.responseText;
            console.log(strOutput);
        }
    };

    xhttp.open("POST", "/subject/delete", true);
    xhttp.send();
}