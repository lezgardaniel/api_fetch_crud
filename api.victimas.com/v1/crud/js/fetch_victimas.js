/* **********     APIs REST: CRUD con Fetch     ********** */

const d = document,
    $table = d.querySelector(".crud-table"),
    $form = d.querySelector(".crud-form"),
    $title = d.querySelector(".crud-title"),
    $template = d.getElementById("crud-template").content,
    $fragment = d.createDocumentFragment();

/* ********** Obtener victimas - GET ********** */
const getAll = async () => {
    try {
        let res = await fetch("http://localhost/api.victimas.com/v1/victimas"),
            json = await res.json();

        if (!res.ok) throw {
            status: res.status,
            statusText: res.statusText
        };

        console.log(json);
        json.forEach(el => {
            $template.querySelector(".idVictima").textContent = el.idVictima;
            $template.querySelector(".primerNombre").textContent = el.primerNombre;
            $template.querySelector(".primerApellido").textContent = el.primerApellido;
            $template.querySelector(".edad").textContent = el.edad;
            $template.querySelector(".genero").textContent = el.genero;
            $template.querySelector(".telefono").textContent = el.telefono;
            $template.querySelector(".idUsuario").textContent = el.idUsuario;

            $template.querySelector(".edit").dataset.idVictima = el.idVictima;
            $template.querySelector(".edit").dataset.primerNombre = el.primerNombre;
            $template.querySelector(".edit").dataset.primerApellido = el.primerApellido;
            $template.querySelector(".edit").dataset.edad = el.edad;
            $template.querySelector(".edit").dataset.genero = el.genero;
            $template.querySelector(".edit").dataset.telefono = el.telefono;
            $template.querySelector(".edit").dataset.idUsuario = el.idUsuario;

            $template.querySelector(".delete").dataset.idVictima = el.idVictima;

            let $clone = d.importNode($template, true);
            $fragment.appendChild($clone);
        });

        $table.querySelector("tbody").appendChild($fragment);
    } catch (err) {
        let message = err.statusText || "Ocurrió un error";
        $table.insertAdjacentHTML("afterend", `<p><b>Error ${err.status}: ${message}</b></p>`);
    }
}

d.addEventListener("DOMContentLoaded", getAll);

d.addEventListener("submit", async e => {
    if (e.target === $form) {
        e.preventDefault();

        if (!e.target.idVictima.value) {
            /* ********** Create - POST ********** */
            try {
                let claveApi = document.getElementById("claveApi").value; 
                let options = {
                    method: "POST",
                    headers: {
                        "Content-type": "application/json; charset=utf-8",
                        "Authorization": `${claveApi}`
                    },
                    body: JSON.stringify({
                        primerNombre: e.target.primerNombre.value,
                        primerApellido: e.target.primerApellido.value,
                        edad: e.target.edad.value,
                        genero: e.target.genero.value,
                        telefono: e.target.telefono.value,
                        idUsuario: e.target.idUsuario.value
                    })
                },
                    res = await fetch("http://localhost/api.victimas.com/v1/victimas", options),
                    json = await res.json();

                if (!res.ok) throw {
                    status: res.status,
                    statusText: res.statusText
                };

                location.reload();
            } catch (err) {
                let message = err.statusText || "Ocurrió un error";
                $form.insertAdjacentHTML("afterend", `<p><b>Error ${err.status}: ${message}</b></p>`);
            }
        } else {
            /* ********** Update - PUT ********** */
            try {
                let claveApi = document.getElementById("claveApi").value; 
                let options = {
                    method: "PUT",
                    headers: {
                        "Content-type": "application/json; charset=utf-8",
                        "Authorization": `${claveApi}`
                    },
                    body: JSON.stringify({
                        primerNombre: e.target.primerNombre.value,
                        primerApellido: e.target.primerApellido.value,
                        edad: e.target.edad.value,
                        genero: e.target.genero.value,
                        telefono: e.target.telefono.value,
                        idUsuario: e.target.idUsuario.value
                    })
                },
                    res = await fetch(`http://localhost/api.victimas.com/v1/victimas/${e.target.idVictima.value}`, options),
                    json = await res.json();

                if (!res.ok) throw {
                    status: res.status,
                    statusText: res.statusText
                };

                location.reload();
            } catch (err) {
                let message = err.statusText || "Ocurrió un error";
                $form.insertAdjacentHTML("afterend", `<p><b>Error ${err.status}: ${message}</b></p>`);
            }
        }
    }
});

d.addEventListener("click", async e => {
    if (e.target.matches(".edit")) {
        $title.textContent = "Editar Victima";

        $form.primerNombre.value = e.target.dataset.primerNombre;
        $form.primerApellido.value = e.target.dataset.primerApellido;
        $form.edad.value = e.target.dataset.edad;
        $form.genero.value = e.target.dataset.genero;
        $form.telefono.value = e.target.dataset.telefono;
        $form.idUsuario.value = e.target.dataset.idUsuario;
        $form.idVictima.value = e.target.dataset.idVictima;
    }

    if (e.target.matches(".delete")) {
        let clave = prompt('Ingresa la clave API del usuario');
        let isDelete = confirm(`¿Estás seguro de eliminar la victima numero ${e.target.dataset.idVictima}?`);

        if (isDelete) {
            /* ********** Delete - DELETE ********** */
            try {
                let options = {
                    method: "DELETE",
                    headers: {
                        "Content-type": "application/json; charset=utf-8",
                        "Authorization": `${clave}`
                    }
                },
                    res = await fetch(`http://localhost/api.victimas.com/v1/victimas/${e.target.dataset.idVictima}`, options),
                    json = await res.json();

                if (!res.ok) throw {
                    status: res.status,
                    statusText: res.statusText
                };

                location.reload();
            } catch (err) {
                let message = err.statusText || "Ocurrió un error";
                alert(`Error ${err.status}: ${message}`);
            }
        }
    }
})