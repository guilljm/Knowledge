function myFunction(route, id) {

    let isConfirm = confirm("Confirmez-vous la suppression ?");
        
    let xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            routeReturn = this.responseText;
            // console.log(routeReturn);
            window.location.href = routeReturn;
            return;
        }
    };

    if (isConfirm) {
        response = "response=" + id;
        xhttp.open("POST", route, true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(response);
    }

}