// App JavaScript Core - AgendaAI
$(document).ready(function () {

    // 1. Alternador de Tema Claro / Escuro com persistência em localStorage
    const savedTheme = localStorage.getItem('agendaai_theme') || 'light';
    setTheme(savedTheme);

    $('#theme-toggle-btn').on('click', function () {
        const currentTheme = $('html').attr('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    function setTheme(theme) {
        $('html').attr('data-bs-theme', theme);
        localStorage.setItem('agendaai_theme', theme);
        if (theme === 'dark') {
            $('#theme-toggle-icon').removeClass('fa-moon').addClass('fa-sun');
        } else {
            $('#theme-toggle-icon').removeClass('fa-sun').addClass('fa-moon');
        }
    }

    // 1.b Alternador de Menu Sidebar para dispositivos móveis
    $('#sidebar-toggle-btn, #sidebar-close-btn, #sidebar-backdrop').on('click', function () {
        $('.sidebar').toggleClass('show');
        $('#sidebar-backdrop').toggleClass('show');
    });

    $('.sidebar-nav .nav-link').on('click', function () {
        if ($(window).width() < 992) {
            $('.sidebar').removeClass('show');
            $('#sidebar-backdrop').removeClass('show');
        }
    });

    // 2. Inicialização do DataTables em Português (Brasil)
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            pageLength: 10,
            responsive: true,
            order: []
        });
    }

    // 3. Inicialização do Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // 4. Inicialização de DatePicker (Flatpickr)
    if (window.flatpickr) {
        flatpickr.setDefaults({
            locale: 'pt'
        });

        $('.datetimepicker').flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            altInput: true,
            altFormat: "d/m/Y H:i",
            time_24hr: true
        });

        $('.datepicker').flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y"
        });
    }

    // 5. Formatação de Campos com jQueryMask
    if ($.fn.mask) {
        $('.mask-patrimonio').mask('PAT-0000-000');
        $('.mask-data').mask('00/00/0000');
        $('.mask-hora').mask('00:00');
        $('.mask-telefone').mask('(00) 00000-0000');
    }
});

// Helper de envio AJAX com Bootbox
function sendAjaxRequest(url, data, callback) {
    $.ajax({
        url: url,
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                bootbox.alert({
                    message: "<strong>Sucesso!</strong> " + response.message,
                    callback: function () {
                        if (callback) callback(response);
                        else location.reload();
                    }
                });
            } else {
                bootbox.alert({
                    message: "<strong class='text-danger'>Erro!</strong> " + response.message
                });
            }
        },
        error: function (xhr) {
            let msg = 'Erro ao processar requisição.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            bootbox.alert({
                message: "<strong class='text-danger'>Erro do Servidor!</strong> " + msg
            });
        }
    });
}
