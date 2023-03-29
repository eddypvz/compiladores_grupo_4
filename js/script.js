
// Acá carga el documento (cuando la página carga)
$(document).ready(function () {

    function showAlert(msg) {
        Toastify({
            text: msg,
            duration: 3000,
            newWindow: true,
            close: true,
            gravity: "top", // `top` or `bottom`
            position: "right", // `left`, `center` or `right`
            stopOnFocus: true, // Prevents dismissing of toast on hover
            onClick: function(){} // Callback after click
        }).showToast();
    }

    // Botones
    $("#analisisLexicoBtn").click(function () {

        const codigo = $("#resourceCode").val();
        console.log(codigo);

        $.ajax({
            url: 'process.php?operation=analisisLexico',
            method: 'post',
            data: {
                codigo: codigo
            },
            success: function(response) {
                $("#analisisCode").html(response); // disparo el trigger para cambio
                showAlert('Análisis léxico realizado');
            },
            error: function () {
                showAlert('Error al realizar análisis, por favor intente de nuevo');
            }
        });
    });

    $("#analisisSintacticoBtn").click(function () {

        const codigo = $("#resourceCode").val();

        $.ajax({
            url: 'process.php?operation=analisisSintactico',
            method: 'post',
            data: {
                codigo: codigo
            },
            success: function(response) {
                $("#analisisCode").html(response); // disparo el trigger para cambio
                showAlert('Análisis sintáctico realizado');
            },
            error: function () {
                showAlert('Error al realizar análisis, por favor intente de nuevo');
            }
        });
    });

    // Cuando seleccione un archivo a subir
    $("#file-upload").on("change", function (e) {

        const fd = new FormData();
        const files = $(this)[0].files[0];
        fd.append('file', files);

        const resourceCode = $("#resourceCode");
        resourceCode.val(''); // borro el contenido del html para reemplazarlo por el contenido que trae

        $.ajax({
            url: 'process.php?operation=fileUpload', // envío el parámetro operation para identificar la operación fileUpload
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response) {
                resourceCode.val(response).trigger('change'); // disparo el trigger para cambio
                showAlert('Archivo cargado');
            },
            error: function () {
                showAlert('Error al cargar archivo, por favor intente de nuevo');
            }
        });
    });

    function calculateLines(idEditor) {
        // obtengo los objetos con jquery
        const editor = $('#' + idEditor);
        const textarea = editor.find('textarea'); // busco el textarea
        const lineNumbers = editor.find('.line-numbers'); // busco el span de lineas

        // esta función corre siempre que hay un evento keyup o change, también al iniciar la página para que cargue los números automáticamente
        const addLines = function () {
            // traigo el contenido separado por salto de linea \n y luego cuento cuántas lineas me dio.
            const numberOfLines = textarea.val().split('\n').length;

            // Arreglo de numeros de lineas
            const data = Array(numberOfLines)
                .fill('<span></span>')
                .join('');
            lineNumbers.html(data);
        }

        // evento keyup
        textarea.on('keyup', event => {
            addLines();
        });

        // evento change
        textarea.on('change', event => {
            addLines();
        });

        // corro las lineas
        addLines();
    }

    // Habilito las lineas para ambos editores
    calculateLines('editorOne');

    // voy a cargar un código de demo al iniciar la página, así no tengo que andar cargando nada a mano
    $.ajax({
        url: 'process.php?operation=loadDemo', // envío el parámetro operation para identificar la operación fileUpload
        method: 'post',
        success: function(response) {
            $("#resourceCode").val(response).trigger('change'); // disparo el trigger para cambio
            $("#analisisLexicoBtn").trigger('click');
            showAlert('Archivo demo cargado');
        },
        error: function () {
            showAlert('Error al cargar demo, por favor intente de nuevo');
        }
    });
})
