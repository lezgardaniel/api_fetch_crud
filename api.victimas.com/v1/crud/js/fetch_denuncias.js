/* **********       Código Javascript     ********** */
/* **********     APIs REST: CRUD con Fetch     ********** */

const d = document,
    $table = d.querySelector(".crud-table"),
    $form = d.querySelector(".crud-form"),
    $title = d.querySelector(".crud-title"),
    $template = d.getElementById("crud-template").content,
    $fragment = d.createDocumentFragment();

/* ********** Obtener denuncias - GET ********** */
const getAll = async () => {
    try {
        let res = await fetch("http://localhost/api.victimas.com/v1/denuncias"),
            json = await res.json();

        if (!res.ok) throw {
            status: res.status,
            statusText: res.statusText
        };

        console.log(json);
        json.forEach(el => {
            $template.querySelector(".idDenuncia").textContent = el.idDenuncia;
            $template.querySelector(".hechos").textContent = el.hechos;
            $template.querySelector(".lugar").textContent = el.lugar;
            $template.querySelector(".fecha").textContent = el.fecha;
            $template.querySelector(".responsable").textContent = el.responsable;
            $template.querySelector(".idVictima").textContent = el.idVictima;

            $template.querySelector(".edit").dataset.idDenuncia = el.idDenuncia;
            $template.querySelector(".edit").dataset.hechos = el.hechos;
            $template.querySelector(".edit").dataset.lugar = el.lugar;
            $template.querySelector(".edit").dataset.fecha = el.fecha;
            $template.querySelector(".edit").dataset.responsable = el.responsable;
            $template.querySelector(".edit").dataset.idVictima = el.idVictima;

            $template.querySelector(".delete").dataset.idDenuncia = el.idDenuncia;

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

        if (!e.target.idDenuncia.value) {
            /* ********** Create - POST ********** */
            try {
                let options = {
                    method: "POST",
                    headers: {
                        "Content-type": "application/json; charset=utf-8"
                    },
                    body: JSON.stringify({
                        hechos: e.target.hechos.value,
                        lugar: e.target.lugar.value,
                        fecha: e.target.fecha.value,
                        responsable: e.target.responsable.value,
                        idVictima: e.target.idVictima.value
                    })
                },
                    res = await fetch("http://localhost/api.victimas.com/v1/denuncias", options),
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
                let options = {
                    method: "PUT",
                    headers: {
                        "Content-type": "application/json; charset=utf-8"
                    },
                    body: JSON.stringify({
                        hechos: e.target.hechos.value,
                        lugar: e.target.lugar.value,
                        fecha: e.target.fecha.value,
                        responsable: e.target.responsable.value,
                        idVictima: e.target.idVictima.value
                    })
                },
                    res = await fetch(`http://localhost/api.victimas.com/v1/denuncias/${e.target.idDenuncia.value}`, options),
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
        $title.textContent = "Editar Denuncia";

        $form.hechos.value = e.target.dataset.hechos;
        $form.lugar.value = e.target.dataset.lugar;
        $form.fecha.value = e.target.dataset.fecha;
        $form.responsable.value = e.target.dataset.responsable;
        $form.idVictima.value = e.target.dataset.idVictima;
        $form.idDenuncia.value = e.target.dataset.idDenuncia;
    }

    if (e.target.matches(".delete")) {
        let isDelete = confirm(`¿Estás seguro de eliminar la denuncia numero ${e.target.dataset.idDenuncia}?`);

        if (isDelete) {
            /* ********** Delete - DELETE ********** */
            try {
                let options = {
                    method: "DELETE",
                    headers: {
                        "Content-type": "application/json; charset=utf-8"
                    }
                },
                    res = await fetch(`http://localhost/api.victimas.com/v1/denuncias/${e.target.dataset.idDenuncia}`, options),
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