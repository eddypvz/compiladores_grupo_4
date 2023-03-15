
$(document).ready(function () {

    function showAlert(msg) {
        Toastify({
            text: msg,
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: "top", // `top` or `bottom`
            position: "center", // `left`, `center` or `right`
            stopOnFocus: true, // Prevents dismissing of toast on hover
            onClick: function(){} // Callback after click
        }).showToast();
    }

    // Cuando seleccione un archivo a subir
    $("#file-upload").on("change", function (e) {

        const fd = new FormData();
        const files = $(this)[0].files[0];
        fd.append('file', files);

        $.ajax({
            url: 'process.php?operation=fileUpload', // envío el parámetro operation para identificar la operación fileUpload
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response) {
                $("#resourceCode").html(response);
                showAlert('Archivo cargado');
            },
            error: function () {
                showAlert('Error al cargar archivo, por favor intente de nuevo');
            }
        });
    });

})
